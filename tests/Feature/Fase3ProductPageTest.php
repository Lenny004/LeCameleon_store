<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class Fase3ProductPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_size_guide_and_sitemap_taxonomies_are_public(): void
    {
        $product = Product::factory()->create(['slug' => 'pieza-guia']);

        $this->get(route('size-guide'))->assertOk()->assertSee('Cómo medir una prenda');
        $this->get(route('sitemap'))->assertOk()->assertSee($product->category->slug)->assertSee($product->brand->slug);
    }

    public function test_unique_badge_and_gallery_alt_text_are_present(): void
    {
        Storage::fake('public');
        $product = Product::factory()->uniquePiece()->create(['slug' => 'pieza-unica']);
        ProductImage::query()->create(['product_id' => $product->id, 'path' => 'products/piece.jpg', 'sort_order' => 0, 'is_primary' => true]);

        $response = $this->get(route('shop.show', $product->slug));

        $response->assertOk()->assertSee('Pieza única')->assertSee('foto 1 de 1');
    }

    public function test_unique_badge_is_hidden_for_regular_products_and_size_guide_is_linked(): void
    {
        $product = Product::factory()->create(['is_unique_piece' => false, 'quantity_available' => 5]);

        $this->get(route('shop.show', $product->slug))
            ->assertOk()
            ->assertDontSee('Pieza Ãºnica')
            ->assertSee(route('size-guide'), false);

        $this->get(route('home'))
            ->assertOk()
            ->assertSee(route('size-guide'), false);
    }
}
