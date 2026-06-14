<?php

declare(strict_types=1);

use App\Enums\OrderStatus;
use App\Models\CartItem;
use App\Models\Category;
use App\Models\Medicine;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->admin = User::factory()->admin()->create();
    $this->customer = User::factory()->create();
    $this->category = Category::factory()->create();
});

describe('Place Order', function (): void {
    it('places order from cart', function (): void {
        $medicine = Medicine::factory()->create([
            'category_id' => $this->category->id,
            'price' => 10.00,
            'stock_quantity' => 20,
        ]);

        CartItem::factory()->create([
            'user_id' => $this->customer->id,
            'medicine_id' => $medicine->id,
            'quantity' => 2,
        ]);

        $response = $this->actingAs($this->customer)
            ->postJson('/api/v1/orders', ['notes' => 'Please deliver fast']);

        $response->assertStatus(201)
            ->assertJsonPath('data.status', 'pending')
            ->assertJsonPath('data.total_price', '20.00')
            ->assertJsonPath('data.notes', 'Please deliver fast');
    });

    it('clears cart after order', function (): void {
        $medicine = Medicine::factory()->create([
            'category_id' => $this->category->id,
            'price' => 10.00,
            'stock_quantity' => 20,
        ]);

        CartItem::factory()->create([
            'user_id' => $this->customer->id,
            'medicine_id' => $medicine->id,
            'quantity' => 2,
        ]);

        $this->actingAs($this->customer)
            ->postJson('/api/v1/orders');

        expect(CartItem::query()->where('user_id', $this->customer->id)->count())->toBe(0);
    });

    it('deducts stock', function (): void {
        $medicine = Medicine::factory()->create([
            'category_id' => $this->category->id,
            'price' => 10.00,
            'stock_quantity' => 20,
        ]);

        CartItem::factory()->create([
            'user_id' => $this->customer->id,
            'medicine_id' => $medicine->id,
            'quantity' => 3,
        ]);

        $this->actingAs($this->customer)
            ->postJson('/api/v1/orders');

        expect($medicine->fresh()->stock_quantity)->toBe(17);
    });

    it('snapshots unit price', function (): void {
        $medicine = Medicine::factory()->create([
            'category_id' => $this->category->id,
            'price' => 15.50,
            'stock_quantity' => 20,
        ]);

        CartItem::factory()->create([
            'user_id' => $this->customer->id,
            'medicine_id' => $medicine->id,
            'quantity' => 1,
        ]);

        $response = $this->actingAs($this->customer)
            ->postJson('/api/v1/orders');

        $response->assertStatus(201);
        $this->assertDatabaseHas('order_items', [
            'medicine_id' => $medicine->id,
            'unit_price' => '15.50',
        ]);
    });

    it('fails with empty cart', function (): void {
        $response = $this->actingAs($this->customer)
            ->postJson('/api/v1/orders');

        $response->assertStatus(422)
            ->assertJsonPath('message', 'Your cart is empty');
    });

    it('fails with unavailable medicine', function (): void {
        $medicine = Medicine::factory()->unavailable()->create([
            'category_id' => $this->category->id,
            'stock_quantity' => 20,
        ]);

        CartItem::factory()->create([
            'user_id' => $this->customer->id,
            'medicine_id' => $medicine->id,
            'quantity' => 1,
        ]);

        $response = $this->actingAs($this->customer)
            ->postJson('/api/v1/orders');

        $response->assertStatus(422);
    });

    it('fails with insufficient stock', function (): void {
        $medicine = Medicine::factory()->create([
            'category_id' => $this->category->id,
            'stock_quantity' => 2,
        ]);

        CartItem::factory()->create([
            'user_id' => $this->customer->id,
            'medicine_id' => $medicine->id,
            'quantity' => 5,
        ]);

        $response = $this->actingAs($this->customer)
            ->postJson('/api/v1/orders');

        $response->assertStatus(422);
    });
});

