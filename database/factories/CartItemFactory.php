<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\CartItem;
use App\Models\Medicine;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CartItem>
 */
final class CartItemFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'medicine_id' => Medicine::factory(),
            'quantity' => fake()->numberBetween(1, 5),
        ];
    }
}
