<?php

namespace Tests\Unit;

use App\Models\ProductImage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProductImageUrlTest extends TestCase
{
    use RefreshDatabase;

    public function test_url_returns_storage_url_for_existing_file(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('products/test/photo.jpg', 'binary');

        $image = new ProductImage(['path' => 'products/test/photo.jpg']);

        $this->assertSame(
            Storage::disk('public')->url('products/test/photo.jpg'),
            $image->url()
        );
    }

    public function test_url_falls_back_to_placeholder_when_path_missing(): void
    {
        Storage::fake('public');

        $image = new ProductImage(['path' => 'products/missing/photo.jpg']);

        $this->assertSame(ProductImage::placeholderUrl(), $image->url());
    }

    public function test_url_returns_absolute_paths_unchanged(): void
    {
        $image = new ProductImage(['path' => 'https://cdn.example.com/photo.jpg']);

        $this->assertSame('https://cdn.example.com/photo.jpg', $image->url());
    }
}
