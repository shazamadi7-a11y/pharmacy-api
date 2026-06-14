<?php

declare(strict_types=1);

use App\Enums\OrderStatus;
use App\Models\CartItem;
use App\Models\Category;
use App\Models\Medicine;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use App\Notifications\LowStockAlert;
use App\Notifications\OrderStatusChanged;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->admin = User::factory()->admin()->create();
    $this->customer = User::factory()->create();
    $this->category = Category::factory()->create();
});

describe('Order Status Notifications', function (): void {
    it('sends notification when order is confirmed', function (): void {
        $order = Order::factory()->create(['user_id' => $this->customer->id]);

        $this->actingAs($this->admin)
            ->patchJson(sprintf('/api/v1/admin/orders/%d/confirm', $order->id))
            ->assertOk();

        expect($this->customer->notifications)->toHaveCount(1);
        expect($this->customer->notifications->first()->type)->toBe(OrderStatusChanged::class);
    });

    it('sends notification when order is rejected', function (): void {
        $order = Order::factory()->create(['user_id' => $this->customer->id]);
        $medicine = Medicine::factory()->create(['category_id' => $this->category->id, 'stock_quantity' => 100]);
        OrderItem::factory()->create(['order_id' => $order->id, 'medicine_id' => $medicine->id, 'quantity' => 5]);

        $this->actingAs($this->admin)
            ->patchJson(sprintf('/api/v1/admin/orders/%d/reject', $order->id))
            ->assertOk();

        expect($this->customer->fresh()->notifications)->toHaveCount(1);
    });

    it('sends notification when order is completed', function (): void {
        $order = Order::factory()->create([
            'user_id' => $this->customer->id,
            'status' => OrderStatus::Confirmed,
        ]);

        $this->actingAs($this->admin)
            ->patchJson(sprintf('/api/v1/admin/orders/%d/complete', $order->id))
            ->assertOk();

        expect($this->customer->fresh()->notifications)->toHaveCount(1);
    });
});

describe('Notification Endpoints', function (): void {
    it('lists all notifications', function (): void {
        $this->customer->notify(new OrderStatusChanged(
            Order::factory()->create(['user_id' => $this->customer->id]),
            'pending',
            'confirmed',
        ));

        $response = $this->actingAs($this->customer)
            ->getJson('/api/v1/notifications');

        $response->assertOk();

        expect($response->json('data.data'))->toHaveCount(1);
    });

    it('lists unread notifications', function (): void {
        $this->customer->notify(new OrderStatusChanged(
            Order::factory()->create(['user_id' => $this->customer->id]),
            'pending',
            'confirmed',
        ));

        $response = $this->actingAs($this->customer)
            ->getJson('/api/v1/notifications/unread');

        $response->assertOk();

        expect($response->json('data.data'))->toHaveCount(1);
    });

    it('marks a notification as read', function (): void {
        $this->customer->notify(new OrderStatusChanged(
            Order::factory()->create(['user_id' => $this->customer->id]),
            'pending',
            'confirmed',
        ));

        $notificationId = $this->customer->notifications->first()->id;

        $response = $this->actingAs($this->customer)
            ->patchJson(sprintf('/api/v1/notifications/%s/read', $notificationId));

        $response->assertOk();

        expect($this->customer->fresh()->unreadNotifications)->toHaveCount(0);
    });

    it('marks all notifications as read', function (): void {
        $order = Order::factory()->create(['user_id' => $this->customer->id]);
        $this->customer->notify(new OrderStatusChanged($order, 'pending', 'confirmed'));
        $this->customer->notify(new OrderStatusChanged($order, 'confirmed', 'completed'));

        $response = $this->actingAs($this->customer)
            ->patchJson('/api/v1/notifications/read-all');

        $response->assertOk();

        expect($this->customer->fresh()->unreadNotifications)->toHaveCount(0);
    });

    it('returns 404 for non-existent notification', function (): void {
        $response = $this->actingAs($this->customer)
            ->patchJson('/api/v1/notifications/non-existent-id/read');

        $response->assertNotFound();
    });
});

describe('Low Stock Alerts', function (): void {
    it('sends low stock alert to admins when stock drops below threshold', function (): void {
        config(['pharmacy.low_stock_threshold' => 10]);

        $medicine = Medicine::factory()->create([
            'category_id' => $this->category->id,
            'stock_quantity' => 8,
            'price' => 10.00,
        ]);

        CartItem::factory()->create([
            'user_id' => $this->customer->id,
            'medicine_id' => $medicine->id,
            'quantity' => 3,
        ]);

        $this->actingAs($this->customer)
            ->postJson('/api/v1/orders')
            ->assertStatus(201);

        expect($this->admin->fresh()->notifications)->toHaveCount(1);
        expect($this->admin->notifications->first()->type)->toBe(LowStockAlert::class);
    });

    it('admin can view low stock alerts', function (): void {
        $medicine = Medicine::factory()->create(['category_id' => $this->category->id]);
        $this->admin->notify(new LowStockAlert($medicine));

        $response = $this->actingAs($this->admin)
            ->getJson('/api/v1/admin/notifications/low-stock');

        $response->assertOk();

        expect($response->json('data.data'))->toHaveCount(1);
    });
});
