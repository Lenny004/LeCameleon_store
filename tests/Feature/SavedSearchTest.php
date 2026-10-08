<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\SavedSearch;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SavedSearchTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_save_active_filters(): void
    {
        $customer = User::factory()->customer()->create();
        Product::factory()->create(['name' => 'Filterable Vintage Coat']);

        $queryParams = ['q' => 'vintage', 'era_decade' => '1980s'];

        $response = $this->actingAs($customer)->post(route('account.saved-searches.store'), [
            'name' => 'Vintage 80s',
            'query_params' => $queryParams,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('saved_searches', [
            'user_id' => $customer->id,
            'name' => 'Vintage 80s',
        ]);

        $saved = SavedSearch::query()->first();
        $this->assertSame($queryParams, $saved->query_params);
    }

    public function test_cannot_save_search_without_active_filters(): void
    {
        $customer = User::factory()->customer()->create();

        $response = $this->actingAs($customer)->post(route('account.saved-searches.store'), [
            'name' => 'Empty',
            'query_params' => ['sort' => 'new'],
        ]);

        $response->assertSessionHasErrors('query_params');
        $this->assertDatabaseCount('saved_searches', 0);
    }

    public function test_user_can_list_and_delete_own_saved_searches(): void
    {
        $customer = User::factory()->customer()->create();
        $other = User::factory()->customer()->create();

        $mine = SavedSearch::query()->create([
            'user_id' => $customer->id,
            'name' => 'Denim',
            'query_params' => ['q' => 'denim'],
        ]);

        SavedSearch::query()->create([
            'user_id' => $other->id,
            'name' => 'Other',
            'query_params' => ['q' => 'silk'],
        ]);

        $this->actingAs($customer)
            ->get(route('account.saved-searches.index'))
            ->assertOk()
            ->assertSee('Denim', false)
            ->assertDontSee('Other', false);

        $this->actingAs($customer)
            ->delete(route('account.saved-searches.destroy', $mine))
            ->assertRedirect()
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('saved_searches', ['id' => $mine->id]);
    }

    public function test_shop_shows_save_button_when_filters_active(): void
    {
        $customer = User::factory()->customer()->create();
        Product::factory()->create(['name' => 'Shop Filter Product']);

        $this->actingAs($customer)
            ->get(route('shop.index', ['q' => 'shop']))
            ->assertOk()
            ->assertSee('Guardar búsqueda', false);

        $this->actingAs($customer)
            ->get(route('shop.index'))
            ->assertOk()
            ->assertDontSee('Guardar búsqueda', false);
    }
}
