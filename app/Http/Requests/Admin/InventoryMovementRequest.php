<?php

namespace App\Http\Requests\Admin;

use App\Enums\InventoryMovementType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class InventoryMovementRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isStaff() ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'product_id' => ['required', 'uuid', 'exists:products,id'],
            'type' => ['required', Rule::enum(InventoryMovementType::class)],
            'quantity' => ['required', 'integer', 'min:1'],
            'notes' => ['nullable', 'string', 'max:500'],
            'new_quantity_available' => ['required_if:type,adjust', 'integer', 'min:0'],
        ];
    }
}
