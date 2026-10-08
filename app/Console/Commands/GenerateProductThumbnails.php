<?php

namespace App\Console\Commands;

use App\Models\ProductImage;
use App\Services\ProductThumbnailService;
use Illuminate\Console\Command;

class GenerateProductThumbnails extends Command
{
    protected $signature = 'products:thumbnails';

    protected $description = 'Genera miniaturas y guarda dimensiones de imágenes de productos';

    public function handle(ProductThumbnailService $thumbnailService): int
    {
        $processed = 0;

        ProductImage::query()->chunkById(100, function ($images) use ($thumbnailService, &$processed): void {
            foreach ($images as $image) {
                $dimensions = $thumbnailService->dimensions($image);
                if ($dimensions !== null && (! $image->width || ! $image->height)) {
                    $image->forceFill($dimensions)->save();
                }

                $thumbnailService->generate($image);
                $processed++;
            }
        });

        $this->info("Imágenes procesadas: {$processed}.");

        return self::SUCCESS;
    }
}
