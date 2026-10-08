<?php

namespace Tests\Feature;

use App\Models\ActivityLog;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class Fase3AdminProductsTest extends TestCase
{
    use RefreshDatabase;

    public function test_staff_can_duplicate_a_product_as_a_draft_without_images(): void
    {
        $product = Product::factory()->create();
        $this->actingAs(User::factory()->staff()->create());
        $this->post(route('admin.products.duplicate', $product))->assertRedirect();
        $this->assertDatabaseHas('products', ['slug' => $product->slug.'-copia', 'status' => 'draft', 'quantity_available' => 0]);
    }

    public function test_duplicate_twice_uses_unique_slug_and_records_activity(): void
    {
        $product = Product::factory()->create();
        $this->actingAs(User::factory()->staff()->create());

        $this->post(route('admin.products.duplicate', $product))->assertRedirect();
        $this->post(route('admin.products.duplicate', $product))->assertRedirect();

        $this->assertDatabaseHas('products', ['slug' => $product->slug.'-copia']);
        $this->assertDatabaseHas('products', ['slug' => $product->slug.'-copia-2']);
        $this->assertSame(2, ActivityLog::query()->where('action', 'product.duplicated')->count());
        $this->assertSame(0, Product::query()->whereIn('slug', [$product->slug.'-copia', $product->slug.'-copia-2'])->with('images')->get()->sum(fn (Product $copy): int => $copy->images->count()));
    }

    public function test_bulk_status_validates_enum_updates_products_and_records_each_change(): void
    {
        $staff = User::factory()->staff()->create();
        $products = Product::factory()->count(2)->create();

        $this->actingAs($staff)
            ->patch(route('admin.products.bulk-status'), [
                'product_ids' => $products->pluck('id')->all(),
                'status' => 'draft',
            ])
            ->assertRedirect();

        $this->assertSame(2, Product::query()->whereIn('id', $products->pluck('id'))->where('status', 'draft')->count());
        $this->assertSame(2, ActivityLog::query()->where('action', 'product.status_changed')->count());
        $this->actingAs($staff)
            ->patch(route('admin.products.bulk-status'), [
                'product_ids' => $products->pluck('id')->all(),
                'status' => 'not-a-status',
            ])
            ->assertSessionHasErrors('status');
    }

    public function test_customer_cannot_duplicate_or_use_bulk_product_actions_and_invalid_ids_fail_validation(): void
    {
        $product = Product::factory()->create();
        $customer = User::factory()->customer()->create();

        $this->actingAs($customer)
            ->post(route('admin.products.duplicate', $product))
            ->assertForbidden();
        $this->actingAs($customer)
            ->patch(route('admin.products.bulk-status'), ['product_ids' => [$product->id], 'status' => 'draft'])
            ->assertForbidden();

        $this->actingAs(User::factory()->staff()->create())
            ->patch(route('admin.products.bulk-status'), ['product_ids' => ['not-a-uuid'], 'status' => 'draft'])
            ->assertSessionHasErrors('product_ids.0');
    }
}
