<?php

namespace Tests\Feature;

use App\Enums\CouponType;
use App\Models\Category;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\BuildsCheckoutPayload;
use Tests\TestCase;

class Fase3CouponsTest extends TestCase
{
    use BuildsCheckoutPayload;
    use RefreshDatabase;

    public function test_coupon_advanced_rules_and_restriction_pivots_are_persisted(): void
    {
        $category = Category::factory()->create();
        $product = Product::factory()->create(['category_id' => $category->id]);
        $coupon = Coupon::query()->create(['code' => 'FIRST', 'type' => CouponType::Percent, 'value' => 10, 'max_uses_per_user' => 1, 'first_order_only' => true]);
        $coupon->categories()->attach($category);
        $coupon->products()->attach($product);

        $this->assertTrue($coupon->fresh()->first_order_only);
        $this->assertSame(1, $coupon->fresh()->categories()->count());
        $this->assertSame(1, $coupon->fresh()->products()->count());
    }

    public function test_first_order_only_rejects_customers_with_previous_orders_and_accepts_first_order(): void
    {
        $coupon = Coupon::query()->create([
            'code' => 'FIRSTONLY',
            'type' => CouponType::Percent,
            'value' => 10,
            'first_order_only' => true,
            'is_active' => true,
        ]);
        $product = Product::factory()->create(['price' => 100, 'quantity_available' => 5]);
        $customer = User::factory()->create();
        Order::factory()->for($customer)->create();

        $this->actingAs($customer)->post(route('cart.store'), ['product_id' => $product->id, 'quantity' => 1]);
        $this->actingAs($customer)
            ->post(route('checkout.store'), $this->checkoutPayload(['coupon_code' => $coupon->code]))
            ->assertSessionHasErrors('coupon_code');

        $firstCustomer = User::factory()->create();
        $this->actingAs($firstCustomer)->post(route('cart.store'), ['product_id' => $product->id, 'quantity' => 1]);
        $this->actingAs($firstCustomer)
            ->post(route('checkout.store'), $this->checkoutPayload(['coupon_code' => $coupon->code]))
            ->assertRedirect(route('checkout.success', Order::query()->where('user_id', $firstCustomer->id)->sole()));
    }

    public function test_max_uses_per_user_and_guest_email_is_enforced(): void
    {
        $coupon = Coupon::query()->create([
            'code' => 'ONCE',
            'type' => CouponType::Percent,
            'value' => 10,
            'max_uses_per_user' => 1,
            'is_active' => true,
        ]);
        $product = Product::factory()->create(['price' => 100, 'quantity_available' => 10]);
        $customer = User::factory()->create();

        $this->actingAs($customer)->post(route('cart.store'), ['product_id' => $product->id, 'quantity' => 1]);
        $this->actingAs($customer)->post(route('checkout.store'), $this->checkoutPayload(['coupon_code' => $coupon->code]));
        $this->actingAs($customer)->post(route('cart.store'), ['product_id' => $product->id, 'quantity' => 1]);
        $this->actingAs($customer)
            ->post(route('checkout.store'), $this->checkoutPayload(['coupon_code' => $coupon->code]))
            ->assertSessionHasErrors('coupon_code');
    }

    public function test_max_uses_per_guest_email_is_enforced(): void
    {
        Coupon::query()->create([
            'code' => 'GUESTONCE',
            'type' => CouponType::Percent,
            'value' => 10,
            'max_uses_per_user' => 1,
            'is_active' => true,
        ]);
        $product = Product::factory()->create(['price' => 100, 'quantity_available' => 5]);
        $payload = $this->checkoutPayload([
            'email' => 'invitado@example.com',
            'coupon_code' => 'GUESTONCE',
        ]);

        $this->post(route('cart.store'), ['product_id' => $product->id, 'quantity' => 1]);
        $this->post(route('checkout.store'), $payload)->assertRedirect();
        $this->post(route('cart.store'), ['product_id' => $product->id, 'quantity' => 1]);
        $this->post(route('checkout.store'), $payload)->assertSessionHasErrors('coupon_code');
    }

    public function test_category_restriction_discounts_only_eligible_subtotal_and_product_miss_is_invalid(): void
    {
        $eligibleCategory = Category::factory()->create();
        $otherCategory = Category::factory()->create();
        $eligible = Product::factory()->create(['category_id' => $eligibleCategory->id, 'price' => 100, 'quantity_available' => 5]);
        $other = Product::factory()->create(['category_id' => $otherCategory->id, 'price' => 50, 'quantity_available' => 5]);
        $coupon = Coupon::query()->create([
            'code' => 'CAT10',
            'type' => CouponType::Percent,
            'value' => 10,
            'is_active' => true,
        ]);
        $coupon->categories()->attach($eligibleCategory);
        $customer = User::factory()->create();

        $this->actingAs($customer)->post(route('cart.store'), ['product_id' => $eligible->id, 'quantity' => 1]);
        $this->actingAs($customer)->post(route('cart.store'), ['product_id' => $other->id, 'quantity' => 1]);
        $this->actingAs($customer)->post(route('checkout.store'), $this->checkoutPayload(['coupon_code' => 'CAT10']))
            ->assertRedirect();
        $this->assertEquals(10, (float) Order::query()->latest('placed_at')->first()->discount_total);

        $invalid = Coupon::query()->create([
            'code' => 'ONLYOTHER',
            'type' => CouponType::Percent,
            'value' => 10,
            'is_active' => true,
        ]);
        $invalid->products()->attach($other);
        $this->actingAs($customer)->post(route('cart.store'), ['product_id' => $eligible->id, 'quantity' => 1]);
        $this->actingAs($customer)->post(route('checkout.store'), $this->checkoutPayload(['coupon_code' => 'ONLYOTHER']))
            ->assertSessionHasErrors('coupon_code');
    }

    public function test_admin_coupon_create_and_edit_syncs_category_and_product_pivots(): void
    {
        $staff = User::factory()->staff()->create();
        $category = Category::factory()->create();
        $product = Product::factory()->create();
        $payload = [
            'code' => 'ADMIN10',
            'type' => CouponType::Percent->value,
            'value' => 10,
            'is_active' => 1,
            'category_ids' => [$category->id],
            'product_ids' => [$product->id],
        ];

        $this->actingAs($staff)->post(route('admin.coupons.store'), $payload)->assertRedirect();
        $coupon = Coupon::query()->where('code', 'ADMIN10')->firstOrFail();
        $this->assertCount(1, $coupon->categories);
        $this->assertCount(1, $coupon->products);

        $this->actingAs($staff)->put(route('admin.coupons.update', $coupon), array_merge($payload, ['category_ids' => [], 'product_ids' => []]))
            ->assertRedirect();
        $this->assertCount(0, $coupon->fresh()->categories);
        $this->assertCount(0, $coupon->fresh()->products);
    }
}
