<?php

namespace Tests\Feature;

use App\Enums\CouponType;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\BuildsCheckoutPayload;
use Tests\TestCase;

class CouponCheckoutTest extends TestCase
{
    use BuildsCheckoutPayload;
    use RefreshDatabase;

    public function test_valid_coupon_reduces_discount_total_on_order(): void
    {
        $customer = User::factory()->customer()->create();
        $product = Product::factory()->create([
            'price' => 100.00,
            'quantity_available' => 5,
            'quantity_reserved' => 0,
        ]);

        Coupon::query()->create([
            'code' => 'QA10OFF',
            'type' => CouponType::Percent,
            'value' => 10,
            'min_order_amount' => null,
            'max_uses' => null,
            'used_count' => 0,
            'starts_at' => now()->subDay(),
            'ends_at' => now()->addMonth(),
            'is_active' => true,
        ]);

        $this->actingAs($customer)
            ->post(route('cart.store'), [
                'product_id' => $product->id,
                'quantity' => 1,
            ]);

        $this->actingAs($customer)
            ->post(route('checkout.store'), $this->checkoutPayload([
                'coupon_code' => 'QA10OFF',
            ]))
            ->assertRedirect();

        $order = Order::query()->latest('placed_at')->first();

        $this->assertNotNull($order);
        $this->assertSame('QA10OFF', $order->coupon_code);
        $this->assertEquals(10.0, (float) $order->discount_total);
    }

    public function test_invalid_coupon_is_rejected_at_checkout(): void
    {
        $customer = User::factory()->customer()->create();
        $product = Product::factory()->create([
            'price' => 80.00,
            'quantity_available' => 3,
            'quantity_reserved' => 0,
        ]);

        $this->actingAs($customer)
            ->post(route('cart.store'), [
                'product_id' => $product->id,
                'quantity' => 1,
            ]);

        $this->actingAs($customer)
            ->post(route('checkout.store'), $this->checkoutPayload([
                'coupon_code' => 'NOT-A-REAL-CODE',
            ]))
            ->assertSessionHasErrors('coupon_code');
    }
}
