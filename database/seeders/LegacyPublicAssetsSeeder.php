<?php

namespace Database\Seeders;

use App\Support\LegacyAssetCopier;
use Illuminate\Database\Seeder;

class LegacyPublicAssetsSeeder extends Seeder
{
    public function run(): void
    {
        $brandAssets = [
            // Width-aware logo set (recursos/img)
            'recursos/img/logo_icon.png' => 'brand/logo-icon.png',   // compact / stacked
            'recursos/img/logo.png' => 'brand/logo.png',             // horizontal
            'recursos/img/logo3.png' => 'brand/logo-wide.png',       // widest
        ];

        foreach ($brandAssets as $legacy => $dest) {
            LegacyAssetCopier::copyToPublicImages($legacy, $dest);
        }

        $marketingAssets = [
            'recursos/img/decoracion/decoracion_vintage.jpg' => 'marketing/decoracion-vintage.jpg',
            'recursos/img/artesanales/artesanias_vintage.jpg' => 'marketing/banner-collection.jpg',
            'recursos/img/categorias/categorias.jpg' => 'marketing/categorias.jpg',
            'recursos/img/categorias/accesorios.jpg' => 'marketing/accesorios.jpg',
            'recursos/img/categorias/interior.jpg' => 'marketing/interior.jpg',
            'recursos/img/categorias/exteriores.jpg' => 'marketing/exteriores.jpg',
        ];

        foreach ($marketingAssets as $legacy => $dest) {
            LegacyAssetCopier::copyToPublicImages($legacy, $dest);
        }

        $placeholderSource = LegacyAssetCopier::listFiles('api/images/productos')[0] ?? null;

        if ($placeholderSource !== null) {
            $extension = strtolower(pathinfo($placeholderSource, PATHINFO_EXTENSION) ?: 'jpg');
            LegacyAssetCopier::copyAbsoluteToPublicDisk(
                $placeholderSource,
                'placeholders/vintage-product.'.$extension,
            );

            if ($extension !== 'jpg') {
                LegacyAssetCopier::copyAbsoluteToPublicDisk(
                    $placeholderSource,
                    'placeholders/vintage-product.jpg',
                );
            }
        }
    }
}
