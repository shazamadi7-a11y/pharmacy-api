<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\CartItem;
use App\Models\Medicine;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin CartItem
 */
final class CartItemResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'medicine_id' => $this->medicine_id,
            'quantity' => $this->quantity,
            'medicine' => new MedicineResource($this->whenLoaded('medicine')),
            'subtotal' => $this->when(
                $this->relationLoaded('medicine') && $this->medicine !== null,
                function (): string {
                    /** @var Medicine $medicine */
                    $medicine = $this->medicine;

                    return number_format((float) $medicine->price * $this->quantity, 2, '.', '');
                }
            ),
        ];
    }
}
