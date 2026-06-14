<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Enums\OrderStatus;
use App\Enums\UserRole;
use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\Api\V1\PlaceOrderRequest;
use App\Http\Requests\Api\V1\UpdateOrderStatusRequest;
use App\Http\Resources\OrderResource;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\StockLog;
use App\Models\User;
use App\Notifications\LowStockAlert;
use App\Notifications\OrderStatusChanged;
use Barryvdh\DomPDF\Facade\Pdf;
use Dedoc\Scramble\Attributes\QueryParameter;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

final class OrderController extends ApiController
{
    public function placeOrder(PlaceOrderRequest $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        $cartItems = $user->cartItems()->with('medicine')->get();

        if ($cartItems->isEmpty()) {
            return $this->error('Your cart is empty', 422);
        }

        foreach ($cartItems as $cartItem) {
            $medicine = $cartItem->medicine;
            if ($medicine === null) {
                continue;
            }

            if (! $medicine->is_available) {
                return $this->error(sprintf("Medicine '%s' is currently unavailable", $medicine->brand_name), 422);
            }

            if ($medicine->stock_quantity < $cartItem->quantity) {
                return $this->error(
                    sprintf("Insufficient stock for '%s'. Available: %s", $medicine->brand_name, $medicine->stock_quantity),
                    422
                );
            }
        }

        $order = DB::transaction(function () use ($user, $cartItems, $request): Order {
            $total = $cartItems->sum(fn (CartItem $item): float => $item->medicine !== null ? (float) $item->medicine->price * $item->quantity : 0.0);

            $order = Order::query()->create([
                'user_id' => $user->id,
                'status' => OrderStatus::Pending,
                'total_price' => $total,
                'notes' => $request->validated('notes'),
            ]);

            foreach ($cartItems as $cartItem) {
                if ($cartItem->medicine === null) {
                    continue;
                }

                $order->items()->create([
                    'medicine_id' => $cartItem->medicine_id,
                    'quantity' => $cartItem->quantity,
                    'unit_price' => $cartItem->medicine->price,
                ]);

                $stockBefore = $cartItem->medicine->stock_quantity;
                $cartItem->medicine->decrement('stock_quantity', $cartItem->quantity);
                $cartItem->medicine->refresh();

                StockLog::query()->create([
                    'medicine_id' => $cartItem->medicine->id,
                    'order_id' => $order->id,
                    'type' => 'sale',
                    'quantity_changed' => $cartItem->quantity,
                    'stock_before' => $stockBefore,
                    'stock_after' => $cartItem->medicine->stock_quantity,
                ]);

                /** @var int $threshold */
                $threshold = config('pharmacy.low_stock_threshold');
                if ($cartItem->medicine->stock_quantity <= $threshold && $cartItem->medicine->stock_quantity >= 0) {
                    $admins = User::query()->where('role', UserRole::Admin)->get();
                    foreach ($admins as $admin) {
                        $admin->notify(new LowStockAlert($cartItem->medicine));
                    }
                }
            }

            $user->cartItems()->delete();

            return $order;
        });

        $order->load('items.medicine');

        return $this->created(new OrderResource($order));
    }

    #[QueryParameter('page', description: 'Page number', type: 'int', default: 1, example: 1)]
    public function myOrders(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        $orders = $user->orders()->with('items.medicine')->latest()->paginate(15);

        return $this->success(OrderResource::collection($orders)->response()->getData(true));
    }

    public function showMyOrder(Request $request, Order $order): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        if ($order->user_id !== $user->id) {
            return $this->forbidden('This order does not belong to you');
        }

        $order->load('items.medicine');

        return $this->success(new OrderResource($order));
    }

    #[QueryParameter('page', description: 'Page number', type: 'int', default: 1, example: 1)]
    public function allOrders(): JsonResponse
    {
        $orders = Order::with('items.medicine')->latest()->paginate(15);

        return $this->success(OrderResource::collection($orders)->response()->getData(true));
    }

    public function showOrder(Order $order): JsonResponse
    {
        $order->load('items.medicine');

        return $this->success(new OrderResource($order));
    }

    public function confirm(UpdateOrderStatusRequest $request, Order $order): JsonResponse
    {
        if ($order->status !== OrderStatus::Pending) {
            return $this->error('Only pending orders can be confirmed', 422);
        }

        $order->update([
            'status' => OrderStatus::Confirmed,
            'admin_notes' => $request->validated('admin_notes'),
        ]);

        $order->user?->notify(new OrderStatusChanged($order, OrderStatus::Pending->value, OrderStatus::Confirmed->value));

        $order->load('items.medicine');

        return $this->success(new OrderResource($order));
    }

    public function reject(UpdateOrderStatusRequest $request, Order $order): JsonResponse
    {
        if ($order->status !== OrderStatus::Pending) {
            return $this->error('Only pending orders can be rejected', 422);
        }

        DB::transaction(function () use ($order, $request): void {
            $order->update([
                'status' => OrderStatus::Rejected,
                'admin_notes' => $request->validated('admin_notes'),
            ]);

            foreach ($order->items as $item) {
                if ($item->medicine === null) {
                    continue;
                }

                $stockBefore = $item->medicine->stock_quantity;
                $item->medicine->increment('stock_quantity', $item->quantity);
                $item->medicine->refresh();

                StockLog::query()->create([
                    'medicine_id' => $item->medicine->id,
                    'order_id' => $order->id,
                    'type' => 'restock',
                    'quantity_changed' => $item->quantity,
                    'stock_before' => $stockBefore,
                    'stock_after' => $item->medicine->stock_quantity,
                ]);
            }
        });

        $order->user?->notify(new OrderStatusChanged($order, OrderStatus::Pending->value, OrderStatus::Rejected->value));

        $order->load('items.medicine');

        return $this->success(new OrderResource($order));
    }

    public function complete(UpdateOrderStatusRequest $request, Order $order): JsonResponse
    {
        if ($order->status !== OrderStatus::Confirmed) {
            return $this->error('Only confirmed orders can be completed', 422);
        }

        $order->update([
            'status' => OrderStatus::Completed,
            'admin_notes' => $request->validated('admin_notes'),
        ]);

        $order->user?->notify(new OrderStatusChanged($order, OrderStatus::Confirmed->value, OrderStatus::Completed->value));

        $order->load('items.medicine');

        return $this->success(new OrderResource($order));
    }

    public function receipt(Request $request, Order $order): Response
    {
        /** @var User $user */
        $user = $request->user();

        abort_if(! $user->isAdmin() && $order->user_id !== $user->id, 403, 'This order does not belong to you');

        $order->load('items.medicine', 'user');

        return Pdf::loadView('receipts.order', ['order' => $order])
            ->download(sprintf('receipt-order-%d.pdf', $order->id));
    }
}
