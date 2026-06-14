<?php

declare(strict_types=1);

use App\Models\CartItem;
use App\Models\Category;
use App\Models\Medicine;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->user = User::factory()->create();
    $this->category = Category::factory()->create();
});

describe('List Cart', function (): void {
    it('returns empty cart', function (): void {
        $response = $this->actingAs($this->user)
            ->getJson('/api/v1/cart');

        $response->assertOk()
            ->assertJsonPath('data.items_count', 0)
            ->assertJsonPath('data.total', '0.00');
    });

    it('lists cart items with details', function (): void {
        $medicine = Medicine::factory()->create([
            'category_id' => $this->category->id,
            'price' => 10.00,
        ]);

        CartItem::factory()->create([
            'user_id' => $this->user->id,
            'medicine_id' => $medicine->id,
            'quantity' => 2,
        ]);

        $response = $this->actingAs($this->user)
            ->getJson('/api/v1/cart');

        $response->assertOk()
            ->assertJsonPath('data.items_count', 1)
            ->assertJsonPath('data.total', '20.00');
    });

    it('shows correct subtotals', function (): void {
        $medicine = Medicine::factory()->create([
            'category_id' => $this->category->id,
            'price' => 5.00,
        ]);

        CartItem::factory()->create([
            'user_id' => $this->user->id,
            'medicine_id' => $medicine->id,
            'quantity' => 3,
        ]);

        $response = $this->actingAs($this->user)
            ->getJson('/api/v1/cart');

        $response->assertOk()
            ->assertJsonPath('data.items.0.subtotal', '15.00');
    });
});

describe('Add to Cart', function (): void {
    it('adds an item to cart', function (): void {
        $medicine = Medicine::factory()->create([
            'category_id' => $this->category->id,
            'stock_quantity' => 10,
        ]);

        $response = $this->actingAs($this->user)
            ->postJson('/api/v1/cart', [
                'medicine_id' => $medicine->id,
                'quantity' => 2,
            ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('cart_items', [
            'user_id' => $this->user->id,
            'medicine_id' => $medicine->id,
            'quantity' => 2,
        ]);
    });

    it('updates quantity on re-add', function (): void {
        $medicine = Medicine::factory()->create([
            'category_id' => $this->category->id,
            'stock_quantity' => 10,
        ]);

        $this->actingAs($this->user)
            ->postJson('/api/v1/cart', [
                'medicine_id' => $medicine->id,
                'quantity' => 2,
            ]);

        $response = $this->actingAs($this->user)
            ->postJson('/api/v1/cart', [
                'medicine_id' => $medicine->id,
                'quantity' => 5,
            ]);

        $response->assertStatus(201);

        expect(CartItem::query()->where('user_id', $this->user->id)->where('medicine_id', $medicine->id)->first()->quantity)->toBe(5);
    });

    it('rejects unavailable medicine', function (): void {
        $medicine = Medicine::factory()->unavailable()->create([
            'category_id' => $this->category->id,
            'stock_quantity' => 10,
        ]);

        $response = $this->actingAs($this->user)
            ->postJson('/api/v1/cart', [
                'medicine_id' => $medicine->id,
                'quantity' => 1,
            ]);

        $response->assertStatus(422);
    });

    it('rejects insufficient stock', function (): void {
        $medicine = Medicine::factory()->create([
            'category_id' => $this->category->id,
            'stock_quantity' => 2,
        ]);

        $response = $this->actingAs($this->user)
            ->postJson('/api/v1/cart', [
                'medicine_id' => $medicine->id,
                'quantity' => 5,
            ]);

        $response->assertStatus(422);
    });
});

describe('Update Cart', function (): void {
    it('updates cart item quantity', function (): void {
        $medicine = Medicine::factory()->create(['category_id' => $this->category->id]);
        $cartItem = CartItem::factory()->create([
            'user_id' => $this->user->id,
            'medicine_id' => $medicine->id,
            'quantity' => 1,
        ]);

        $response = $this->actingAs($this->user)
            ->putJson('/api/v1/cart/'.$cartItem->id, ['quantity' => 3]);

        $response->assertOk();

        expect($cartItem->fresh()->quantity)->toBe(3);
    });

    it('rejects update on other user cart item', function (): void {
        $otherUser = User::factory()->create();
        $medicine = Medicine::factory()->create(['category_id' => $this->category->id]);
        $cartItem = CartItem::factory()->create([
            'user_id' => $otherUser->id,
            'medicine_id' => $medicine->id,
        ]);

        $response = $this->actingAs($this->user)
            ->putJson('/api/v1/cart/'.$cartItem->id, ['quantity' => 3]);

        $response->assertStatus(403);
    });
});

describe('Remove from Cart', function (): void {
    it('removes a single item', function (): void {
        $medicine = Medicine::factory()->create(['category_id' => $this->category->id]);
        $cartItem = CartItem::factory()->create([
            'user_id' => $this->user->id,
            'medicine_id' => $medicine->id,
        ]);

        $response = $this->actingAs($this->user)
            ->deleteJson('/api/v1/cart/'.$cartItem->id);

        $response->assertOk();
        $this->assertDatabaseMissing('cart_items', ['id' => $cartItem->id]);
    });

    it('clears all cart items', function (): void {
        $medicine1 = Medicine::factory()->create(['category_id' => $this->category->id]);
        $medicine2 = Medicine::factory()->create(['category_id' => $this->category->id]);
        CartItem::factory()->create(['user_id' => $this->user->id, 'medicine_id' => $medicine1->id]);
        CartItem::factory()->create(['user_id' => $this->user->id, 'medicine_id' => $medicine2->id]);

        $response = $this->actingAs($this->user)
            ->deleteJson('/api/v1/cart');

        $response->assertOk();

        expect(CartItem::query()->where('user_id', $this->user->id)->count())->toBe(0);
    });
});

describe('Auth Guard', function (): void {
    it('requires authentication', function (): void {
        $response = $this->getJson('/api/v1/cart');

        $response->assertUnauthorized();
    });
});
