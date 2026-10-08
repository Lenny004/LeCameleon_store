<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Product;
use App\Models\Review;
use App\Models\User;
use App\Services\ReviewService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class Fase3ReviewsTest extends TestCase
{
    use RefreshDatabase;

    public function test_only_a_delivered_buyer_can_review_and_review_is_verified(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create(['slug' => 'review-piece']);
        $this->assertFalse(app(ReviewService::class)->canReview($user, $product));
        $order = Order::factory()->for($user)->delivered()->create();
        $order->items()->create(['product_id' => $product->id, 'name' => $product->name, 'sku' => $product->sku, 'quantity' => 1, 'unit_price' => $product->price, 'line_total' => $product->price]);

        $this->assertTrue(app(ReviewService::class)->canReview($user, $product));
        $this->actingAs($user)->post(route('shop.reviews.store', $product), ['rating' => 5, 'body' => 'Una pieza excelente'])->assertRedirect();
        $this->assertDatabaseHas('reviews', ['product_id' => $product->id, 'is_verified_purchase' => true]);
    }

    public function test_store_reply_is_visible_on_public_page(): void
    {
        $review = Review::factory()->create(['is_approved' => true, 'store_reply' => 'Gracias por tu compra.']);
        $this->get(route('shop.show', $review->product->slug))->assertSee('Respuesta de Le Cameleon');
    }

    public function test_paid_order_does_not_enable_review_and_customer_cannot_post_without_delivery(): void
    {
        $user = User::factory()->customer()->create();
        $product = Product::factory()->create();
        $order = Order::factory()->for($user)->paid()->create();
        $order->items()->create([
            'product_id' => $product->id,
            'name' => $product->name,
            'sku' => $product->sku,
            'quantity' => 1,
            'unit_price' => $product->price,
            'line_total' => $product->price,
        ]);

        $this->assertFalse(app(ReviewService::class)->canReview($user, $product));
        $this->actingAs($user)
            ->post(route('shop.reviews.store', $product), ['rating' => 5, 'body' => 'Todavía no'])
            ->assertForbidden()
            ->assertSee('Solo quienes compraron esta pieza pueden rese', false);
    }

    public function test_staff_can_create_edit_and_delete_store_reply_but_customer_cannot_use_admin_route(): void
    {
        $review = Review::factory()->create();
        $staff = User::factory()->staff()->create();

        $this->actingAs($staff)
            ->patch(route('admin.reviews.reply', $review), ['store_reply' => 'Respuesta inicial'])
            ->assertRedirect();
        $this->assertSame('Respuesta inicial', $review->fresh()->store_reply);

        $this->actingAs($staff)
            ->patch(route('admin.reviews.reply', $review), ['store_reply' => 'Respuesta editada'])
            ->assertRedirect();
        $this->assertSame('Respuesta editada', $review->fresh()->store_reply);

        $this->actingAs($staff)
            ->patch(route('admin.reviews.reply', $review), ['store_reply' => ''])
            ->assertRedirect();
        $this->assertNull($review->fresh()->store_reply);

        $this->actingAs(User::factory()->customer()->create())
            ->patch(route('admin.reviews.reply', $review), ['store_reply' => 'No'])
            ->assertForbidden();
    }
}
