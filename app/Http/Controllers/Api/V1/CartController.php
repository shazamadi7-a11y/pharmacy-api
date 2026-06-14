<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\Api\V1\AddToCartRequest;
use App\Http\Requests\Api\V1\UpdateCartRequest;
use App\Http\Resources\CartItemResource;
use App\Models\CartItem;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class CartController extends ApiController
{
    public function index(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        $cartItems = $user->cartItems()->with('medicine.images')->get();

        /** @var float $total */
        $total = $cartItems->sum(fn (CartItem $item): float => $item->medicine !== null ? (float) $item->medicine->price * $item->quantity : 0.0);

        return $this->success([
            'items' => CartItemResource::collection($cartItems),
            'total' => number_format($total, 2, '.', ''),
            'items_count' => $cartItems->count(),
        ]);
    }

    public function store(AddToCartRequest $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        $cartItem = CartItem::query()->updateOrCreate([
            'user_id' => $user->id,
            'medicine_id' => $request->validated('medicine_id'),
        ], [
            'quantity' => $request->validated('quantity', 1),
        ]);

        $cartItem->load('medicine.images');

        return $this->created(new CartItemResource($cartItem));
    }

    public function update(UpdateCartRequest $request, CartItem $cartItem): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        if ($cartItem->user_id !== $user->id) {
            return $this->forbidden('This cart item does not belong to you');
        }

        $cartItem->update(['quantity' => $request->validated('quantity')]);
        $cartItem->load('medicine.images');

        return $this->success(new CartItemResource($cartItem));
    }

    public function destroy(Request $request, CartItem $cartItem): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        if ($cartItem->user_id !== $user->id) {
            return $this->forbidden('This cart item does not belong to you');
        }

        $cartItem->delete();

        return $this->success(message: 'Item removed from cart');
    }

    public function clear(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        $user->cartItems()->delete();

        return $this->success(message: 'Cart cleared successfully');
    }
}