describe('Customer Orders', function (): void {
    it('customer sees only own orders', function (): void {
        $medicine = Medicine::factory()->create(['category_id' => $this->category->id, 'stock_quantity' => 100]);

        // Create order for customer
        CartItem::factory()->create(['user_id' => $this->customer->id, 'medicine_id' => $medicine->id, 'quantity' => 1]);
        $this->actingAs($this->customer)->postJson('/api/v1/orders');

        // Create order for another user
        $otherUser = User::factory()->create();
        CartItem::factory()->create(['user_id' => $otherUser->id, 'medicine_id' => $medicine->id, 'quantity' => 1]);
        $this->actingAs($otherUser)->postJson('/api/v1/orders');

        $response = $this->actingAs($this->customer)
            ->getJson('/api/v1/orders');

        $response->assertOk();

        expect($response->json('data.data'))->toHaveCount(1);
    });

    it('customer cannot see other user order', function (): void {
        $otherUser = User::factory()->create();
        $order = Order::factory()->create(['user_id' => $otherUser->id]);

        $response = $this->actingAs($this->customer)
            ->getJson('/api/v1/orders/'.$order->id);

        $response->assertStatus(403);
    });
});

describe('Admin Orders', function (): void {
    it('admin sees all orders', function (): void {
        Order::factory()->count(3)->create();

        $response = $this->actingAs($this->admin)
            ->getJson('/api/v1/admin/orders');

        $response->assertOk();

        expect($response->json('data.data'))->toHaveCount(3);
    });

    it('admin can view any order', function (): void {
        $order = Order::factory()->create(['user_id' => $this->customer->id]);

        $response = $this->actingAs($this->admin)
            ->getJson('/api/v1/admin/orders/'.$order->id);

        $response->assertOk()
            ->assertJsonPath('data.id', $order->id);
    });

    it('confirms a pending order', function (): void {
        $order = Order::factory()->create([
            'user_id' => $this->customer->id,
            'status' => OrderStatus::Pending,
        ]);

        $response = $this->actingAs($this->admin)
            ->patchJson(sprintf('/api/v1/admin/orders/%d/confirm', $order->id), [
                'admin_notes' => 'Approved',
            ]);

        $response->assertOk()
            ->assertJsonPath('data.status', 'confirmed')
            ->assertJsonPath('data.admin_notes', 'Approved');
    });

    it('rejects a pending order and restores stock', function (): void {
        $medicine = Medicine::factory()->create([
            'category_id' => $this->category->id,
            'stock_quantity' => 7,
        ]);

        $order = Order::factory()->create([
            'user_id' => $this->customer->id,
            'status' => OrderStatus::Pending,
        ]);

        $order->items()->create([
            'medicine_id' => $medicine->id,
            'quantity' => 3,
            'unit_price' => $medicine->price,
        ]);

        $response = $this->actingAs($this->admin)
            ->patchJson(sprintf('/api/v1/admin/orders/%d/reject', $order->id), [
                'admin_notes' => 'Out of stock',
            ]);

        $response->assertOk()
            ->assertJsonPath('data.status', 'rejected');

        expect($medicine->fresh()->stock_quantity)->toBe(10);
    });

    it('completes a confirmed order', function (): void {
        $order = Order::factory()->create([
            'user_id' => $this->customer->id,
            'status' => OrderStatus::Confirmed,
        ]);

        $response = $this->actingAs($this->admin)
            ->patchJson(sprintf('/api/v1/admin/orders/%d/complete', $order->id));

        $response->assertOk()
            ->assertJsonPath('data.status', 'completed');
    });

    it('rejects invalid transition: confirm non-pending', function (): void {
        $order = Order::factory()->create([
            'user_id' => $this->customer->id,
            'status' => OrderStatus::Confirmed,
        ]);

        $response = $this->actingAs($this->admin)
            ->patchJson(sprintf('/api/v1/admin/orders/%d/confirm', $order->id));

        $response->assertStatus(422);
    });

    it('rejects invalid transition: complete non-confirmed', function (): void {
        $order = Order::factory()->create([
            'user_id' => $this->customer->id,
            'status' => OrderStatus::Pending,
        ]);

        $response = $this->actingAs($this->admin)
            ->patchJson(sprintf('/api/v1/admin/orders/%d/complete', $order->id));

        $response->assertStatus(422);
    });
});

describe('Role Guards', function (): void {
    it('blocks non-admin from admin order routes', function (): void {
        $response = $this->actingAs($this->customer)
            ->getJson('/api/v1/admin/orders');

        $response->assertStatus(403);
    });

    it('requires auth for placing orders', function (): void {
        $response = $this->postJson('/api/v1/orders');

        $response->assertUnauthorized();
    });
});
