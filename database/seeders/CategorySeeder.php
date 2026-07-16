<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Support\LegacyAssetCopier;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * @var array<string, string>
     */
    private array $legacyCategoryImages = [
        'apparel' => 'recursos/img/accesorios/playeras_femeninas/playera_femenina.jpg',
        'objects-decor' => 'recursos/img/decoracion/decoracion_vintage.jpg',
        'accessories' => 'recursos/img/categorias/accesorios.jpg',
        'dresses' => 'recursos/img/categorias/interior.jpg',
        'denim' => 'recursos/img/categorias/exteriores.jpg',
        'kitchenware' => 'recursos/img/decoracion/cocina/horno_3.png',
        'handbags' => 'recursos/img/accesorios/collares/collares.jpg',
    ];

    public function run(): void
    {
        $apparel = $this->createCategory([
            'name' => 'Apparel',
            'slug' => 'apparel',
            'description' => 'Vintage clothing and wearable fashion.',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $objects = $this->createCategory([
            'name' => 'Objects & Decor',
            'slug' => 'objects-decor',
            'description' => 'Home decor, ceramics, and collectible objects.',
            'is_active' => true,
            'sort_order' => 2,
        ]);

        $accessories = $this->createCategory([
            'name' => 'Accessories',
            'slug' => 'accessories',
            'description' => 'Bags, scarves, jewelry, and small leather goods.',
            'is_active' => true,
            'sort_order' => 3,
        ]);

        $this->createCategory([
            'parent_id' => $apparel->id,
            'name' => 'Dresses',
            'slug' => 'dresses',
            'description' => 'Vintage dresses from every era.',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $this->createCategory([
            'parent_id' => $apparel->id,
            'name' => 'Denim',
            'slug' => 'denim',
            'description' => 'Vintage jeans, jackets, and denim pieces.',
            'is_active' => true,
            'sort_order' => 2,
        ]);

        $this->createCategory([
            'parent_id' => $objects->id,
            'name' => 'Kitchenware',
            'slug' => 'kitchenware',
            'description' => 'Vintage pots, glassware, and kitchen collectibles.',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $this->createCategory([
            'parent_id' => $accessories->id,
            'name' => 'Handbags',
            'slug' => 'handbags',
            'description' => 'Designer and vintage handbags.',
            'is_active' => true,
            'sort_order' => 1,
        ]);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private function createCategory(array $data): Category
    {
        $slug = (string) $data['slug'];
        $imagePath = $this->seedCategoryImage($slug);

        return Category::query()->create([
            ...$data,
            'image_path' => $imagePath,
        ]);
    }

    private function seedCategoryImage(string $slug): string
    {
        $legacyRelative = $this->legacyCategoryImages[$slug] ?? 'recursos/img/categorias/categorias.jpg';
        $extension = strtolower(pathinfo($legacyRelative, PATHINFO_EXTENSION) ?: 'jpg');
        $destPath = 'categories/'.$slug.'.'.$extension;

        return LegacyAssetCopier::copyToPublicDisk($legacyRelative, $destPath) ?? $destPath;
    }
}
