<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\DosageForm;
use App\Models\Category;
use App\Models\Medicine;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Medicine>
 */
final class MedicineFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'category_id' => Category::factory(),
            'brand_name' => fake()->unique()->word().' '.fake()->randomElement(['Plus', 'Forte', 'Max', 'Pro']), /** @phpstan-ignore-line */
            'scientific_name' => fake()->word().' '.fake()->randomElement(['hydrochloride', 'sulfate', 'acetate']), /** @phpstan-ignore-line */
            'dosage_form' => fake()->randomElement(DosageForm::cases()),
            'price' => fake()->randomFloat(2, 1, 500),
            'stock_quantity' => fake()->numberBetween(0, 200),
            'expiry_date' => fake()->dateTimeBetween('+1 month', '+3 years'),
            'requires_prescription' => fake()->boolean(30),
            'is_available' => true,
            'description' => fake()->sentence(),
            'manufacturer' => fake()->company(),
        ];
    }

    public function unavailable(): static
    {
        return $this->state(fn (array $attributes): array => [
            'is_available' => false,
        ]);
    }

    public function outOfStock(): static
    {
        return $this->state(fn (array $attributes): array => [
            'stock_quantity' => 0,
        ]);
    }
}
