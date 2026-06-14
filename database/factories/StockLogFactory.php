<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Medicine;
use App\Models\StockLog;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<StockLog>
 */
final class StockLogFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'medicine_id' => Medicine::factory(),
            'order_id' => null,
            'type' => 'sale',
            'quantity_changed' => fake()->numberBetween(1, 10),
            'stock_before' => fake()->numberBetween(10, 100),
            'stock_after' => fake()->numberBetween(0, 90),
        ];
    }
}
