<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\BuildsCheckoutPayload;
use Tests\TestCase;

class Fase2AddressesTest extends TestCase
{
    use BuildsCheckoutPayload;
    use RefreshDatabase;

    public function test_customer_can_create_update_set_default_and_delete_address(): void
    {
        $user = User::factory()->customer()->create();
        $payload = $this->addressPayload();

        $this->actingAs($user)->post(route('account.addresses.store'), $payload)->assertRedirect();
        $address = $user->addresses()->firstOrFail();
        $this->actingAs($user)->put(route('account.addresses.update', $address), [...$payload, 'city' => 'Santa Tecla'])->assertRedirect();
        $this->actingAs($user)->patch(route('account.addresses.default', $address))->assertRedirect();
        $this->actingAs($user)->delete(route('account.addresses.destroy', $address))->assertRedirect();
        $this->assertDatabaseMissing('addresses', ['id' => $address->id]);
    }

    public function test_address_limit_returns_validation_error_and_preserves_input(): void
    {
        $user = User::factory()->customer()->create();
        foreach (range(1, 10) as $number) {
            $user->addresses()->create([...$this->addressPayload(), 'line1' => 'Calle '.$number]);
        }

        $payload = $this->addressPayload();
        $this->actingAs($user)
            ->from(route('account.addresses.create'))
            ->post(route('account.addresses.store'), $payload)
            ->assertRedirect(route('account.addresses.create'))
            ->assertSessionHasErrors('addresses')
            ->assertSessionHasInput('line1', $payload['line1']);
    }

    public function test_customer_cannot_modify_another_users_address(): void
    {
        $owner = User::factory()->customer()->create();
        $other = User::factory()->customer()->create();
        $address = $owner->addresses()->create($this->addressPayload());

        $this->actingAs($other)->get(route('account.addresses.edit', $address))->assertForbidden();
        $this->actingAs($other)->delete(route('account.addresses.destroy', $address))->assertForbidden();
    }

    public function test_checkout_does_not_duplicate_an_identical_saved_address(): void
    {
        Setting::create(['key' => 'payments.transfer', 'value' => ['enabled' => true, 'bank' => 'Banco', 'account_holder' => 'Le Cameleon', 'account_number' => '123']]);
        $user = User::factory()->customer()->create();
        $address = $this->addressPayload();
        $user->addresses()->create([...$address, 'is_default' => true]);
        $product = Product::factory()->create(['quantity_available' => 2]);

        $this->actingAs($user)->post(route('cart.store'), ['product_id' => $product->id, 'quantity' => 1]);
        $this->actingAs($user)->post(route('checkout.store'), $this->checkoutPayload([
            'email' => $user->email,
            'shipping_address' => $address,
            'billing_address' => $address,
            'payment_method' => 'transfer',
            'save_address' => 1,
        ]))->assertRedirect();

        $this->assertSame(1, $user->addresses()->count());
    }

    /** @return array<string, mixed> */
    private function addressPayload(): array
    {
        return [
            'label' => 'Casa',
            'first_name' => 'Ana',
            'last_name' => 'López',
            'line1' => 'Calle Principal 123',
            'line2' => null,
            'city' => 'San Salvador',
            'state' => 'San Salvador',
            'postal_code' => '1101',
            'country' => 'SV',
            'phone' => '7777-7777',
        ];
    }
}
