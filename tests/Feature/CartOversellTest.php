<?php

namespace Tests\Feature;

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CartOversellTest extends TestCase
{
    use RefreshDatabase;

    public function test_cannot_add_more_than_sellable_quantity_to_cart(): void
    {
        $product = Product::factory()->create([
            'quantity_available' => 2,
            'quantity_reserved' => 0,
        ]);

        $this->post(route('cart.store'), [
            'product_id' => $product->id,
            'quantity' => 3,
        ])->assertSessionHasErrors('quantity');
    }

    public function test_cannot_exceed_sellable_quantity_when_adding_incrementally(): void
    {
        $product = Product::factory()->create([
            'quantity_available' => 2,
            'quantity_reserved' => 0,
        ]);

        $this->post(route('cart.store'), [
            'product_id' => $product->id,
            'quantity' => 2,
        ])->assertSessionDoesntHaveErrors();

        $this->post(route('cart.store'), [
            'product_id' => $product->id,
            'quantity' => 1,
        ])->assertSessionHasErrors('quantity');
    }
}
