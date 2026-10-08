<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class Fase3WhatsAppTest extends TestCase
{
    use RefreshDatabase;

    public function test_whatsapp_is_hidden_without_a_number(): void
    {
        $this->get(route('home'))->assertDontSee('Escríbenos por WhatsApp');
    }

    public function test_whatsapp_link_is_rendered_on_home_and_product_page(): void
    {
        Setting::query()->create(['key' => 'store.whatsapp', 'value' => ['number' => '50377777777', 'message' => 'Hola']]);
        $product = Product::factory()->create(['slug' => 'pieza-whatsapp']);

        $this->get(route('home'))->assertSee('https://wa.me/50377777777?text=Hola', false);
        $this->get(route('shop.show', $product->slug))->assertSee('https://wa.me/50377777777', false);
    }

    public function test_staff_can_save_whatsapp_number_and_it_survives_other_settings_updates(): void
    {
        $staff = User::factory()->admin()->create(['two_factor_confirmed_at' => now()]);
        $this->actingAs($staff)->withSession(['two_factor_passed' => true])
            ->put(route('admin.settings.update'), [
                'store_name' => 'Le Cameleon',
                'whatsapp' => ['number' => '+50377777777', 'message' => 'Hola'],
            ])
            ->assertRedirect();

        $this->withSession(['two_factor_passed' => true])->get(route('admin.settings.index'))
            ->assertSee('value="50377777777"', false)
            ->assertSee('Hola', false);

        $this->actingAs($staff)->withSession(['two_factor_passed' => true])
            ->put(route('admin.settings.update'), [
                'store_name' => 'Le Cameleon actualizado',
            ])
            ->assertRedirect();

        $this->assertSame('50377777777', data_get(Setting::getValue('store.whatsapp'), 'number'));
    }

    public function test_whatsapp_validation_rejects_invalid_numbers_and_stores_only_digits(): void
    {
        $staff = User::factory()->admin()->create(['two_factor_confirmed_at' => now()]);

        $this->actingAs($staff)->withSession(['two_factor_passed' => true])->put(route('admin.settings.update'), [
            'store_name' => 'Le Cameleon',
            'whatsapp' => ['number' => '123', 'message' => 'Hola'],
        ])->assertSessionHasErrors('whatsapp.number');

        $this->actingAs($staff)->withSession(['two_factor_passed' => true])->put(route('admin.settings.update'), [
            'store_name' => 'Le Cameleon',
            'whatsapp' => ['number' => '+50377777777', 'message' => 'Hola'],
        ])->assertRedirect();

        $this->assertSame('50377777777', data_get(Setting::getValue('store.whatsapp'), 'number'));
    }

    public function test_product_whatsapp_link_contains_name_and_absolute_url_in_encoded_text(): void
    {
        Setting::query()->create(['key' => 'store.whatsapp', 'value' => ['number' => '50377777777']]);
        $product = Product::factory()->create(['name' => 'Vestido Azul', 'slug' => 'vestido-azul']);

        $this->get(route('shop.show', $product->slug))
            ->assertSee(rawurlencode('Hola, me interesa esta pieza: Vestido Azul ('.route('shop.show', $product->slug).')'), false);
    }
}
