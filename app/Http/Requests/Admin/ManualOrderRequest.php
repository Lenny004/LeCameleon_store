<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class ManualOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => ['required', 'email', 'max:255'],
            'customer_name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'shipping_address' => ['required', 'array'],
            'shipping_address.first_name' => ['required', 'string', 'max:100'],
            'shipping_address.last_name' => ['required', 'string', 'max:100'],
            'shipping_address.line1' => ['required', 'string', 'max:255'],
            'shipping_address.line2' => ['nullable', 'string', 'max:255'],
            'shipping_address.city' => ['required', 'string', 'max:100'],
            'shipping_address.state' => ['nullable', 'string', 'max:100'],
            'shipping_address.postal_code' => ['required', 'string', 'max:20'],
            'shipping_address.country' => ['required', 'string', 'size:2'],
            'shipping_address.phone' => ['nullable', 'string', 'max:30'],
            'destination_municipality_id' => ['nullable', 'integer', 'exists:sv_municipalities,id'],
            'shipping_override' => ['nullable', 'numeric', 'min:0', 'max:9999999999.99'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['nullable', 'uuid', 'exists:products,id'],
            'items.*.quantity' => ['nullable', 'required_with:items.*.product_id', 'integer', 'min:1', 'max:32767'],
            'payment_method' => ['required', 'in:transfer,cod,manual'],
            'mark_paid' => ['nullable', 'boolean'],
            'send_email' => ['nullable', 'boolean'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    protected function after(): array
    {
        return [function ($validator): void {
            $items = $this->input('items', []);
            $hasProduct = collect($items)->contains(fn ($item): bool => filled(data_get($item, 'product_id')));

            if (! $hasProduct) {
                $validator->errors()->add('items', 'Agrega al menos un producto.');
            }
        }];
    }
}
