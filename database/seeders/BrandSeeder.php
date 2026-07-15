<?php

namespace Database\Seeders;

use App\Models\Brand;
use Illuminate\Database\Seeder;

class BrandSeeder extends Seeder
{
    public function run(): void
    {
        $brands = [
            ['name' => 'Chanel', 'slug' => 'chanel', 'description' => 'French luxury fashion house founded in 1910.'],
            ['name' => 'Levi\'s', 'slug' => 'levis', 'description' => 'American denim and casualwear icon since 1853.'],
            ['name' => 'Hermès', 'slug' => 'hermes', 'description' => 'Parisian maison known for leather goods and silk.'],
            ['name' => 'Pyrex', 'slug' => 'pyrex', 'description' => 'Vintage American glassware and kitchen collectibles.'],
            ['name' => 'Le Creuset', 'slug' => 'le-creuset', 'description' => 'French cast iron cookware in signature enamel colors.'],
            ['name' => 'Unknown Vintage', 'slug' => 'unknown-vintage', 'description' => 'Unlabeled or artisan vintage pieces.'],
        ];

        foreach ($brands as $brand) {
            Brand::query()->create([
                ...$brand,
                'logo_path' => 'brands/'. $brand['slug'] .'.png',
            ]);
        }
    }
}
