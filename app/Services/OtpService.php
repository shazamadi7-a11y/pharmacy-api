<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\OtpCode;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Twilio\Rest\Client;

final class OtpService
{
    public function generate(User $user, string $type = 'phone_verification'): OtpCode
    {
        $user->otpCodes()->where('type', $type)->whereNull('verified_at')->delete();

        return OtpCode::query()->create([
            'user_id' => $user->id,
            'code' => mb_str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT),
            'type' => $type,
            'expires_at' => now()->addMinutes(10),
        ]);
    }

    public function sendViaTwilio(User $user): void
    {
        /** @var string $accountSid */
        $accountSid = config('services.twilio.account_sid');
        /** @var string $authToken */
        $authToken = config('services.twilio.auth_token');
        /** @var string $verifySid */
        $verifySid = config('services.twilio.verify_sid');

        $client = new Client($accountSid, $authToken);

        $verification = $client->verify->v2
            ->services($verifySid)
            ->verifications
            ->create($user->phone ?? '', 'sms');

        Log::info('Twilio Verify OTP sent', [
            'user_id' => $user->id,
            'phone' => $user->phone,
            'verification_sid' => $verification->sid,
            'status' => $verification->status,
        ]);
    }

    public function verifyViaTwilio(User $user, string $code): bool
    {
        /** @var string $accountSid */
        $accountSid = config('services.twilio.account_sid');
        /** @var string $authToken */
        $authToken = config('services.twilio.auth_token');
        /** @var string $verifySid */
        $verifySid = config('services.twilio.verify_sid');

        $client = new Client($accountSid, $authToken);

        $check = $client->verify->v2
            ->services($verifySid)
            ->verificationChecks
            ->create([
                'to' => $user->phone ?? '',
                'code' => $code,
            ]);

        if ($check->status === 'approved') {
            $user->update(['phone_verified_at' => now()]);

            $user->otpCodes()
                ->where('type', 'phone_verification')
                ->whereNull('verified_at')
                ->update(['verified_at' => now()]);

            return true;
        }

        return false;
    }

    public function verify(User $user, string $code, string $type = 'phone_verification'): bool
    {
        $otp = $user->otpCodes()
            ->where('code', $code)
            ->where('type', $type)
            ->whereNull('verified_at')
            ->latest()
            ->first();

        if ($otp === null || $otp->isExpired()) {
            return false;
        }

        $otp->update(['verified_at' => now()]);

        if ($type === 'phone_verification') {
            $user->update(['phone_verified_at' => now()]);
        }

        return true;
    }

    public function isTwilioConfigured(): bool
    {
        return config('services.twilio.verify_sid') !== null
            && config('services.twilio.verify_sid') !== '';
    }
}
