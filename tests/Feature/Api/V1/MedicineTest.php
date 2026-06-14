<?php

declare(strict_types=1);

use App\Models\Category;
use App\Models\Medicine;
use App\Models\MedicineImage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->admin = User::factory()->admin()->create();
    $this->customer = User::factory()->create();
    $this->category = Category::factory()->create();
});

describe('List Medicines', function (): void {
    it('lists all medicines', function (): void {
        Medicine::factory()->count(3)->create(['category_id' => $this->category->id]);

        $response = $this->getJson('/api/v1/medicines');

        $response->assertOk();

        expect($response->json('data.data'))->toHaveCount(3);
    });

    it('paginates medicines', function (): void {
        Medicine::factory()->count(20)->create(['category_id' => $this->category->id]);

        $response = $this->getJson('/api/v1/medicines?per_page=5');

        $response->assertOk();

        expect($response->json('data.data'))->toHaveCount(5);
        expect($response->json('data.meta.last_page'))->toBe(4);
    });

    it('filters by category', function (): void {
        $otherCategory = Category::factory()->create();
        Medicine::factory()->count(2)->create(['category_id' => $this->category->id]);
        Medicine::factory()->count(3)->create(['category_id' => $otherCategory->id]);

        $response = $this->getJson('/api/v1/medicines?filter[category_id]='.$this->category->id);

        $response->assertOk();

        expect($response->json('data.data'))->toHaveCount(2);
    });

    it('filters by availability', function (): void {
        Medicine::factory()->count(2)->create(['category_id' => $this->category->id, 'is_available' => true]);
        Medicine::factory()->unavailable()->create(['category_id' => $this->category->id]);

        $response = $this->getJson('/api/v1/medicines?filter[is_available]=1');

        $response->assertOk();

        expect($response->json('data.data'))->toHaveCount(2);
    });

    it('searches by brand and scientific name', function (): void {
        Medicine::factory()->create([
            'category_id' => $this->category->id,
            'brand_name' => 'Amoxicillin Pro',
            'scientific_name' => 'amoxicillin trihydrate',
        ]);
        Medicine::factory()->create([
            'category_id' => $this->category->id,
            'brand_name' => 'Panadol Forte',
            'scientific_name' => 'paracetamol hydrochloride',
        ]);

        $response = $this->getJson('/api/v1/medicines?filter[search]=Amoxicillin');

        $response->assertOk();

        expect($response->json('data.data'))->toHaveCount(1);
    });

    it('sorts by price', function (): void {
        Medicine::factory()->create(['category_id' => $this->category->id, 'price' => 50.00]);
        Medicine::factory()->create(['category_id' => $this->category->id, 'price' => 10.00]);
        Medicine::factory()->create(['category_id' => $this->category->id, 'price' => 30.00]);

        $response = $this->getJson('/api/v1/medicines?sort=price');

        $response->assertOk();

        $prices = collect($response->json('data.data'))->pluck('price')->toArray();
        expect($prices)->toBe(['10.00', '30.00', '50.00']);
    });
});

describe('Show Medicine', function (): void {
    it('shows a medicine with images', function (): void {
        $medicine = Medicine::factory()->create(['category_id' => $this->category->id]);
        MedicineImage::factory()->create(['medicine_id' => $medicine->id, 'is_primary' => true]);

        $response = $this->getJson('/api/v1/medicines/'.$medicine->id);

        $response->assertOk()
            ->assertJsonPath('data.brand_name', $medicine->brand_name)
            ->assertJsonCount(1, 'data.images');
    });

    it('returns 404 for missing medicine', function (): void {
        $response = $this->getJson('/api/v1/medicines/999');

        $response->assertNotFound();
    });
});

describe('Admin CRUD', function (): void {
    it('admin creates a medicine', function (): void {
        $response = $this->actingAs($this->admin)
            ->postJson('/api/v1/medicines', [
                'category_id' => $this->category->id,
                'brand_name' => 'Amoxicillin',
                'scientific_name' => 'amoxicillin trihydrate',
                'dosage_form' => 'tablet',
                'price' => 15.99,
                'stock_quantity' => 100,
            ]);

        $response->assertStatus(201)
            ->assertJsonPath('data.brand_name', 'Amoxicillin');
    });

    it('admin updates a medicine', function (): void {
        $medicine = Medicine::factory()->create(['category_id' => $this->category->id]);

        $response = $this->actingAs($this->admin)
            ->putJson('/api/v1/medicines/'.$medicine->id, [
                'price' => 25.00,
            ]);

        $response->assertOk()
            ->assertJsonPath('data.price', '25.00');
    });

    it('admin deletes a medicine', function (): void {
        $medicine = Medicine::factory()->create(['category_id' => $this->category->id]);

        $response = $this->actingAs($this->admin)
            ->deleteJson('/api/v1/medicines/'.$medicine->id);

        $response->assertOk();
        $this->assertDatabaseMissing('medicines', ['id' => $medicine->id]);
    });

    it('admin toggles availability', function (): void {
        $medicine = Medicine::factory()->create(['category_id' => $this->category->id, 'is_available' => true]);

        $response = $this->actingAs($this->admin)
            ->patchJson(sprintf('/api/v1/medicines/%d/toggle', $medicine->id));

        $response->assertOk()
            ->assertJsonPath('data.is_available', false);
    });
});

describe('Manufacturer Field', function (): void {
    it('admin creates a medicine with manufacturer', function (): void {
        $response = $this->actingAs($this->admin)
            ->postJson('/api/v1/medicines', [
                'category_id' => $this->category->id,
                'brand_name' => 'TestMed',
                'scientific_name' => 'testmedicine hydrochloride',
                'dosage_form' => 'tablet',
                'price' => 10.00,
                'stock_quantity' => 50,
                'manufacturer' => 'PharmaCo Inc.',
            ]);

        $response->assertStatus(201)
            ->assertJsonPath('data.manufacturer', 'PharmaCo Inc.');
    });

    it('shows manufacturer in medicine details', function (): void {
        $medicine = Medicine::factory()->create([
            'category_id' => $this->category->id,
            'manufacturer' => 'TestLab Ltd.',
        ]);

        $response = $this->getJson('/api/v1/medicines/'.$medicine->id);

        $response->assertOk()
            ->assertJsonPath('data.manufacturer', 'TestLab Ltd.');
    });

    it('admin updates manufacturer', function (): void {
        $medicine = Medicine::factory()->create(['category_id' => $this->category->id]);

        $response = $this->actingAs($this->admin)
            ->putJson('/api/v1/medicines/'.$medicine->id, [
                'manufacturer' => 'New Manufacturer',
            ]);

        $response->assertOk()
            ->assertJsonPath('data.manufacturer', 'New Manufacturer');
    });
});

describe('Auth & Role Guards', function (): void {
    it('fails without auth for admin routes', function (): void {
        $response = $this->postJson('/api/v1/medicines', ['brand_name' => 'Test']);

        $response->assertUnauthorized();
    });

    it('fails for non-admin', function (): void {
        $response = $this->actingAs($this->customer)
            ->postJson('/api/v1/medicines', ['brand_name' => 'Test']);

        $response->assertStatus(403);
    });
});
