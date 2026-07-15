<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\Review;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductReviewTest extends TestCase
{
    use RefreshDatabase;

    public function test_product_page_shows_approved_reviews_and_summary(): void
    {
        $product = Product::factory()->create([
            'name' => 'Rated Vintage Jacket',
            'slug' => 'rated-vintage-jacket',
        ]);

        Review::factory()->approved()->create([
            'product_id' => $product->id,
            'rating' => 5,
            'title' => 'Excelente chaqueta',
            'body' => 'Tal cual la descripción.',
        ]);

        Review::factory()->approved()->create([
            'product_id' => $product->id,
            'rating' => 3,
            'title' => 'Regular',
            'body' => 'Está bien, nada más.',
        ]);

        Review::factory()->pending()->create([
            'product_id' => $product->id,
            'rating' => 1,
            'title' => 'Pendiente',
            'body' => 'No debería verse aún.',
        ]);

        $response = $this->get(route('shop.show', $product->slug));

        $response->assertOk();
        $response->assertSee('Valoraciones y comentarios', false);
        $response->assertSee('Excelente chaqueta', false);
        $response->assertSee('Regular', false);
        $response->assertDontSee('No debería verse aún.', false);
        $response->assertSee('4.0', false);
    }

    public function test_product_reviews_can_be_filtered_by_rating(): void
    {
        $product = Product::factory()->create([
            'slug' => 'filterable-reviews-piece',
        ]);

        Review::factory()->approved()->create([
            'product_id' => $product->id,
            'rating' => 5,
            'title' => 'Cinco estrellas',
        ]);

        Review::factory()->approved()->create([
            'product_id' => $product->id,
            'rating' => 2,
            'title' => 'Dos estrellas',
        ]);

        $response = $this->get(route('shop.show', [
            'slug' => $product->slug,
            'review_rating' => 5,
        ]));

        $response->assertOk();
        $response->assertSee('Cinco estrellas', false);
        $response->assertDontSee('Dos estrellas', false);
    }

    public function test_shop_can_filter_products_by_minimum_rating(): void
    {
        $high = Product::factory()->create(['name' => 'High Rated Coat']);
        $low = Product::factory()->create(['name' => 'Low Rated Scarf']);

        Review::factory()->approved()->create([
            'product_id' => $high->id,
            'rating' => 5,
        ]);

        Review::factory()->approved()->create([
            'product_id' => $low->id,
            'rating' => 2,
        ]);

        $response = $this->get(route('shop.index', ['min_rating' => 4]));

        $response->assertOk();
        $response->assertSee('High Rated Coat', false);
        $response->assertDontSee('Low Rated Scarf', false);
    }

    public function test_authenticated_customer_can_submit_a_review(): void
    {
        $customer = User::factory()->create();
        $product = Product::factory()->create();

        $response = $this->actingAs($customer)->post(route('shop.reviews.store', $product), [
            'rating' => 4,
            'title' => 'Muy buena',
            'body' => 'Me encantó la calidad de la pieza.',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('reviews', [
            'product_id' => $product->id,
            'user_id' => $customer->id,
            'rating' => 4,
            'is_approved' => false,
        ]);
    }
}
