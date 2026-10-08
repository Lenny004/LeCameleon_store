<?php

namespace Tests\Feature;

use App\Enums\ProductStatus;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FormsC2Test extends TestCase
{
    use RefreshDatabase;

    public function test_checkout_without_data_returns_named_spanish_errors(): void
    {
        $product = Product::factory()->create([
            'quantity_available' => 1,
            'quantity_reserved' => 0,
        ]);

        $this->post(route('cart.store'), [
            'product_id' => $product->id,
            'quantity' => 1,
        ])->assertRedirect();

        $response = $this->post(route('checkout.store'), []);

        $response->assertSessionHasErrors(['email', 'shipping_address.city']);
        $errors = session('errors');

        $this->assertSame('El campo ciudad de envío es obligatorio.', $errors->get('shipping_address.city')[0]);
        $this->assertSame('El campo correo electrónico es obligatorio.', $errors->get('email')[0]);
    }

    public function test_contact_respects_database_name_length_and_message_business_limit(): void
    {
        $response = $this->post(route('contact.store'), [
            'name' => str_repeat('a', 256),
            'email' => 'cliente@example.com',
            'message' => str_repeat('b', 5001),
        ]);

        $response->assertSessionHasErrors(['name', 'message']);
        $errors = session('errors');

        $this->assertSame('El campo nombre no debe tener más de 255 caracteres.', $errors->get('name')[0]);
        $this->assertSame('El campo mensaje no debe tener más de 5000 caracteres.', $errors->get('message')[0]);
    }

    public function test_review_rating_above_five_is_rejected(): void
    {
        $customer = User::factory()->customer()->create();
        $product = Product::factory()->create([
            'status' => ProductStatus::Published,
            'published_at' => now(),
        ]);

        $response = $this->actingAs($customer)->post(route('shop.reviews.store', $product), [
            'rating' => 6,
            'body' => 'Una experiencia interesante.',
        ]);

        $response->assertSessionHasErrors('rating');
        $this->assertSame(
            'El campo calificación no debe ser mayor que 5.',
            session('errors')->get('rating')[0],
        );
    }
}
