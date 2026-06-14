<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Seeder;

final class AdminSeeder extends Seeder
{
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@pharmacy.com',
            'phone' => '+970599000001',
            'phone_verified_at' => now(),
            'role' => UserRole::Admin,
        ]);
    }
}
