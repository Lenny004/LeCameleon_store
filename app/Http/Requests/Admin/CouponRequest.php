<?php

namespace App\Http\Requests\Admin;

use App\Enums\CouponType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CouponRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isStaff() ?? false;
    }

    public function rules(): array
    {
        $couponId = $this->route('coupon')?->id;

        return [
            'code' => ['required', 'string', 'max:50', Rule::unique('coupons', 'code')->ignore($couponId)],
            'type' => ['required', 'string', 'max:20', Rule::enum(CouponType::class)],
            'value' => [
                'required', 'decimal:0,2', 'min:0', 'max:9999999999.99',
                Rule::when($this->input('type') === CouponType::Percent->value, ['max:100']),
            ],
            'min_order_amount' => ['nullable', 'decimal:0,2', 'min:0', 'max:9999999999.99'],
            'max_uses' => ['nullable', 'integer', 'min:1', 'max:2147483647'],
            'max_uses_per_user' => ['nullable', 'integer', 'min:1', 'max:4294967295'],
            'first_order_only' => ['sometimes', 'boolean'],
            'starts_at' => ['nullable', 'date'],
            'ends_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
            'is_active' => ['sometimes', 'boolean'],
            'shipping_only' => ['sometimes', 'boolean'],
            'category_ids' => ['sometimes', 'array'],
            'category_ids.*' => ['integer', 'exists:categories,id'],
            'product_ids' => ['sometimes', 'array'],
            'product_ids.*' => ['uuid', 'exists:products,id'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'first_order_only' => $this->boolean('first_order_only'),
        ]);
    }
}
