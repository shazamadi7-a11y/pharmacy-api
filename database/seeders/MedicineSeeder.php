<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\DosageForm;
use App\Models\Category;
use App\Models\Medicine;
use App\Models\MedicineImage;
use Illuminate\Database\Seeder;

final class MedicineSeeder extends Seeder
{
    public function run(): void
    {
        $categories = Category::all()->keyBy('name');

        $medicines = [
            // Antibiotics
            [
                'category' => 'Antibiotics',
                'brand_name' => 'Amoxil',
                'scientific_name' => 'Amoxicillin',
                'dosage_form' => DosageForm::Capsule,
                'price' => 12.50,
                'stock_quantity' => 200,
                'expiry_date' => now()->addYear(),
                'requires_prescription' => true,
                'description' => 'Broad-spectrum antibiotic for bacterial infections',
                'manufacturer' => 'GlaxoSmithKline',
            ],
            [
                'category' => 'Antibiotics',
                'brand_name' => 'Augmentin',
                'scientific_name' => 'Amoxicillin/Clavulanate',
                'dosage_form' => DosageForm::Tablet,
                'price' => 24.99,
                'stock_quantity' => 150,
                'expiry_date' => now()->addMonths(18),
                'requires_prescription' => true,
                'description' => 'Combination antibiotic for resistant bacterial infections',
                'manufacturer' => 'GlaxoSmithKline',
            ],
            [
                'category' => 'Antibiotics',
                'brand_name' => 'Zithromax',
                'scientific_name' => 'Azithromycin',
                'dosage_form' => DosageForm::Tablet,
                'price' => 18.75,
                'stock_quantity' => 120,
                'expiry_date' => now()->addYear(),
                'requires_prescription' => true,
                'description' => 'Macrolide antibiotic for respiratory and skin infections',
                'manufacturer' => 'Pfizer',
            ],

            // Painkillers
            [
                'category' => 'Painkillers',
                'brand_name' => 'Panadol',
                'scientific_name' => 'Paracetamol',
                'dosage_form' => DosageForm::Tablet,
                'price' => 5.99,
                'stock_quantity' => 500,
                'expiry_date' => now()->addYears(2),
                'requires_prescription' => false,
                'description' => 'Pain relief and fever reducer',
                'manufacturer' => 'GlaxoSmithKline',
            ],
            [
                'category' => 'Painkillers',
                'brand_name' => 'Advil',
                'scientific_name' => 'Ibuprofen',
                'dosage_form' => DosageForm::Tablet,
                'price' => 8.50,
                'stock_quantity' => 350,
                'expiry_date' => now()->addYears(2),
                'requires_prescription' => false,
                'description' => 'Anti-inflammatory pain reliever',
                'manufacturer' => 'Pfizer',
            ],
            [
                'category' => 'Painkillers',
                'brand_name' => 'Voltaren',
                'scientific_name' => 'Diclofenac',
                'dosage_form' => DosageForm::Cream,
                'price' => 14.25,
                'stock_quantity' => 180,
                'expiry_date' => now()->addMonths(18),
                'requires_prescription' => false,
                'description' => 'Topical anti-inflammatory for joint and muscle pain',
                'manufacturer' => 'Novartis',
            ],

            // Vitamins
            [
                'category' => 'Vitamins',
                'brand_name' => 'Centrum',
                'scientific_name' => 'Multivitamin Complex',
                'dosage_form' => DosageForm::Tablet,
                'price' => 22.99,
                'stock_quantity' => 300,
                'expiry_date' => now()->addYears(3),
                'requires_prescription' => false,
                'description' => 'Complete daily multivitamin for adults',
                'manufacturer' => 'Haleon',
            ],
            [
                'category' => 'Vitamins',
                'brand_name' => 'Nature Made D3',
                'scientific_name' => 'Cholecalciferol',
                'dosage_form' => DosageForm::Capsule,
                'price' => 11.50,
                'stock_quantity' => 250,
                'expiry_date' => now()->addYears(2),
                'requires_prescription' => false,
                'description' => 'Vitamin D3 supplement for bone health',
                'manufacturer' => 'Pharmavite',
            ],
            [
                'category' => 'Vitamins',
                'brand_name' => 'Emergen-C',
                'scientific_name' => 'Ascorbic Acid',
                'dosage_form' => DosageForm::Syrup,
                'price' => 9.99,
                'stock_quantity' => 200,
                'expiry_date' => now()->addYear(),
                'requires_prescription' => false,
                'description' => 'Vitamin C immune support supplement',
                'manufacturer' => 'Haleon',
            ],

            // Supplements
            [
                'category' => 'Supplements',
                'brand_name' => 'Optimum Nutrition Fish Oil',
                'scientific_name' => 'Omega-3 Fatty Acids',
                'dosage_form' => DosageForm::Capsule,
                'price' => 19.99,
                'stock_quantity' => 160,
                'expiry_date' => now()->addYear(),
                'requires_prescription' => false,
                'description' => 'Heart and brain health omega-3 supplement',
                'manufacturer' => 'Optimum Nutrition',
            ],
            [
                'category' => 'Supplements',
                'brand_name' => 'NOW Probiotics',
                'scientific_name' => 'Lactobacillus Acidophilus',
                'dosage_form' => DosageForm::Capsule,
                'price' => 16.75,
                'stock_quantity' => 140,
                'expiry_date' => now()->addMonths(8),
                'requires_prescription' => false,
                'description' => 'Digestive health probiotic supplement',
                'manufacturer' => 'NOW Foods',
            ],

            // Skincare
            [
                'category' => 'Skincare',
                'brand_name' => 'CeraVe Moisturizer',
                'scientific_name' => 'Ceramides Complex',
                'dosage_form' => DosageForm::Cream,
                'price' => 15.99,
                'stock_quantity' => 220,
                'expiry_date' => now()->addYears(2),
                'requires_prescription' => false,
                'description' => 'Daily moisturizing cream for dry skin',
                'manufacturer' => "L'Oréal",
            ],
            [
                'category' => 'Skincare',
                'brand_name' => 'Differin Gel',
                'scientific_name' => 'Adapalene',
                'dosage_form' => DosageForm::Cream,
                'price' => 29.99,
                'stock_quantity' => 90,
                'expiry_date' => now()->addYear(),
                'requires_prescription' => false,
                'description' => 'Retinoid gel for acne treatment',
                'manufacturer' => 'Galderma',
            ],
            [
                'category' => 'Skincare',
                'brand_name' => 'Dermovate',
                'scientific_name' => 'Clobetasol Propionate',
                'dosage_form' => DosageForm::Cream,
                'price' => 35.00,
                'stock_quantity' => 60,
                'expiry_date' => now()->addMonths(18),
                'requires_prescription' => true,
                'description' => 'Potent topical corticosteroid for severe skin conditions',
                'manufacturer' => 'GlaxoSmithKline',
            ],

            // First Aid
            [
                'category' => 'First Aid',
                'brand_name' => 'Betadine',
                'scientific_name' => 'Povidone-Iodine',
                'dosage_form' => DosageForm::Drops,
                'price' => 7.50,
                'stock_quantity' => 300,
                'expiry_date' => now()->addYears(3),
                'requires_prescription' => false,
                'description' => 'Antiseptic solution for wound disinfection',
                'manufacturer' => 'Avrio Health',
            ],
            [
                'category' => 'First Aid',
                'brand_name' => 'Burnol',
                'scientific_name' => 'Aminacrine Hydrochloride',
                'dosage_form' => DosageForm::Cream,
                'price' => 6.25,
                'stock_quantity' => 250,
                'expiry_date' => now()->addYears(2),
                'requires_prescription' => false,
                'description' => 'Antiseptic burn cream for minor burns',
                'manufacturer' => 'Dr. Morepen',
            ],
            [
                'category' => 'First Aid',
                'brand_name' => 'Neosporin',
                'scientific_name' => 'Neomycin/Polymyxin B/Bacitracin',
                'dosage_form' => DosageForm::Cream,
                'price' => 9.99,
                'stock_quantity' => 0,
                'expiry_date' => now()->addYear(),
                'requires_prescription' => false,
                'is_available' => false,
                'description' => 'Triple antibiotic ointment for wound care',
                'manufacturer' => 'Johnson & Johnson',
            ],
        ];

        foreach ($medicines as $data) {
            $categoryName = $data['category'];
            unset($data['category']);

            $category = $categories->get($categoryName);

            if (! $category instanceof Category) {
                continue;
            }

            $data['category_id'] = $category->id;

            if (! isset($data['is_available'])) {
                $data['is_available'] = true;
            }

            $medicine = Medicine::query()->create($data);

            $brandSlug = urlencode($medicine->brand_name);

            MedicineImage::query()->create([
                'medicine_id' => $medicine->id,
                'image_path' => 'https://placehold.co/300x300/EEE/31343C?text='.$brandSlug,
                'is_primary' => true,
            ]);

            if ($medicine->id % 3 === 0) {
                MedicineImage::query()->create([
                    'medicine_id' => $medicine->id,
                    'image_path' => sprintf('https://placehold.co/300x300/DDEEFF/31343C?text=%s+Box', $brandSlug),
                    'is_primary' => false,
                ]);
            }
        }
    }
}
