<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\Api\V1\ForgotPasswordRequest;
use App\Http\Requests\Api\V1\LoginRequest;
use App\Http\Requests\Api\V1\RegisterRequest;
use App\Http\Requests\Api\V1\ResendVerificationRequest;
use App\Http\Requests\Api\V1\ResetPasswordRequest;
use App\Http\Requests\Api\V1\VerifyEmailRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Notifications\OtpNotification;
use App\Services\OtpService;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;

final class AuthController extends ApiController
{
    public function register(RegisterRequest $request): JsonResponse
    {
        $user = User::query()->create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone' => $request->phone,
        ]);

        $user->sendEmailVerificationNotification();

        $token = $user->createToken('auth-token')->plainTextToken;

        return $this->created([
            'user' => new UserResource($user),
            'token' => $token,
        ], 'User registered successfully. Please check your email to verify your account.');
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $user = User::query()->where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return $this->unauthorized('Invalid credentials');
        }

        $token = $user->createToken('auth-token')->plainTextToken;

        return $this->success([
            'user' => new UserResource($user),
            'token' => $token,
        ], 'Login successful');
    }

    public function logout(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();
        $user->currentAccessToken()->delete();

        return $this->success(message: 'Logged out successfully');
    }

    public function me(Request $request): JsonResponse
    {
        return $this->success(new UserResource($request->user()));
    }

    public function verifyEmail(VerifyEmailRequest $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        if ($user->hasVerifiedEmail()) {
            return $this->success(message: 'Email already verified');
        }

        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
        }

        return $this->success(message: 'Email verified successfully');
    }

    public function resendVerificationEmail(ResendVerificationRequest $request): JsonResponse
    {
        $user = User::query()->where('email', $request->email)->first();

        if (! $user) {
            return $this->notFound('User not found');
        }

        if ($user->hasVerifiedEmail()) {
            return $this->error('Email already verified', 400);
        }

        $user->sendEmailVerificationNotification();

        return $this->success(message: 'Verification email sent successfully');
    }

    public function forgotPassword(ForgotPasswordRequest $request): JsonResponse
    {
        $status = Password::sendResetLink(
            $request->only('email')
        );

        if ($status === Password::RESET_LINK_SENT) {
            return $this->success(message: 'Password reset link sent to your email');
        }

        return $this->error('Unable to send reset link', 500);
    }

    public function sendOtp(Request $request, OtpService $otpService): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        if ($user->phone === null) {
            return $this->error('No phone number associated with your account', 422);
        }

        if ($user->phone_verified_at !== null) {
            return $this->error('Phone number already verified', 422);
        }

        if ($otpService->isTwilioConfigured()) {
            $otpService->sendViaTwilio($user);
            $otp = $otpService->generate($user);
            $user->notify(new OtpNotification($otp));

            return $this->success(message: 'OTP code sent successfully');
        }

        $otp = $otpService->generate($user);
        $user->notify(new OtpNotification($otp));

        if (config('app.debug')) {
            return $this->success(['otp_code' => $otp->code], 'OTP code sent successfully');
        }

        return $this->success(message: 'OTP code sent successfully');
    }

    public function verifyOtp(Request $request, OtpService $otpService): JsonResponse
    {
        $request->validate([
            'code' => ['required', 'string', 'size:6'],
        ]);

        /** @var User $user */
        $user = $request->user();

        $verified = $otpService->isTwilioConfigured()
            ? $otpService->verifyViaTwilio($user, $request->string('code')->value())
            : $otpService->verify($user, $request->string('code')->value());

        if ($verified) {
            return $this->success(
                new UserResource($user->fresh()),
                'Phone number verified successfully',
            );
        }

        return $this->error('Invalid or expired OTP code', 422);
    }

    public function resetPassword(ResetPasswordRequest $request): JsonResponse
    {
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password): void {
                $user->forceFill([
                    'password' => Hash::make($password),
                ])->save();

                $user->tokens()->delete();

                event(new PasswordReset($user));
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return $this->success(message: 'Password reset successfully');
        }

        return $this->error(
            match ($status) {
                Password::INVALID_TOKEN => 'Invalid or expired reset token',
                Password::INVALID_USER => 'User not found',
                default => 'Unable to reset password',
            },
            400
        );
    }
}
