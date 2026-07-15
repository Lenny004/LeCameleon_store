<?php

namespace Database\Seeders;

use App\Enums\ConditionGrade;
use App\Enums\ProductStatus;
use App\Enums\ProductType;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductAttribute;
use App\Models\ProductImage;
use App\Models\Review;
use App\Models\User;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $brands = Brand::query()->pluck('id', 'slug');
        $categories = Category::query()->pluck('id', 'slug');

        $products = [
            [
                'name' => '1970s Floral Maxi Dress',
                'slug' => '1970s-floral-maxi-dress',
                'sku' => 'LC-DRS-001',
                'type' => ProductType::Apparel,
                'brand_slug' => 'unknown-vintage',
                'category_slug' => 'dresses',
                'price' => 185.00,
                'compare_at_price' => 240.00,
                'condition_grade' => ConditionGrade::Excellent,
                'era_decade' => '1970s',
                'size_label' => 'M',
                'color' => 'Floral Multi',
                'material' => 'Polyester Chiffon',
                'is_unique_piece' => true,
                'quantity_available' => 1,
                'measurements' => [
                    'bust_cm' => 92,
                    'waist_cm' => 72,
                    'length_cm' => 145,
                ],
                'attributes' => ['provenance' => 'Estate sale, San Francisco', 'care' => 'Dry clean only'],
            ],
            [
                'name' => 'Levi\'s 501 Original Fit Jeans',
                'slug' => 'levis-501-original-fit-jeans',
                'sku' => 'LC-DNM-002',
                'type' => ProductType::Apparel,
                'brand_slug' => 'levis',
                'category_slug' => 'denim',
                'price' => 120.00,
                'compare_at_price' => null,
                'condition_grade' => ConditionGrade::Good,
                'era_decade' => '1990s',
                'size_label' => '32x32',
                'color' => 'Indigo',
                'material' => 'Cotton Denim',
                'is_unique_piece' => true,
                'quantity_available' => 1,
                'attributes' => ['made_in' => 'USA', 'button_fly' => 'Yes'],
            ],
            [
                'name' => 'Chanel Quilted Lambskin Flap Bag',
                'slug' => 'chanel-quilted-lambskin-flap-bag',
                'sku' => 'LC-BAG-003',
                'type' => ProductType::Accessory,
                'brand_slug' => 'chanel',
                'category_slug' => 'handbags',
                'price' => 3200.00,
                'compare_at_price' => 4500.00,
                'condition_grade' => ConditionGrade::Excellent,
                'era_decade' => '1990s',
                'size_label' => 'Medium',
                'color' => 'Black',
                'material' => 'Lambskin Leather',
                'is_unique_piece' => true,
                'quantity_available' => 1,
                'attributes' => ['authenticity' => 'Verified', 'hardware' => 'Gold-tone CC turn-lock'],
            ],
            [
                'name' => 'Hermès Silk Carré Scarf',
                'slug' => 'hermes-silk-carre-scarf',
                'sku' => 'LC-ACC-004',
                'type' => ProductType::Accessory,
                'brand_slug' => 'hermes',
                'category_slug' => 'accessories',
                'price' => 380.00,
                'compare_at_price' => null,
                'condition_grade' => ConditionGrade::Mint,
                'era_decade' => '1980s',
                'size_label' => '90cm',
                'color' => 'Navy & Gold',
                'material' => 'Silk Twill',
                'is_unique_piece' => true,
                'quantity_available' => 1,
                'attributes' => ['artist' => 'Philippe Ledoux', 'hem' => 'Hand-rolled'],
            ],
            [
                'name' => 'Pyrex Primary Colors Mixing Bowls Set',
                'slug' => 'pyrex-primary-colors-mixing-bowls',
                'sku' => 'LC-KIT-005',
                'type' => ProductType::Object,
                'brand_slug' => 'pyrex',
                'category_slug' => 'kitchenware',
                'price' => 95.00,
                'compare_at_price' => 130.00,
                'condition_grade' => ConditionGrade::Good,
                'era_decade' => '1970s',
                'size_label' => 'Set of 4',
                'color' => 'Primary Colors',
                'material' => 'Glass',
                'is_unique_piece' => false,
                'quantity_available' => 2,
                'attributes' => ['pattern' => 'Primary Colors', 'includes' => '4 nesting bowls with lids'],
            ],
            [
                'name' => 'Le Creuset Flame Dutch Oven 5.5qt',
                'slug' => 'le-creuset-flame-dutch-oven',
                'sku' => 'LC-KIT-006',
                'type' => ProductType::Object,
                'brand_slug' => 'le-creuset',
                'category_slug' => 'kitchenware',
                'price' => 275.00,
                'compare_at_price' => 350.00,
                'condition_grade' => ConditionGrade::Excellent,
                'era_decade' => '1990s',
                'size_label' => '5.5 qt',
                'color' => 'Flame Orange',
                'material' => 'Cast Iron Enamel',
                'is_unique_piece' => true,
                'quantity_available' => 1,
                'attributes' => ['made_in' => 'France', 'lid_knob' => 'Phenolic black'],
            ],
            [
                'name' => '1960s Mod Shift Dress',
                'slug' => '1960s-mod-shift-dress',
                'sku' => 'LC-DRS-007',
                'type' => ProductType::Apparel,
                'brand_slug' => 'unknown-vintage',
                'category_slug' => 'dresses',
                'price' => 145.00,
                'compare_at_price' => null,
                'condition_grade' => ConditionGrade::Good,
                'era_decade' => '1960s',
                'size_label' => 'S',
                'color' => 'Geometric Black & White',
                'material' => 'Wool Blend',
                'is_unique_piece' => true,
                'quantity_available' => 1,
                'attributes' => ['style' => 'Mod shift', 'zipper' => 'Back invisible zip'],
            ],
            [
                'name' => 'Vintage Levi\'s Trucker Jacket',
                'slug' => 'vintage-levis-trucker-jacket',
                'sku' => 'LC-DNM-008',
                'type' => ProductType::Apparel,
                'brand_slug' => 'levis',
                'category_slug' => 'denim',
                'price' => 165.00,
                'compare_at_price' => 200.00,
                'condition_grade' => ConditionGrade::Fair,
                'era_decade' => '1980s',
                'size_label' => 'L',
                'color' => 'Light Wash',
                'material' => 'Cotton Denim',
                'is_unique_piece' => true,
                'quantity_available' => 1,
                'attributes' => ['distressing' => 'Natural fade and wear', 'patches' => 'None'],
            ],
            [
                'name' => 'Brass Art Deco Table Lamp',
                'slug' => 'brass-art-deco-table-lamp',
                'sku' => 'LC-OBJ-009',
                'type' => ProductType::Object,
                'brand_slug' => 'unknown-vintage',
                'category_slug' => 'objects-decor',
                'price' => 220.00,
                'compare_at_price' => null,
                'condition_grade' => ConditionGrade::Good,
                'era_decade' => '1930s',
                'size_label' => '18" tall',
                'color' => 'Antique Brass',
                'material' => 'Brass & Glass',
                'is_unique_piece' => true,
                'quantity_available' => 1,
                'attributes' => ['wiring' => 'Updated UL-listed cord', 'shade' => 'Original frosted glass'],
            ],
            [
                'name' => '1980s Oversized Blazer',
                'slug' => '1980s-oversized-blazer',
                'sku' => 'LC-APP-010',
                'type' => ProductType::Apparel,
                'brand_slug' => 'unknown-vintage',
                'category_slug' => 'apparel',
                'price' => 98.00,
                'compare_at_price' => 130.00,
                'condition_grade' => ConditionGrade::Excellent,
                'era_decade' => '1980s',
                'size_label' => 'L',
                'color' => 'Camel',
                'material' => 'Wool',
                'is_unique_piece' => true,
                'quantity_available' => 1,
                'attributes' => ['shoulder_pads' => 'Original intact', 'lining' => 'Silk'],
            ],
            [
                'name' => 'Vintage Ceramic Vase Collection',
                'slug' => 'vintage-ceramic-vase-collection',
                'sku' => 'LC-OBJ-011',
                'type' => ProductType::Object,
                'brand_slug' => 'unknown-vintage',
                'category_slug' => 'objects-decor',
                'price' => 75.00,
                'compare_at_price' => null,
                'condition_grade' => ConditionGrade::Good,
                'era_decade' => '1970s',
                'size_label' => 'Set of 3',
                'color' => 'Earth Tones',
                'material' => 'Ceramic',
                'is_unique_piece' => false,
                'quantity_available' => 3,
                'attributes' => ['origin' => 'West German pottery', 'glaze' => 'Fat lava style'],
            ],
            [
                'name' => '1990s Silk Slip Dress',
                'slug' => '1990s-silk-slip-dress',
                'sku' => 'LC-DRS-012',
                'type' => ProductType::Apparel,
                'brand_slug' => 'unknown-vintage',
                'category_slug' => 'dresses',
                'price' => 155.00,
                'compare_at_price' => 195.00,
                'condition_grade' => ConditionGrade::Mint,
                'era_decade' => '1990s',
                'size_label' => 'S',
                'color' => 'Champagne',
                'material' => 'Silk',
                'is_unique_piece' => true,
                'quantity_available' => 1,
                'attributes' => ['style' => 'Bias-cut slip', 'occasion' => 'Evening'],
            ],
        ];

        foreach ($products as $index => $data) {
            $attributes = $data['attributes'];
            $brandSlug = $data['brand_slug'];
            $categorySlug = $data['category_slug'];
            unset($data['attributes'], $data['brand_slug'], $data['category_slug']);

            $product = Product::query()->create([
                ...$data,
                'brand_id' => $brands[$brandSlug] ?? $brands->first(),
                'category_id' => $categories[$categorySlug] ?? $categories->first(),
                'status' => ProductStatus::Published,
                'short_description' => 'Curated vintage piece from the Le Cameleon collection.',
                'quantity_reserved' => 0,
                'low_stock_threshold' => 1,
                'published_at' => now()->subDays(rand(1, 30)),
                'measurements' => $data['measurements'] ?? null,
            ]);

            $primaryImagePath = $index === 10
                ? 'placeholders/vintage-product.jpg'
                : 'products/'.$product->slug.'/main.jpg';

            ProductImage::query()->create([
                'product_id' => $product->id,
                'path' => $primaryImagePath,
                'alt' => $product->name,
                'sort_order' => 0,
                'is_primary' => true,
            ]);

            ProductImage::query()->create([
                'product_id' => $product->id,
                'path' => 'products/'.$product->slug.'/detail-'.($index + 1).'.jpg',
                'alt' => $product->name.' detail view',
                'sort_order' => 1,
                'is_primary' => false,
            ]);

            foreach ($attributes as $key => $value) {
                ProductAttribute::query()->create([
                    'product_id' => $product->id,
                    'key' => $key,
                    'value' => $value,
                ]);
            }
        }

        $featuredProduct = Product::query()->where('slug', '1970s-floral-maxi-dress')->first();
        $demoCustomer = User::query()->where('email', 'customer@lecameleon.store')->first();

        if ($featuredProduct && $demoCustomer) {
            Review::query()->create([
                'product_id' => $featuredProduct->id,
                'user_id' => $demoCustomer->id,
                'rating' => 5,
                'title' => 'Beautiful drape and true vintage fit',
                'body' => 'The measurements matched the listing and the condition was exactly as described.',
                'is_approved' => true,
            ]);
        }
    }
}
