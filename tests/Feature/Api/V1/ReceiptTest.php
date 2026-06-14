<?php

declare(strict_types=1);

use App\Models\Category;
use App\Models\Medicine;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->admin = User::factory()->admin()->create();
    $this->customer = User::factory()->create();
    $this->category = Category::factory()->create();
});

describe('Receipt PDF', function (): void {
    it('customer can download their own order receipt', function (): void {
        $medicine = Medicine::factory()->create(['category_id' => $this->category->id]);
        $order = Order::factory()->create(['user_id' => $this->customer->id]);
        OrderItem::factory()->create([
            'order_id' => $order->id,
            'medicine_id' => $medicine->id,
            'quantity' => 2,
            'unit_price' => 15.00,
        ]);

        $response = $this->actingAs($this->customer)
            ->getJson(sprintf('/api/v1/orders/%d/receipt', $order->id));

        $response->assertOk();

        expect($response->headers->get('Content-Type'))->toContain('pdf');
    });

    it('customer cannot download another users order receipt', function (): void {
        $otherCustomer = User::factory()->create();
        $order = Order::factory()->create(['user_id' => $otherCustomer->id]);

        $response = $this->actingAs($this->customer)
            ->getJson(sprintf('/api/v1/orders/%d/receipt', $order->id));

        $response->assertForbidden();
    });

    it('admin can download any order receipt', function (): void {
        $medicine = Medicine::factory()->create(['category_id' => $this->category->id]);
        $order = Order::factory()->create(['user_id' => $this->customer->id]);
        OrderItem::factory()->create([
            'order_id' => $order->id,
            'medicine_id' => $medicine->id,
            'quantity' => 1,
            'unit_price' => 10.00,
        ]);

        $response = $this->actingAs($this->admin)
            ->getJson(sprintf('/api/v1/admin/orders/%d/receipt', $order->id));

        $response->assertOk();

        expect($response->headers->get('Content-Type'))->toContain('pdf');
    });

    it('requires authentication', function (): void {
        $order = Order::factory()->create(['user_id' => $this->customer->id]);

        $response = $this->getJson(sprintf('/api/v1/orders/%d/receipt', $order->id));

        $response->assertUnauthorized();
    });
});
