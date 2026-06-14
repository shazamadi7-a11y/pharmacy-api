<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\Medicine;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin OrderItem
 */
final class OrderItemResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'medicine_id' => $this->medicine_id,
            'brand_name' => $this->when(
                $this->relationLoaded('medicine') && $this->medicine !== null,
                function (): string {
                    /** @var Medicine $medicine */
                    $medicine = $this->medicine;

                    return $medicine->brand_name;
                }
            ),
            'quantity' => $this->quantity,
            'unit_price' => $this->unit_price,
            'subtotal' => number_format((float) $this->unit_price * $this->quantity, 2, '.', ''),
        ];
    }
}
