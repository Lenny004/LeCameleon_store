<?php

namespace Tests\Feature;

use App\Enums\CouponType;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Coupon;
use App\Models\InventoryMovement;
use App\Models\Product;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminCatalogManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_catalog_lists_render_real_models_counts_actions_and_paginators(): void
    {
        $admin = User::factory()->admin()->create();
        $category = Category::factory()->create(['name' => 'Real Category']);
        $brand = Brand::factory()->create(['name' => 'Real Brand']);
        Product::factory()->for($category)->for($brand)->create(['name' => 'Real Product']);
        $coupon = Coupon::query()->create([
            'code' => 'REAL20',
            'type' => CouponType::Percent,
            'value' => 20,
            'shipping_only' => true,
            'is_active' => true,
        ]);

        $this->actingAs($admin)
            ->get(route('admin.categories.index'))
            ->assertOk()
            ->assertSee('Real Category', false)
            ->assertSee(route('admin.categories.edit', $category), false)
            ->assertViewHas('categories', fn ($items) => $items instanceof LengthAwarePaginator
                && $items->first()->products_count === 1);

        $this->actingAs($admin)
            ->get(route('admin.brands.index'))
            ->assertOk()
            ->assertSee('Real Brand', false)
            ->assertSee(route('admin.brands.destroy', $brand), false)
            ->assertViewHas('brands', fn ($items) => $items instanceof LengthAwarePaginator
                && $items->first()->products_count === 1);

        $this->actingAs($admin)
            ->get(route('admin.coupons.index'))
            ->assertOk()
            ->assertSee('REAL20', false)
            ->assertSee('Shipping', false)
            ->assertSee(route('admin.coupons.show', $coupon), false)
            ->assertViewHas('coupons', fn ($items) => $items instanceof LengthAwarePaginator);
    }

    public function test_coupon_shipping_only_is_persisted_and_percent_cannot_exceed_one_hundred(): void
    {
        $admin = User::factory()->admin()->create();
        $payload = [
            'code' => 'SHIP20',
            'type' => CouponType::Percent->value,
            'value' => 20,
            'min_order_amount' => 10,
            'max_uses' => 50,
            'is_active' => 1,
            'shipping_only' => 1,
        ];

        $this->actingAs($admin)
            ->post(route('admin.coupons.store'), $payload)
            ->assertRedirect();

        $coupon = Coupon::query()->where('code', 'SHIP20')->firstOrFail();
        $this->assertTrue($coupon->shipping_only);

        $this->actingAs($admin)
            ->put(route('admin.coupons.update', $coupon), [
                ...$payload,
                'value' => 101,
                'shipping_only' => 0,
            ])
            ->assertSessionHasErrors('value');

        $this->assertTrue($coupon->fresh()->shipping_only);
    }

    public function test_settings_are_admin_only_allowlisted_and_consumed_by_storefront(): void
    {
        $admin = User::factory()->admin()->create();
        $staff = User::factory()->staff()->create();

        $this->actingAs($staff)
            ->get(route('admin.settings.index'))
            ->assertForbidden();

        $this->actingAs($staff)
            ->put(route('admin.settings.update'), [])
            ->assertForbidden();

        $this->actingAs($admin)
            ->put(route('admin.settings.update'), [
                'store_name' => 'Cameleon Real',
                'contact_email' => 'contacto@example.com',
                'contact_phone' => '+503 2222-3333',
                'returns_policy' => 'Policy from database.',
                'settings' => [['key' => 'unapproved.key', 'value' => 'unsafe']],
            ])
            ->assertRedirect();

        $this->assertDatabaseMissing('settings', ['key' => 'unapproved.key']);
        $this->assertSame(['en' => 'Cameleon Real'], Setting::getValue('store.name'));

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('Cameleon Real', false)
            ->assertSee('contacto@example.com', false);

        $this->get(route('returns'))
            ->assertOk()
            ->assertSee('Policy from database.', false);
    }

    public function test_inventory_uses_real_stock_metrics_products_and_movements(): void
    {
        $admin = User::factory()->admin()->create();
        $lowStock = Product::factory()->create([
            'name' => 'Low Stock Product',
            'quantity_available' => 3,
            'quantity_reserved' => 2,
            'low_stock_threshold' => 1,
        ]);
        Product::factory()->create([
            'name' => 'Out Of Stock Product',
            'quantity_available' => 1,
            'quantity_reserved' => 1,
        ]);

        $this->actingAs($admin)
            ->get(route('admin.inventory.index'))
            ->assertOk()
            ->assertSee('Low Stock Product', false)
            ->assertSee(route('admin.products.edit', $lowStock), false)
            ->assertViewHas('totalSkus', 2)
            ->assertViewHas('inStock', 1)
            ->assertViewHas('outOfStock', 1)
            ->assertViewHas('lowStockCount', 1)
            ->assertViewHas('stockProducts', fn ($items) => $items instanceof LengthAwarePaginator)
            ->assertViewHas('movements', fn ($items) => $items instanceof LengthAwarePaginator);

        $this->actingAs($admin)
            ->post(route('admin.inventory.store'), [
                'product_id' => $lowStock->id,
                'type' => 'stock_in',
                'quantity' => 2,
                'notes' => 'Feature test stock',
            ])
            ->assertRedirect();

        $this->assertSame(5, $lowStock->fresh()->quantity_available);
        $this->assertDatabaseHas('inventory_movements', [
            'product_id' => $lowStock->id,
            'user_id' => $admin->id,
            'quantity' => 2,
            'notes' => 'Feature test stock',
        ]);
        $this->assertSame(1, InventoryMovement::query()->count());
    }

    public function test_rates_matrix_route_is_not_captured_by_zone_binding(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->get(route('admin.logistics.zones.rates-matrix'))
            ->assertOk()
            ->assertSee('Rates matrix', false);
    }

    public function test_creating_a_product_with_empty_fields_returns_spanish_field_errors(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)
            ->post(route('admin.products.store'), []);

        $response->assertSessionHasErrors([
            'name',
            'slug',
            'sku',
            'type',
            'status',
            'price',
            'condition_grade',
            'quantity_available',
        ]);
        $this->assertStringContainsString('nombre', session('errors')->get('name')[0]);
        $this->assertStringContainsString('precio', session('errors')->get('price')[0]);
    }

    public function test_coupon_code_longer_than_database_column_returns_maximum_error(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)
            ->post(route('admin.coupons.store'), [
                'code' => str_repeat('A', 51),
                'type' => CouponType::Percent->value,
                'value' => 10,
            ]);

        $response->assertSessionHasErrors('code');
        $this->assertStringContainsString('50', session('errors')->get('code')[0]);
    }

    public function test_creating_a_user_with_a_duplicate_email_returns_an_error(): void
    {
        $admin = User::factory()->admin()->create();
        $existing = User::factory()->create(['email' => 'duplicado@example.com']);

        $response = $this->actingAs($admin)
            ->post(route('admin.users.store'), [
                'name' => 'Usuario duplicado',
                'email' => $existing->email,
                'password' => 'Password123!',
                'role' => 'staff',
            ]);

        $response->assertSessionHasErrors('email');
        $this->assertStringContainsString('correo electrónico', session('errors')->get('email')[0]);
    }
}
