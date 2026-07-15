<?php

namespace Tests\Feature;

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CatalogFilterTest extends TestCase
{
    use RefreshDatabase;

    public function test_published_products_appear_on_the_shop_page(): void
    {
        $published = Product::factory()->create([
            'name' => 'Published Vintage Wool Coat',
        ]);

        $response = $this->get(route('shop.index'));

        $response->assertOk();
        $response->assertSee($published->name, false);
    }

    public function test_draft_products_do_not_appear_on_the_shop_page(): void
    {
        $draft = Product::factory()->draft()->create([
            'name' => 'Draft Unreleased Silk Gown',
        ]);

        Product::factory()->create([
            'name' => 'Visible Shop Listing Piece',
        ]);

        $response = $this->get(route('shop.index'));

        $response->assertOk();
        $response->assertDontSee($draft->name, false);
    }
}
