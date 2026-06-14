<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\V1;

use App\Enums\DosageForm;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class UpdateMedicineRequest extends FormRequest
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
            'category_id' => ['sometimes', 'required', 'integer', 'exists:categories,id'],
            'brand_name' => ['sometimes', 'required', 'string', 'max:255'],
            'scientific_name' => ['sometimes', 'required', 'string', 'max:255'],
            'dosage_form' => ['sometimes', 'required', 'string', Rule::enum(DosageForm::class)],
            'price' => ['sometimes', 'required', 'numeric', 'min:0.01'],
            'stock_quantity' => ['sometimes', 'required', 'integer', 'min:0'],
            'expiry_date' => ['nullable', 'date', 'after:today'],
            'requires_prescription' => ['sometimes', 'boolean'],
            'is_available' => ['sometimes', 'boolean'],
            'description' => ['nullable', 'string'],
            'images' => ['nullable', 'array', 'max:5'],
            'images.*' => ['image', 'max:2048'],
            'manufacturer' => ['nullable', 'string', 'max:255'],
        ];
    }
}
