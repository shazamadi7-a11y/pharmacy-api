<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\Medicine;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Medicine
 */
final class MedicineResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'category_id' => $this->category_id,
            'brand_name' => $this->brand_name,
            'scientific_name' => $this->scientific_name,
            'dosage_form' => $this->dosage_form->value,
            'price' => $this->price,
            'stock_quantity' => $this->stock_quantity,
            'expiry_date' => $this->expiry_date?->toDateString(),
            'requires_prescription' => $this->requires_prescription,
            'is_available' => $this->is_available,
            'description' => $this->description,
            'manufacturer' => $this->manufacturer,
            'images' => MedicineImageResource::collection($this->whenLoaded('images')),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
