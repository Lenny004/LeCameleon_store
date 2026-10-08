<?php

namespace Tests\Feature;

use App\Mail\OrderPlaced;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;
use Tests\Concerns\BuildsCheckoutPayload;
use Tests\TestCase;

class Fase1MailQueueTest extends TestCase
{
    use BuildsCheckoutPayload;
    use RefreshDatabase;

    public function test_checkout_persists_order_and_queues_order_placed_mail(): void
    {
        Mail::fake();
        $customer = User::factory()->customer()->create();
        $product = Product::factory()->create([
            'quantity_available' => 2,
            'quantity_reserved' => 0,
            'price' => 120.00,
        ]);

        $this->actingAs($customer)->post(route('cart.store'), [
            'product_id' => $product->id,
            'quantity' => 1,
        ]);

        $this->actingAs($customer)
            ->post(route('checkout.store'), $this->checkoutPayload([
                'email' => $customer->email,
            ]))
            ->assertRedirect();

        $this->assertDatabaseCount('orders', 1);
        Mail::assertQueued(OrderPlaced::class);
    }

    public function test_queue_fake_does_not_prevent_order_persistence(): void
    {
        Queue::fake();
        $customer = User::factory()->customer()->create();
        $product = Product::factory()->create(['quantity_available' => 1]);

        $this->actingAs($customer)->post(route('cart.store'), [
            'product_id' => $product->id,
            'quantity' => 1,
        ]);

        $this->actingAs($customer)->post(route('checkout.store'), $this->checkoutPayload([
            'email' => $customer->email,
        ]));

        $this->assertDatabaseCount('orders', 1);
    }
}
