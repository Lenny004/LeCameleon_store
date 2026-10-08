<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class CounterOfferRequest extends FormRequest
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
            'counter_amount' => ['required', 'decimal:0,2', 'min:1', 'max:9999999999.99'],
            'admin_notes' => ['nullable', 'string', 'max:2000'],
        ];
    }
}
