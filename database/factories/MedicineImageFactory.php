<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Medicine;
use App\Models\MedicineImage;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MedicineImage>
 */
final class MedicineImageFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'medicine_id' => Medicine::factory(),
            'image_path' => 'images/medicines/'.fake()->uuid().'.jpg',
            'is_primary' => false,
        ];
    }
}
