<?php

declare(strict_types=1);

use App\Enums\UserRole;
use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->admin = User::factory()->admin()->create();
    $this->customer = User::factory()->create();
});

describe('Admin Middleware', function (): void {
    it('blocks customer users with 403', function (): void {
        $response = $this->actingAs($this->customer)
            ->postJson('/api/v1/categories', ['name' => 'Test']);

        $response->assertStatus(403)
            ->assertJson(['success' => false, 'message' => 'Admin access required']);
    });

    it('allows admin users', function (): void {
        $response = $this->actingAs($this->admin)
            ->postJson('/api/v1/categories', ['name' => 'Test Category']);

        $response->assertStatus(201);
    });
});

describe('Role in User Resource', function (): void {
    it('returns role in user resource on login', function (): void {
        $user = User::factory()->create(['password' => bcrypt('password')]);

        $response = $this->postJson('/api/v1/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertOk()
            ->assertJsonPath('data.user.role', UserRole::Customer->value);
    });

    it('returns role in me endpoint', function (): void {
        $response = $this->actingAs($this->admin)
            ->getJson('/api/v1/me');

        $response->assertOk()
            ->assertJsonPath('data.role', UserRole::Admin->value);
    });
});

describe('List Categories', function (): void {
    it('lists all categories', function (): void {
        Category::factory()->count(3)->create();

        $response = $this->getJson('/api/v1/categories');

        $response->assertOk()
            ->assertJsonCount(3, 'data');
    });

    it('returns empty state', function (): void {
        $response = $this->getJson('/api/v1/categories');

        $response->assertOk()
            ->assertJsonCount(0, 'data');
    });

    it('includes medicines_count', function (): void {
        $category = Category::factory()->create();

        $response = $this->getJson('/api/v1/categories');

        $response->assertOk()
            ->assertJsonPath('data.0.medicines_count', 0);
    });
});

describe('Show Category', function (): void {
    it('shows a single category', function (): void {
        $category = Category::factory()->create();

        $response = $this->getJson('/api/v1/categories/'.$category->id);

        $response->assertOk()
            ->assertJsonPath('data.name', $category->name);
    });

    it('returns 404 for missing category', function (): void {
        $response = $this->getJson('/api/v1/categories/999');

        $response->assertNotFound();
    });
});

describe('Create Category', function (): void {
    it('admin creates a category', function (): void {
        $response = $this->actingAs($this->admin)
            ->postJson('/api/v1/categories', [
                'name' => 'Antibiotics',
                'description' => 'Medications for bacterial infections',
            ]);

        $response->assertStatus(201)
            ->assertJsonPath('data.name', 'Antibiotics')
            ->assertJsonPath('data.slug', 'antibiotics');
    });

    it('auto-generates slug from name', function (): void {
        $response = $this->actingAs($this->admin)
            ->postJson('/api/v1/categories', ['name' => 'Cold & Flu']);

        $response->assertStatus(201)
            ->assertJsonPath('data.slug', 'cold-flu');
    });

    it('fails with duplicate name', function (): void {
        Category::factory()->create(['name' => 'Antibiotics']);

        $response = $this->actingAs($this->admin)
            ->postJson('/api/v1/categories', ['name' => 'Antibiotics']);

        $response->assertStatus(422);
    });

    it('fails without auth', function (): void {
        $response = $this->postJson('/api/v1/categories', ['name' => 'Test']);

        $response->assertUnauthorized();
    });

    it('fails for non-admin', function (): void {
        $response = $this->actingAs($this->customer)
            ->postJson('/api/v1/categories', ['name' => 'Test']);

        $response->assertStatus(403);
    });
});

describe('Update Category', function (): void {
    it('admin updates a category', function (): void {
        $category = Category::factory()->create();

        $response = $this->actingAs($this->admin)
            ->putJson('/api/v1/categories/'.$category->id, ['name' => 'Updated Name']);

        $response->assertOk()
            ->assertJsonPath('data.name', 'Updated Name');
    });
});

describe('Delete Category', function (): void {
    it('admin deletes a category', function (): void {
        $category = Category::factory()->create();

        $response = $this->actingAs($this->admin)
            ->deleteJson('/api/v1/categories/'.$category->id);

        $response->assertOk();
        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
    });
});
