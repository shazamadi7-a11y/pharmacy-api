<?php

declare(strict_types=1);

use App\Enums\OrderStatus;
use App\Models\CartItem;
use App\Models\Category;
use App\Models\Medicine;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\StockLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->admin = User::factory()->admin()->create();
    $this->customer = User::factory()->create();
    $this->category = Category::factory()->create();
});

describe('Sales Report', function (): void {
    it('returns sales report for admin', function (): void {
        $medicine = Medicine::factory()->create(['category_id' => $this->category->id, 'price' => 25.00]);
        $order = Order::factory()->create([
            'user_id' => $this->customer->id,
            'status' => OrderStatus::Completed,
            'total_price' => 50.00,
        ]);
        OrderItem::factory()->create([
            'order_id' => $order->id,
            'medicine_id' => $medicine->id,
            'quantity' => 2,
            'unit_price' => 25.00,
        ]);

        $response = $this->actingAs($this->admin)
            ->getJson('/api/v1/admin/reports/sales');

        $response->assertOk()
            ->assertJsonPath('data.total_orders', 1)
            ->assertJsonPath('data.total_revenue', '50.00');
    });

    it('filters by date range', function (): void {
        Order::factory()->create([
            'user_id' => $this->customer->id,
            'status' => OrderStatus::Completed,
            'total_price' => 100.00,
            'created_at' => now()->subDays(5),
        ]);

        $response = $this->actingAs($this->admin)
            ->getJson('/api/v1/admin/reports/sales?from='.now()->subDays(10)->toDateString().'&to='.now()->toDateString());

        $response->assertOk()
            ->assertJsonPath('data.total_orders', 1);
    });

    it('denies access to non-admin', function (): void {
        $response = $this->actingAs($this->customer)
            ->getJson('/api/v1/admin/reports/sales');

        $response->assertStatus(403);
    });
});

describe('Inventory Report', function (): void {
    it('returns inventory report for admin', function (): void {
        Medicine::factory()->create(['category_id' => $this->category->id, 'stock_quantity' => 0]);
        Medicine::factory()->create(['category_id' => $this->category->id, 'stock_quantity' => 5]);
        Medicine::factory()->create(['category_id' => $this->category->id, 'stock_quantity' => 50]);

        config(['pharmacy.low_stock_threshold' => 10]);

        $response = $this->actingAs($this->admin)
            ->getJson('/api/v1/admin/reports/inventory');

        $response->assertOk()
            ->assertJsonPath('data.total_medicines', 3)
            ->assertJsonPath('data.out_of_stock', 1)
            ->assertJsonPath('data.low_stock', 1);
    });
});

describe('Frequently Out of Stock', function (): void {
    it('returns frequently out of stock medicines', function (): void {
        $medicine = Medicine::factory()->create(['category_id' => $this->category->id, 'stock_quantity' => 0]);

        StockLog::query()->create([
            'medicine_id' => $medicine->id,
            'order_id' => null,
            'type' => 'sale',
            'quantity_changed' => 5,
            'stock_before' => 5,
            'stock_after' => 0,
        ]);
        StockLog::query()->create([
            'medicine_id' => $medicine->id,
            'order_id' => null,
            'type' => 'sale',
            'quantity_changed' => 3,
            'stock_before' => 3,
            'stock_after' => 0,
        ]);

        $response = $this->actingAs($this->admin)
            ->getJson('/api/v1/admin/reports/frequently-out-of-stock');

        $response->assertOk();

        expect($response->json('data'))->toHaveCount(1);
        expect($response->json('data.0.out_of_stock_count'))->toBe(2);
    });
});

describe('Stock Logs', function (): void {
    it('creates stock log on order placement', function (): void {
        $medicine = Medicine::factory()->create([
            'category_id' => $this->category->id,
            'stock_quantity' => 20,
            'price' => 10.00,
        ]);

        CartItem::factory()->create([
            'user_id' => $this->customer->id,
            'medicine_id' => $medicine->id,
            'quantity' => 5,
        ]);

        $this->actingAs($this->customer)
            ->postJson('/api/v1/orders')
            ->assertStatus(201);

        $this->assertDatabaseHas('stock_logs', [
            'medicine_id' => $medicine->id,
            'type' => 'sale',
            'quantity_changed' => 5,
            'stock_before' => 20,
            'stock_after' => 15,
        ]);
    });

    it('creates stock log on order rejection', function (): void {
        $medicine = Medicine::factory()->create([
            'category_id' => $this->category->id,
            'stock_quantity' => 15,
        ]);
        $order = Order::factory()->create(['user_id' => $this->customer->id]);
        OrderItem::factory()->create([
            'order_id' => $order->id,
            'medicine_id' => $medicine->id,
            'quantity' => 5,
        ]);

        $this->actingAs($this->admin)
            ->patchJson(sprintf('/api/v1/admin/orders/%d/reject', $order->id))
            ->assertOk();

        $this->assertDatabaseHas('stock_logs', [
            'medicine_id' => $medicine->id,
            'type' => 'restock',
            'quantity_changed' => 5,
            'stock_before' => 15,
            'stock_after' => 20,
        ]);
    });
});
