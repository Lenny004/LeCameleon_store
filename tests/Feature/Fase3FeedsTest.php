<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class Fase3FeedsTest extends TestCase
{
    use RefreshDatabase;

    public function test_feeds_include_only_published_products_with_stock_and_csv_headers(): void
    {
        $available = Product::factory()->create(['slug' => 'feed-available', 'quantity_available' => 2, 'quantity_reserved' => 0]);
        Product::factory()->soldOut()->create(['slug' => 'feed-sold-out']);
        Product::factory()->draft()->create(['slug' => 'feed-draft']);

        $this->get(route('feeds.google'))->assertOk()->assertSee($available->name)->assertDontSee('feed-sold-out')->assertDontSee('feed-draft');
        $this->get(route('feeds.meta'))->assertOk()->assertSee('id,title,description,availability,condition,price,link,image_link,brand,google_product_category,item_group_id', false);
        config(['store.feeds.token' => 'secret']);
        $this->get(route('feeds.google'))->assertNotFound();
        $this->get(route('feeds.google', ['token' => 'secret']))->assertOk();
    }

    public function test_feed_formats_price_absolute_image_and_plain_description_and_can_be_disabled(): void
    {
        Cache::flush();
        $product = Product::factory()->create([
            'price' => 12.34,
            'description' => '<strong>Descripción</strong> limpia',
        ]);
        ProductImage::query()->create(['product_id' => $product->id, 'path' => 'missing.jpg']);

        $response = $this->get(route('feeds.meta'));

        $response->assertOk()
            ->assertSee('12.34 USD', false)
            ->assertSee('http://', false)
            ->assertSee('Descripción limpia', false)
            ->assertDontSee('<strong>', false);

        config(['store.feeds.enabled' => false]);
        $this->get(route('feeds.google'))->assertNotFound();
    }

    public function test_product_save_invalidates_feed_cache(): void
    {
        Cache::flush();
        $first = Product::factory()->create(['name' => 'Primera pieza']);
        $this->get(route('feeds.google'))->assertSee($first->name, false);

        $second = Product::factory()->create(['name' => 'Segunda pieza']);
        $this->get(route('feeds.google'))->assertSee($second->name, false);
    }
}
