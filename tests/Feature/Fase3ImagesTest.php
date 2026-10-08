<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\ProductImage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class Fase3ImagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_thumb_accessor_falls_back_to_original_and_uses_existing_thumb(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('products/piece.jpg', 'original');
        $image = new ProductImage(['path' => 'products/piece.jpg']);
        $this->assertSame(Storage::disk('public')->url('products/piece.jpg'), $image->thumbUrl());

        Storage::disk('public')->put('products/piece_thumb.jpg', 'thumb');
        $this->assertSame(Storage::disk('public')->url('products/piece_thumb.jpg'), $image->thumbUrl());
    }

    public function test_thumbnail_command_processes_existing_images(): void
    {
        Storage::fake('public');
        $jpeg = base64_decode('/9j/4AAQSkZJRgABAQAAAQABAAD/2wBDAP//////////////////////////////////////////////////////////////////////////////////////2wBDAf//////////////////////////////////////////////////////////////////////////////////////wAARCAABAAEDASIAAhEBAxEB/8QAFQABAQAAAAAAAAAAAAAAAAAAAAX/xAAUEAEAAAAAAAAAAAAAAAAAAAAA/9oADAMBAAIQAxAAAAH/AP/EABQQAQAAAAAAAAAAAAAAAAAAAAD/2gAIAQEAAQUCif/EABQRAQAAAAAAAAAAAAAAAAAAAAD/2gAIAQMBAT8Bf//EABQRAQAAAAAAAAAAAAAAAAAAAAD/2gAIAQIBAT8Bf//EABQQAQAAAAAAAAAAAAAAAAAAAAD/2gAIAQEABj8Cf//Z', true);
        Storage::disk('public')->put('products/existing.jpg', $jpeg);
        ProductImage::query()->create(['product_id' => Product::factory()->create()->id, 'path' => 'products/existing.jpg']);

        $this->artisan('products:thumbnails')->assertExitCode(0);
        $this->assertDatabaseHas('product_images', ['path' => 'products/existing.jpg', 'width' => 1, 'height' => 1]);
    }

    public function test_srcset_uses_original_dimensions_without_reading_thumbnail_dimensions(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('products/piece.jpg', 'original');
        Storage::disk('public')->put('products/piece_thumb.jpg', 'thumb');
        $image = new ProductImage(['path' => 'products/piece.jpg', 'width' => 800, 'height' => 600]);

        $srcset = $image->srcset();

        $this->assertSame(2, substr_count($srcset, 'w'));
        $this->assertStringContainsString('400w', $srcset);
        $this->assertStringContainsString('800w', $srcset);

        Storage::disk('public')->delete('products/piece_thumb.jpg');
        $this->assertSame($image->url(), $image->srcset());
    }

    public function test_staff_upload_stores_dimensions_and_thumbnail(): void
    {
        Storage::fake('public');
        $product = Product::factory()->create();

        $staff = User::factory()->staff()->create(['two_factor_confirmed_at' => now()]);
        $this->withSession(['two_factor_passed' => true])->actingAs($staff)
            ->put(route('admin.products.update', $product), [
                'name' => $product->name,
                'slug' => $product->slug,
                'sku' => $product->sku,
                'type' => $product->type->value,
                'status' => $product->status->value,
                'price' => $product->price,
                'condition_grade' => $product->condition_grade->value,
                'quantity_available' => $product->quantity_available,
                'brand_id' => $product->brand_id,
                'category_id' => $product->category_id,
                'images' => [UploadedFile::fake()->image('a.jpg', 800, 600)],
            ])
            ->assertRedirect();

        $image = ProductImage::query()->where('product_id', $product->id)->firstOrFail();
        $this->assertSame(800, $image->width);
        $this->assertSame(600, $image->height);
        Storage::disk('public')->assertExists($image->path);
        Storage::disk('public')->assertExists($image->thumbPath());
    }
}
