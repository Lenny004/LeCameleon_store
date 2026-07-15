<?php

namespace App\Http\Requests\Store;

use App\Models\Product;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class OfferRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'amount' => ['required', 'numeric', 'min:1'],
            'message' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            /** @var Product|null $product */
            $product = $this->route('product');

            if (! $product instanceof Product) {
                return;
            }

            $amount = (float) $this->input('amount');

            if ($amount >= (float) $product->price) {
                $validator->errors()->add(
                    'amount',
                    'La oferta debe ser menor al precio de lista ($'.number_format((float) $product->price, 2).').',
                );
            }
        });
    }
}
