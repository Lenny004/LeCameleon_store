<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\BuildsCheckoutPayload;
use Tests\TestCase;

class CheckoutReservationTest extends TestCase
{
    use BuildsCheckoutPayload;
    use RefreshDatabase;

    public function test_placing_order_increases_quantity_reserved_for_authenticated_customer(): void
    {
        $customer = User::factory()->customer()->create();
        $product = Product::factory()->create([
            'quantity_available' => 5,
            'quantity_reserved' => 0,
            'price' => 120.00,
        ]);

        $this->actingAs($customer)
            ->post(route('cart.store'), [
                'product_id' => $product->id,
                'quantity' => 2,
            ])
            ->assertSessionDoesntHaveErrors();

        $this->actingAs($customer)
            ->post(route('checkout.store'), $this->checkoutPayload())
            ->assertRedirect();

        $product->refresh();

        $this->assertSame(2, $product->quantity_reserved);
        $this->assertSame(5, $product->quantity_available);
    }
}
