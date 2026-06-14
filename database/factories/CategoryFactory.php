<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Category>
 */
final class CategoryFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        /** @var string $name */
        $name = fake()->unique()->randomElement([
            'Antibiotics',
            'Painkillers',
            'Vitamins',
            'Supplements',
            'Skincare',
            'First Aid',
            'Allergy Relief',
            'Digestive Health',
            'Cold & Flu',
            'Eye Care',
        ]);

        return [
            'name' => $name,
            'slug' => Str::slug($name),
            'description' => fake()->sentence(),
            'image' => null,
        ];
    }
}
