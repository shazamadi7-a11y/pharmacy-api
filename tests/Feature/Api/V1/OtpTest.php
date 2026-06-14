<?php

declare(strict_types=1);

use App\Models\OtpCode;
use App\Models\User;
use App\Notifications\OtpNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    Notification::fake();
    $this->user = User::factory()->create(['phone' => '+970591234567']);
});

describe('Send OTP', function (): void {
    it('sends an OTP code', function (): void {
        $response = $this->actingAs($this->user)
            ->postJson('/api/v1/otp/send');

        $response->assertOk()
            ->assertJson(['success' => true, 'message' => 'OTP code sent successfully'])
            ->assertJsonStructure(['data' => ['otp_code']]);

        $this->assertDatabaseHas('otp_codes', [
            'user_id' => $this->user->id,
            'type' => 'phone_verification',
        ]);

        Notification::assertSentTo($this->user, OtpNotification::class);
    });

    it('fails without phone number', function (): void {
        $user = User::factory()->create(['phone' => null]);

        $response = $this->actingAs($user)
            ->postJson('/api/v1/otp/send');

        $response->assertStatus(422)
            ->assertJson(['success' => false]);
    });

    it('fails if phone already verified', function (): void {
        $this->user->update(['phone_verified_at' => now()]);

        $response = $this->actingAs($this->user)
            ->postJson('/api/v1/otp/send');

        $response->assertStatus(422);
    });

    it('requires authentication', function (): void {
        $response = $this->postJson('/api/v1/otp/send');

        $response->assertUnauthorized();
    });
});

describe('Verify OTP', function (): void {
    it('verifies a valid OTP code', function (): void {
        $otp = OtpCode::query()->create([
            'user_id' => $this->user->id,
            'code' => '123456',
            'type' => 'phone_verification',
            'expires_at' => now()->addMinutes(10),
        ]);

        $response = $this->actingAs($this->user)
            ->postJson('/api/v1/otp/verify', ['code' => '123456']);

        $response->assertOk()
            ->assertJson(['success' => true, 'message' => 'Phone number verified successfully']);

        $this->user->refresh();
        expect($this->user->phone_verified_at)->not->toBeNull();
    });

    it('fails with invalid OTP code', function (): void {
        $response = $this->actingAs($this->user)
            ->postJson('/api/v1/otp/verify', ['code' => '000000']);

        $response->assertStatus(422)
            ->assertJson(['success' => false]);
    });

    it('fails with expired OTP code', function (): void {
        OtpCode::query()->create([
            'user_id' => $this->user->id,
            'code' => '123456',
            'type' => 'phone_verification',
            'expires_at' => now()->subMinutes(1),
        ]);

        $response = $this->actingAs($this->user)
            ->postJson('/api/v1/otp/verify', ['code' => '123456']);

        $response->assertStatus(422);
    });

    it('requires code field', function (): void {
        $response = $this->actingAs($this->user)
            ->postJson('/api/v1/otp/verify', []);

        $response->assertStatus(422);
    });
});
