<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\OtpCode;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

final class OtpNotification extends Notification
{
    use Queueable;

    public function __construct(
        private readonly OtpCode $otpCode,
    ) {}

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'code' => $this->otpCode->code,
            'type' => $this->otpCode->type,
            'expires_at' => $this->otpCode->expires_at->toIso8601String(),
            'message' => sprintf('Your OTP code is: %s. It expires in 10 minutes.', $this->otpCode->code),
        ];
    }
}
