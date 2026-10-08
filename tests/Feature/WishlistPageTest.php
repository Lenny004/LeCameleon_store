<?php

namespace Tests\Feature;

use App\Enums\ProductStatus;
use App\Models\Product;
use App\Models\Wishlist;
use App\Models\WishlistItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WishlistPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_add_and_remove_wishlist_item(): void
    {
        $product = Product::factory()->create([
            'status' => ProductStatus::Published,
            'published_at' => now()->subDay(),
        ]);

        $this->post(route('wishlist.store'), [
            'product_id' => $product->id,
        ])
            ->assertRedirect()
            ->assertSessionHas('success');

        $response = $this->get(route('wishlist.index'));
        $response->assertOk()->assertSee($product->name, false);

        $wishlist = Wishlist::query()->whereNotNull('session_id')->firstOrFail();
        $item = WishlistItem::query()->where('wishlist_id', $wishlist->id)->firstOrFail();

        $this->delete(route('wishlist.destroy', $item))
            ->assertRedirect()
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('wishlist_items', ['id' => $item->id]);
    }

    public function test_guest_cannot_remove_another_sessions_wishlist_item(): void
    {
        $product = Product::factory()->create([
            'status' => ProductStatus::Published,
            'published_at' => now()->subDay(),
        ]);

        $otherWishlist = Wishlist::query()->create(['session_id' => 'other-session']);
        $foreignItem = $otherWishlist->items()->create(['product_id' => $product->id]);

        $this->withSession(['wishlist_session_id' => 'my-session'])
            ->delete(route('wishlist.destroy', $foreignItem))
            ->assertForbidden();

        $this->assertDatabaseHas('wishlist_items', ['id' => $foreignItem->id]);
    }
}
