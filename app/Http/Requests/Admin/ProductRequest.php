<?php

namespace App\Http\Requests\Admin;

use App\Enums\ConditionGrade;
use App\Enums\ProductStatus;
use App\Enums\ProductType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProductRequest extends FormRequest
{
    /** @var list<string> */
    public const MEASUREMENT_KEYS = [
        'chest_cm',
        'waist_cm',
        'hips_cm',
        'length_cm',
        'shoulder_cm',
        'sleeve_cm',
    ];

    public function authorize(): bool
    {
        return $this->user()?->isStaff() ?? false;
    }

    /**
     * Unchecked checkboxes are omitted from the request; normalize them here.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_unique_piece' => $this->boolean('is_unique_piece'),
        ]);

        $measurements = [];

        foreach (self::MEASUREMENT_KEYS as $key) {
            $value = $this->input("measurement_{$key}");

            if ($value !== null && $value !== '') {
                $measurements[$key] = (float) $value;
            }
        }

        $this->merge([
            'measurements' => $measurements !== [] ? $measurements : null,
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $productId = $this->route('product')?->id;

        return [
            'brand_id' => ['nullable', 'exists:brands,id'],
            'category_id' => ['nullable', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:280', Rule::unique('products', 'slug')->ignore($productId)],
            'sku' => ['required', 'string', 'max:80', Rule::unique('products', 'sku')->ignore($productId)],
            'type' => ['required', Rule::enum(ProductType::class)],
            'status' => ['required', Rule::enum(ProductStatus::class)],
            'description' => ['nullable', 'string'],
            'short_description' => ['nullable', 'string', 'max:500'],
            'price' => ['required', 'numeric', 'min:0'],
            'compare_at_price' => ['nullable', 'numeric', 'min:0'],
            'cost_price' => ['nullable', 'numeric', 'min:0'],
            'condition_grade' => ['required', Rule::enum(ConditionGrade::class)],
            'era_decade' => ['nullable', 'string', 'max:20'],
            'size_label' => ['nullable', 'string', 'max:50'],
            'color' => ['nullable', 'string', 'max:80'],
            'material' => ['nullable', 'string', 'max:150'],
            'measurements' => ['nullable', 'array'],
            'measurements.*' => ['numeric', 'min:0'],
            'is_unique_piece' => ['sometimes', 'boolean'],
            'quantity_available' => ['required', 'integer', 'min:0'],
            'low_stock_threshold' => ['sometimes', 'integer', 'min:0'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:500'],
            'published_at' => ['nullable', 'date'],
            'images' => ['sometimes', 'array'],
            'images.*' => ['image', 'max:5120'],
            'keep_image_ids' => ['sometimes', 'array'],
            'keep_image_ids.*' => ['integer', 'exists:product_images,id'],
        ];
    }
}
