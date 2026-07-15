<?php

namespace App\Http\Requests\Store;

use Illuminate\Foundation\Http\FormRequest;

class ReturnRequestFormRequest extends FormRequest
{
    public function authorize(): bool
    {
        $order = $this->route('order');

        return $this->user() !== null && $order->user_id === $this->user()->id;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $order = $this->route('order');

        return [
            'order_item_id' => [
                'nullable',
                'integer',
                'exists:order_items,id,order_id,'.$order->id,
            ],
            'reason' => ['required', 'string', 'max:2000'],
        ];
    }
}
