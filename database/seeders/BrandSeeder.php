<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Support\LegacyAssetCopier;
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

        $legacyLogos = LegacyAssetCopier::listFiles('api/images/marca');

        foreach ($brands as $index => $brand) {
            $logoPath = 'brands/'.$brand['slug'].'.png';
            $legacyLogo = $legacyLogos[$index % max(count($legacyLogos), 1)] ?? null;

            if ($legacyLogo !== null) {
                $extension = strtolower(pathinfo($legacyLogo, PATHINFO_EXTENSION) ?: 'png');
                $logoPath = 'brands/'.$brand['slug'].'.'.$extension;
                LegacyAssetCopier::copyAbsoluteToPublicDisk($legacyLogo, $logoPath);
            }

            Brand::query()->create([
                ...$brand,
                'logo_path' => $logoPath,
            ]);
        }
    }
}
