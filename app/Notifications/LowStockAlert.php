<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\Medicine;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

final class LowStockAlert extends Notification
{
    use Queueable;

    public function __construct(
        private readonly Medicine $medicine,
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
            'medicine_id' => $this->medicine->id,
            'brand_name' => $this->medicine->brand_name,
            'stock_quantity' => $this->medicine->stock_quantity,
            'message' => sprintf(
                'Low stock alert: %s has only %d units remaining.',
                $this->medicine->brand_name,
                $this->medicine->stock_quantity,
            ),
        ];
    }
}
