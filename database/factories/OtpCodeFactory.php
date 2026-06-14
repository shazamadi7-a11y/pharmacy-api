<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\OtpCode;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<OtpCode>
 */
final class OtpCodeFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'code' => mb_str_pad((string) fake()->numberBetween(0, 999999), 6, '0', STR_PAD_LEFT),
            'type' => 'phone_verification',
            'expires_at' => now()->addMinutes(10),
        ];
    }
}
