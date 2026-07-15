<?php

namespace Tests\Feature;

use App\Models\Brand;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CatalogSearchTest extends TestCase
{
    use RefreshDatabase;

    public function test_multi_term_search_matches_across_fields(): void
    {
        $match = Product::factory()->create([
            'name' => 'Vintage Denim Trucker',
            'description' => 'Classic blue jacket from the 1990s.',
        ]);

        Product::factory()->create([
            'name' => 'Denim Overalls',
            'description' => 'Wide-leg denim piece with adjustable straps.',
        ]);

        Product::factory()->create([
            'name' => 'Leather Biker Coat',
            'description' => 'Black leather outerwear, no denim.',
        ]);

        $response = $this->get(route('search', ['q' => 'denim jacket']));

        $response->assertOk();
        $response->assertSee($match->name, false);
        $response->assertDontSee('Denim Overalls', false);
        $response->assertDontSee('Leather Biker Coat', false);
    }

    public function test_empty_search_query_lists_all_published_products(): void
    {
        $published = Product::factory()->create([
            'name' => 'Shop Listing Without Query',
        ]);

        Product::factory()->draft()->create([
            'name' => 'Hidden Draft Piece',
        ]);

        $response = $this->get(route('search'));

        $response->assertOk();
        $response->assertSee($published->name, false);
        $response->assertDontSee('Hidden Draft Piece', false);
    }

    public function test_search_api_returns_suggestions_for_matching_query(): void
    {
        $brand = Brand::factory()->create(['name' => 'Levi Strauss']);

        $product = Product::factory()->create([
            'brand_id' => $brand->id,
            'name' => 'Denim Chore Coat',
            'slug' => 'denim-chore-coat',
            'price' => 89.50,
            'description' => 'Heavy jacket weight denim.',
        ]);

        $response = $this->getJson(route('api.search', ['q' => 'denim jacket']));

        $response->assertOk();
        $response->assertJsonPath('suggestions.0.slug', $product->slug);
        $response->assertJsonPath('suggestions.0.name', $product->name);
        $response->assertJsonPath('suggestions.0.price', '89.50');
    }

    public function test_search_api_returns_empty_suggestions_without_query(): void
    {
        Product::factory()->create(['name' => 'Visible Catalog Item']);

        $response = $this->getJson(route('api.search'));

        $response->assertOk();
        $response->assertJsonPath('suggestions', []);
    }
}
