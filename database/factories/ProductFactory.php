<?php

namespace Database\Factories;

use App\Enums\ConditionGrade;
use App\Enums\ProductStatus;
use App\Enums\ProductType;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    public function definition(): array
    {
        $name = fake()->unique()->words(4, true);
        $price = fake()->randomFloat(2, 25, 450);

        return [
            'brand_id' => Brand::factory(),
            'category_id' => Category::factory(),
            'name' => ucwords($name),
            'slug' => Str::slug($name.'-'.fake()->unique()->numerify('####')),
            'sku' => strtoupper(fake()->unique()->bothify('LC-####-??')),
            'type' => fake()->randomElement(ProductType::cases()),
            'status' => ProductStatus::Published,
            'description' => fake()->paragraphs(2, true),
            'short_description' => fake()->sentence(12),
            'price' => $price,
            'compare_at_price' => fake()->optional(0.4)->randomFloat(2, $price * 1.1, $price * 1.5),
            'cost_price' => fake()->optional(0.6)->randomFloat(2, $price * 0.3, $price * 0.7),
            'condition_grade' => fake()->randomElement(ConditionGrade::cases()),
            'era_decade' => fake()->optional(0.8)->randomElement(['1960s', '1970s', '1980s', '1990s', '2000s']),
            'size_label' => fake()->optional(0.7)->randomElement(['XS', 'S', 'M', 'L', 'XL', 'One Size']),
            'color' => fake()->optional()->safeColorName(),
            'material' => fake()->optional()->randomElement(['Cotton', 'Silk', 'Wool', 'Leather', 'Ceramic', 'Brass']),
            'is_unique_piece' => fake()->boolean(30),
            'quantity_available' => fake()->numberBetween(1, 5),
            'quantity_reserved' => 0,
            'low_stock_threshold' => 1,
            'meta_title' => null,
            'meta_description' => null,
            'published_at' => now(),
        ];
    }

    public function draft(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => ProductStatus::Draft,
            'published_at' => null,
        ]);
    }

    public function soldOut(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => ProductStatus::SoldOut,
            'quantity_available' => 0,
        ]);
    }

    public function uniquePiece(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_unique_piece' => true,
            'quantity_available' => 1,
        ]);
    }
}
