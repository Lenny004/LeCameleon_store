<?php

namespace Tests\Feature;

use App\Enums\OrderStatus;
use App\Models\Cart;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Services\CartService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class Fase2AdminOrdersTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_filters_and_exports_orders_with_spanish_labels(): void
    {
        $admin = User::factory()->admin()->create();
        $formulaUser = User::factory()->customer()->create(['name' => '=cliente']);
        $order = Order::factory()->create([
            'user_id' => $formulaUser->id,
            'status' => OrderStatus::Pending,
            'shipping_address' => ['first_name' => 'Cliente', 'email' => 'search@example.com'],
        ]);

        $this->actingAs($admin)
            ->get(route('admin.orders.index', ['status' => 'pending', 'search' => $order->number]))
            ->assertOk()
            ->assertSee('Pendiente')
            ->assertSee('Buscar');

        $response = $this->actingAs($admin)
            ->get(route('admin.orders.export'))
            ->assertOk()
            ->assertHeader('content-type', 'text/csv; charset=UTF-8');
        $this->assertStringContainsString('número', $response->streamedContent());
        $this->assertStringContainsString("'=cliente", $response->streamedContent());
    }

    public function test_print_is_for_staff_only_and_includes_shipping_contact(): void
    {
        $staff = User::factory()->staff()->create();
        $customer = User::factory()->customer()->create();
        $order = Order::factory()->create([
            'shipping_address' => [
                'first_name' => 'Ana',
                'phone' => '7777-7777',
                'sv_municipality_id' => null,
            ],
        ]);

        $this->actingAs($staff)
            ->get(route('admin.orders.print', $order))
            ->assertOk()
            ->assertSee('Hoja de empaque / Comprobante de pedido')
            ->assertSee('7777-7777');
        $this->actingAs($customer)->get(route('admin.orders.print', $order))->assertForbidden();
    }

    public function test_internal_note_is_not_visible_to_customer(): void
    {
        $admin = User::factory()->admin()->create();
        $customer = User::factory()->customer()->create();
        $order = Order::factory()->for($customer)->create();

        $this->actingAs($admin)
            ->post(route('admin.orders.notes.store', $order), ['body' => 'Revisar embalaje'])
            ->assertRedirect();
        $this->assertDatabaseHas('order_notes', ['order_id' => $order->id, 'body' => 'Revisar embalaje']);
        $this->actingAs($customer)->get(route('account.orders.show', $order))->assertDontSee('Revisar embalaje');
    }

    public function test_manual_order_does_not_touch_customer_cart_and_reserves_stock(): void
    {
        $admin = User::factory()->admin()->create();
        $customer = User::factory()->customer()->create(['email' => 'manual@example.com']);
        $product = Product::factory()->create(['quantity_available' => 2]);
        $cart = app(CartService::class)->resolveCart($customer, null);
        app(CartService::class)->addItem($cart, $product, 1);

        $payload = [
            'email' => $customer->email,
            'customer_name' => $customer->name,
            'shipping_address' => [
                'first_name' => 'Ana',
                'last_name' => 'López',
                'line1' => 'Calle 1',
                'city' => 'San Salvador',
                'state' => 'San Salvador',
                'postal_code' => '1101',
                'country' => 'SV',
                'phone' => '7777-7777',
            ],
            'items' => [['product_id' => $product->id, 'quantity' => 1]],
            'payment_method' => 'manual',
        ];

        $this->actingAs($admin)->post(route('admin.orders.store-manual'), $payload)->assertRedirect();
        $this->assertSame(1, $cart->fresh()->items()->count());
        $this->assertSame(1, (int) $product->fresh()->quantity_reserved);
        $this->assertSame(0, Cart::query()->where('session_id', 'like', 'admin-manual-%')->count());
    }
}
