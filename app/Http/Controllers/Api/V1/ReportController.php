<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Enums\OrderStatus;
use App\Http\Controllers\Api\ApiController;
use App\Models\Medicine;
use App\Models\Order;
use App\Models\StockLog;
use Dedoc\Scramble\Attributes\QueryParameter;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

final class ReportController extends ApiController
{
    #[QueryParameter('from', description: 'Start date for the report period (defaults to start of current month)', type: 'string', format: 'date', example: '2026-01-01')]
    #[QueryParameter('to', description: 'End date for the report period (defaults to today)', type: 'string', format: 'date', example: '2026-03-11')]
    public function sales(Request $request): JsonResponse
    {
        $from = $request->date('from') ?? now()->startOfMonth();
        $to = $request->date('to') ?? now();

        $orders = Order::query()
            ->where('status', OrderStatus::Completed)
            ->whereBetween('created_at', [$from, $to])
            ->get();

        $totalRevenue = $orders->sum(fn (Order $order): float => (float) $order->total_price);

        $topMedicines = DB::table('order_items')
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->join('medicines', 'medicines.id', '=', 'order_items.medicine_id')
            ->where('orders.status', OrderStatus::Completed->value)
            ->whereBetween('orders.created_at', [$from, $to])
            ->select(
                'medicines.id',
                'medicines.brand_name',
                DB::raw('SUM(order_items.quantity) as total_sold'),
                DB::raw('SUM(order_items.quantity * order_items.unit_price) as total_revenue'),
            )
            ->groupBy('medicines.id', 'medicines.brand_name')
            ->orderByDesc('total_sold')
            ->limit(10)
            ->get();

        return $this->success([
            'period' => [
                'from' => $from->toDateString(),
                'to' => $to->toDateString(),
            ],
            'total_orders' => $orders->count(),
            'total_revenue' => number_format($totalRevenue, 2, '.', ''),
            'top_medicines' => $topMedicines,
        ]);
    }

    public function inventory(): JsonResponse
    {
        $medicines = Medicine::query()
            ->select('id', 'brand_name', 'stock_quantity', 'is_available', 'manufacturer')
            ->orderBy('stock_quantity')
            ->get();

        $totalMedicines = $medicines->count();
        $outOfStock = $medicines->where('stock_quantity', 0)->count();
        $lowStock = $medicines->where('stock_quantity', '>', 0)
            ->where('stock_quantity', '<=', config('pharmacy.low_stock_threshold'))
            ->count();

        return $this->success([
            'total_medicines' => $totalMedicines,
            'out_of_stock' => $outOfStock,
            'low_stock' => $lowStock,
            'medicines' => $medicines,
        ]);
    }

    public function frequentlyOutOfStock(): JsonResponse
    {
        $medicines = StockLog::query()
            ->select(
                'medicine_id',
                DB::raw('COUNT(*) as out_of_stock_count'),
            )
            ->where('type', 'sale')
            ->where('stock_after', 0)
            ->groupBy('medicine_id')
            ->orderByDesc('out_of_stock_count')
            ->limit(10)
            ->with('medicine:id,brand_name,stock_quantity,manufacturer')
            ->get();

        return $this->success($medicines);
    }
}
