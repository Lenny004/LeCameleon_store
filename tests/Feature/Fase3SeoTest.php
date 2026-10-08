<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Payment;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class Fase3SeoTest extends TestCase
{
    use RefreshDatabase;

    public function test_product_has_canonical_og_and_decodable_used_product_schema(): void
    {
        $product = Product::factory()->create(['slug' => 'seo-piece']);
        $response = $this->get(route('shop.show', $product->slug));

        $response->assertOk()->assertSee('rel="canonical"', false)->assertSee('property="og:image"', false)->assertSee('https://schema.org/UsedCondition');
        $this->assertStringContainsString('application/ld+json', $response->getContent());
    }

    public function test_product_schema_contains_offer_and_only_approved_rating(): void
    {
        $product = Product::factory()->create(['slug' => 'schema-piece', 'price' => 123.45]);
        $product->reviews()->create([
            'user_id' => User::factory()->create()->id,
            'rating' => 5,
            'body' => 'Aprobada',
            'is_approved' => true,
        ]);
        $product->reviews()->create([
            'user_id' => User::factory()->create()->id,
            'rating' => 1,
            'body' => 'Pendiente',
            'is_approved' => false,
        ]);

        $content = $this->get(route('shop.show', $product->slug))->getContent();
        preg_match('/<script type="application\\/ld\\+json"[^>]*>(.*?)<\\/script>/s', $content, $matches);
        $schema = json_decode($matches[1] ?? '', true);

        $this->assertSame('https://schema.org/UsedCondition', $schema['itemCondition']);
        $this->assertSame('123.45', $schema['offers']['price']);
        $this->assertSame('https://schema.org/InStock', $schema['offers']['availability']);
        $this->assertSame(1, $schema['aggregateRating']['reviewCount']);
    }

    public function test_noindex_covers_private_routes_and_shop_filters_but_not_plain_shop(): void
    {
        $this->get(route('login'))->assertSee('noindex,follow', false);
        $this->get(route('cart.index'))->assertSee('noindex,follow', false);
        $product = Product::factory()->create();
        $customer = User::factory()->create();
        $this->actingAs($customer)->post(route('cart.store'), ['product_id' => $product->id, 'quantity' => 1]);
        $this->actingAs($customer)->get(route('checkout.index'))->assertSee('noindex,follow', false);
        $this->get(route('shop.index'))->assertDontSee('noindex,follow', false);
        $this->get(route('shop.index', ['color' => 'azul']))->assertSee('noindex,follow', false);

        $staff = User::factory()->staff()->create();
        $order = Order::factory()->for($staff)->create();
        Payment::query()->create([
            'order_id' => $order->id,
            'provider' => 'transfer',
            'status' => 'pending',
            'amount' => $order->grand_total,
        ]);
        $this->actingAs($staff)->get(route('checkout.success', $order))->assertSee('noindex,follow', false);
        $this->get(URL::signedRoute('checkout.receipts.create', ['order' => $order]))->assertSee('noindex,follow', false);
        $this->actingAs($staff)->get(route('verification.notice'))->assertSee('noindex,follow', false);
        $this->actingAs($staff)->get(route('two-factor.challenge'))->assertSee('noindex,follow', false);
    }

    public function test_canonical_keeps_only_page_query_and_home_exposes_organization(): void
    {
        $response = $this->get(route('shop.index', ['color' => 'azul', 'page' => 2]));

        $response->assertSee('rel="canonical" href="'.route('shop.index', ['page' => 2]).'"', false);
        $this->get(route('home'))->assertSee('"@type":"Organization"', false);
    }
}
