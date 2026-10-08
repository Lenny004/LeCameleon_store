<?php

namespace Tests\Feature;

use App\Enums\OfferStatus;
use App\Models\Address;
use App\Models\Offer;
use App\Models\Product;
use App\Models\Setting;
use App\Models\User;
use App\Services\CartService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use PragmaRX\Google2FA\Google2FA;
use Tests\Concerns\BuildsCheckoutPayload;
use Tests\TestCase;

class Fase2CoreTest extends TestCase
{
    use BuildsCheckoutPayload;
    use RefreshDatabase;

    public function test_transfer_checkout_uses_transfer_provider_and_shows_instructions(): void
    {
        Mail::fake();
        Setting::create(['key' => 'payments.transfer', 'value' => ['enabled' => true, 'bank' => 'Banco', 'account_holder' => 'Le Cameleon', 'account_number' => '123', 'account_type' => 'savings', 'instructions' => 'Envía el comprobante.']]);
        $user = User::factory()->customer()->create();
        $product = Product::factory()->create(['quantity_available' => 2, 'price' => 25]);

        $this->actingAs($user)->post(route('cart.store'), ['product_id' => $product->id, 'quantity' => 1]);
        $this->actingAs($user)->post(route('checkout.store'), $this->checkoutPayload(['email' => $user->email, 'payment_method' => 'transfer']))->assertRedirect();

        $this->assertDatabaseHas('payments', ['provider' => 'transfer', 'status' => 'pending']);
        $order = $user->orders()->latest()->firstOrFail();
        $this->actingAs($user)->get(route('checkout.success', $order))->assertOk()->assertSee('Banco');
    }

    public function test_cod_is_rejected_when_disabled(): void
    {
        $user = User::factory()->customer()->create();
        $product = Product::factory()->create(['quantity_available' => 1]);
        $this->actingAs($user)->post(route('cart.store'), ['product_id' => $product->id, 'quantity' => 1]);

        $this->actingAs($user)->post(route('checkout.store'), $this->checkoutPayload(['email' => $user->email, 'payment_method' => 'cod']))
            ->assertSessionHasErrors(['payment_method']);
        $this->assertDatabaseCount('orders', 0);
    }

    public function test_saved_address_has_single_default_and_prefills_checkout(): void
    {
        $user = User::factory()->customer()->create();
        $address = Address::query()->create(['user_id' => $user->id, 'first_name' => 'Ana', 'last_name' => 'López', 'line1' => 'Calle 1', 'city' => 'San Salvador', 'postal_code' => '1101', 'country' => 'SV', 'is_default' => true]);
        $second = Address::query()->create(['user_id' => $user->id, 'first_name' => 'Luis', 'last_name' => 'Pérez', 'line1' => 'Calle 2', 'city' => 'Santa Tecla', 'postal_code' => '1501', 'country' => 'SV']);
        $this->actingAs($user)->patch(route('account.addresses.default', $second))->assertRedirect();
        $this->assertDatabaseHas('addresses', ['id' => $address->id, 'is_default' => 0]);
        $product = Product::factory()->create(['quantity_available' => 1]);
        $this->actingAs($user)->post(route('cart.store'), ['product_id' => $product->id, 'quantity' => 1]);
        $this->actingAs($user)->get(route('checkout.index'))->assertOk()->assertSee('Calle 2');
    }

    public function test_accepted_offer_changes_cart_price(): void
    {
        $user = User::factory()->customer()->create();
        $product = Product::factory()->create(['quantity_available' => 1, 'price' => 100]);
        Offer::create(['product_id' => $product->id, 'user_id' => $user->id, 'amount' => 70, 'accepted_amount' => 70, 'status' => OfferStatus::Accepted, 'expires_at' => now()->addDay(), 'responded_at' => now()]);
        $cart = app(CartService::class)->resolveCart($user, null);
        app(CartService::class)->addItem($cart, $product, 1);
        $this->assertSame(70.0, (float) $cart->items()->first()->unit_price);
    }

    public function test_receipt_can_be_uploaded_and_accepted_by_admin(): void
    {
        Storage::fake('local');
        Mail::fake();
        Setting::create(['key' => 'payments.transfer', 'value' => ['enabled' => true, 'bank' => 'Banco', 'account_holder' => 'Le Cameleon', 'account_number' => '123', 'account_type' => 'savings', 'instructions' => '']]);
        $user = User::factory()->customer()->create();
        $admin = User::factory()->admin()->create();
        $product = Product::factory()->create(['quantity_available' => 1]);
        $this->actingAs($user)->post(route('cart.store'), ['product_id' => $product->id, 'quantity' => 1]);
        $this->actingAs($user)->post(route('checkout.store'), $this->checkoutPayload(['email' => $user->email, 'payment_method' => 'transfer']));
        $order = $user->orders()->latest()->firstOrFail();

        $this->actingAs($user)->post(route('account.orders.receipts.store', $order), ['receipt' => UploadedFile::fake()->create('comprobante.pdf', 100, 'application/pdf')])->assertRedirect();
        $receipt = $order->paymentReceipts()->firstOrFail();
        $this->actingAs($admin)->patch(route('admin.orders.receipts.accept', [$order, $receipt]))->assertRedirect();
        $this->assertDatabaseHas('payment_receipts', ['id' => $receipt->id, 'status' => 'accepted']);
        $this->assertDatabaseHas('orders', ['id' => $order->id, 'status' => 'paid']);
    }

    public function test_admin_two_factor_setup_and_challenge(): void
    {
        config(['security.admin_2fa_required' => true]);
        $admin = User::factory()->admin()->create();
        $response = $this->actingAs($admin)->get(route('admin.dashboard'));
        $response->assertRedirect(route('admin.two-factor.setup'));
        $this->actingAs($admin)->get(route('admin.two-factor.setup'))->assertOk();
        $admin->refresh();
        $code = app(Google2FA::class)->getCurrentOtp($admin->two_factor_secret);
        $this->actingAs($admin)->post(route('admin.two-factor.confirm'), ['code' => $code])->assertRedirect();
        $this->assertNotNull($admin->fresh()->two_factor_confirmed_at);
    }
}
