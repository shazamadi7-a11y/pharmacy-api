<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

final class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Antibiotics', 'description' => 'Medications used to treat bacterial infections'],
            ['name' => 'Painkillers', 'description' => 'Medications for pain relief and management'],
            ['name' => 'Vitamins', 'description' => 'Essential vitamins and nutritional supplements'],
            ['name' => 'Supplements', 'description' => 'Dietary and health supplements'],
            ['name' => 'Skincare', 'description' => 'Dermatological products and skin treatments'],
            ['name' => 'First Aid', 'description' => 'First aid supplies and emergency medications'],
        ];

        foreach ($categories as $category) {
            $nameSlug = urlencode($category['name']);

            Category::query()->create([
                'name' => $category['name'],
                'slug' => Str::slug($category['name']),
                'description' => $category['description'],
                'image' => 'https://placehold.co/400x300/EEE/31343C?text='.$nameSlug,
            ]);
        }
    }
}
