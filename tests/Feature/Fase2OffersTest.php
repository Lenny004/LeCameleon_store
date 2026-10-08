<?php

namespace Tests\Feature;

use App\Enums\OfferStatus;
use App\Jobs\ExpireOffers;
use App\Jobs\OfferExpiringSoon;
use App\Mail\OfferExpiringSoon as OfferExpiringSoonMail;
use App\Models\Offer;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Services\OfferPricingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class Fase2OffersTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_can_accept_or_decline_a_counteroffer(): void
    {
        $user = User::factory()->customer()->create();
        $product = Product::factory()->create();
        $offer = Offer::create([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'amount' => 50,
            'counter_amount' => 70,
            'status' => OfferStatus::Countered,
            'expires_at' => now()->addDay(),
        ]);

        $this->actingAs($user)->patch(route('account.offers.accept', $offer))->assertRedirect();
        $this->assertDatabaseHas('offers', ['id' => $offer->id, 'status' => 'accepted', 'accepted_amount' => 70]);

        $second = Offer::create([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'amount' => 55,
            'counter_amount' => 75,
            'status' => OfferStatus::Countered,
            'expires_at' => now()->addDay(),
        ]);
        $this->actingAs($user)->patch(route('account.offers.decline', $second))->assertRedirect();
        $this->assertDatabaseHas('offers', ['id' => $second->id, 'status' => 'declined']);
    }

    public function test_expired_or_used_offer_does_not_change_price(): void
    {
        $user = User::factory()->customer()->create();
        $product = Product::factory()->create(['price' => 100]);
        Offer::create(['user_id' => $user->id, 'product_id' => $product->id, 'amount' => 70, 'accepted_amount' => 70, 'status' => OfferStatus::Accepted, 'expires_at' => now()->subMinute()]);
        $used = Offer::create(['user_id' => $user->id, 'product_id' => $product->id, 'amount' => 60, 'accepted_amount' => 60, 'status' => OfferStatus::Accepted, 'expires_at' => now()->addDay(), 'order_id' => Order::factory()->create(['user_id' => $user->id])->id]);

        $this->assertSame(100.0, app(OfferPricingService::class)->effectivePriceFor($user, $product));
        $this->assertNull(app(OfferPricingService::class)->offerFor($user, $product));
        $this->assertNotNull($used->fresh()->order_id);
    }

    public function test_jobs_expire_offers_and_notify_only_once(): void
    {
        Mail::fake();
        $user = User::factory()->customer()->create();
        $product = Product::factory()->create();
        $offer = Offer::create(['user_id' => $user->id, 'product_id' => $product->id, 'amount' => 50, 'status' => OfferStatus::Accepted, 'expires_at' => now()->subMinute()]);
        (new ExpireOffers)->handle();
        $this->assertDatabaseHas('offers', ['id' => $offer->id, 'status' => 'expired']);

        $soon = Offer::create(['user_id' => $user->id, 'product_id' => $product->id, 'amount' => 55, 'status' => OfferStatus::Accepted, 'expires_at' => now()->addHours(4)]);
        (new OfferExpiringSoon)->handle();
        (new OfferExpiringSoon)->handle();
        Mail::assertQueued(OfferExpiringSoonMail::class, 1);
        $this->assertNotNull($soon->fresh()->expiry_notified_at);
    }
}
