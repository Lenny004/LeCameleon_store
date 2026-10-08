<?php

namespace Tests\Feature;

use App\Enums\OfferStatus;
use App\Enums\ProductStatus;
use App\Mail\OfferReceived;
use App\Models\Offer;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class OfferTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_can_submit_offer_below_list_price(): void
    {
        Mail::fake();

        $customer = User::factory()->customer()->create();
        $product = Product::factory()->uniquePiece()->create([
            'status' => ProductStatus::Published,
            'price' => 150.00,
            'quantity_available' => 1,
            'quantity_reserved' => 0,
            'published_at' => now(),
        ]);

        $response = $this->actingAs($customer)->post(route('shop.offers.store', $product), [
            'amount' => 120.00,
            'message' => 'Me encanta esta pieza.',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('offers', [
            'product_id' => $product->id,
            'user_id' => $customer->id,
            'amount' => '120.00',
            'status' => OfferStatus::Pending->value,
        ]);

        $offer = Offer::query()->first();
        Mail::assertSent(OfferReceived::class, function (OfferReceived $mail) use ($offer): bool {
            return $mail->offer->is($offer);
        });
    }

    public function test_offer_at_or_above_list_price_is_rejected(): void
    {
        $customer = User::factory()->customer()->create();
        $product = Product::factory()->uniquePiece()->create([
            'status' => ProductStatus::Published,
            'price' => 100.00,
            'quantity_available' => 1,
            'published_at' => now(),
        ]);

        $response = $this->actingAs($customer)->post(route('shop.offers.store', $product), [
            'amount' => 100.00,
        ]);

        $response->assertSessionHasErrors('amount');
        $this->assertDatabaseCount('offers', 0);
    }

    public function test_guest_cannot_submit_offer(): void
    {
        $product = Product::factory()->uniquePiece()->create([
            'status' => ProductStatus::Published,
            'price' => 80.00,
            'quantity_available' => 1,
            'published_at' => now(),
        ]);

        $response = $this->post(route('shop.offers.store', $product), [
            'amount' => 60.00,
        ]);

        $response->assertRedirect(route('login'));
        $this->assertDatabaseCount('offers', 0);
    }
}
