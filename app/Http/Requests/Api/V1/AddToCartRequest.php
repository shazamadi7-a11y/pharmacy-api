<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\V1;

use App\Models\Medicine;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

final class AddToCartRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'medicine_id' => ['required', 'integer', 'exists:medicines,id'],
            'quantity' => ['sometimes', 'integer', 'min:1'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'medicine_id.exists' => 'The selected medicine does not exist.',
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            $medicineId = $this->input('medicine_id');
            if (! is_numeric($medicineId)) {
                return;
            }

            /** @var Medicine|null $medicine */
            $medicine = Medicine::query()->find((int) $medicineId);

            if ($medicine === null) {
                return;
            }

            if (! $medicine->is_available) {
                $validator->errors()->add('medicine_id', 'This medicine is currently unavailable.');
            }

            $quantityInput = $this->input('quantity', 1);
            $quantity = is_numeric($quantityInput) ? (int) $quantityInput : 1;
            if ($medicine->stock_quantity < $quantity) {
                $validator->errors()->add('quantity', 'Insufficient stock. Available: '.$medicine->stock_quantity);
            }
        });
    }
}
