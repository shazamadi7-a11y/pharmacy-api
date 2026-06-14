<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

final class OrderStatusChanged extends Notification
{
    use Queueable;

    public function __construct(
        private readonly Order $order,
        private readonly string $oldStatus,
        private readonly string $newStatus,
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
            'order_id' => $this->order->id,
            'old_status' => $this->oldStatus,
            'new_status' => $this->newStatus,
            'message' => sprintf(
                'Your order #%d status has been updated from %s to %s.',
                $this->order->id,
                $this->oldStatus,
                $this->newStatus,
            ),
        ];
    }
}
