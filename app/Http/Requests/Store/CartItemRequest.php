<?php

namespace App\Http\Requests\Store;

use Illuminate\Foundation\Http\FormRequest;

class CartItemRequest extends FormRequest
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
            'product_id' => $this->isMethod('post')
                ? ['required', 'uuid', 'exists:products,id']
                : ['nullable', 'uuid', 'exists:products,id'],
            'quantity' => ['required', 'integer', 'min:1', 'max:99'],
        ];
    }
}
