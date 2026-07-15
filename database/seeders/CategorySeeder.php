<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $apparel = Category::query()->create([
            'name' => 'Apparel',
            'slug' => 'apparel',
            'description' => 'Vintage clothing and wearable fashion.',
            'image_path' => 'categories/apparel.jpg',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $objects = Category::query()->create([
            'name' => 'Objects & Decor',
            'slug' => 'objects-decor',
            'description' => 'Home decor, ceramics, and collectible objects.',
            'image_path' => 'categories/objects.jpg',
            'is_active' => true,
            'sort_order' => 2,
        ]);

        $accessories = Category::query()->create([
            'name' => 'Accessories',
            'slug' => 'accessories',
            'description' => 'Bags, scarves, jewelry, and small leather goods.',
            'image_path' => 'categories/accessories.jpg',
            'is_active' => true,
            'sort_order' => 3,
        ]);

        Category::query()->create([
            'parent_id' => $apparel->id,
            'name' => 'Dresses',
            'slug' => 'dresses',
            'description' => 'Vintage dresses from every era.',
            'image_path' => 'categories/dresses.jpg',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        Category::query()->create([
            'parent_id' => $apparel->id,
            'name' => 'Denim',
            'slug' => 'denim',
            'description' => 'Vintage jeans, jackets, and denim pieces.',
            'image_path' => 'categories/denim.jpg',
            'is_active' => true,
            'sort_order' => 2,
        ]);

        Category::query()->create([
            'parent_id' => $objects->id,
            'name' => 'Kitchenware',
            'slug' => 'kitchenware',
            'description' => 'Vintage pots, glassware, and kitchen collectibles.',
            'image_path' => 'categories/kitchenware.jpg',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        Category::query()->create([
            'parent_id' => $accessories->id,
            'name' => 'Handbags',
            'slug' => 'handbags',
            'description' => 'Designer and vintage handbags.',
            'image_path' => 'categories/handbags.jpg',
            'is_active' => true,
            'sort_order' => 1,
        ]);
    }
}
