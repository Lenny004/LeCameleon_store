<?php

namespace Tests\Feature;

use App\Models\SvMunicipality;
use Database\Seeders\ElSalvadorGeoSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\SeedsSvLogistics;
use Tests\TestCase;

/**
 * Shipping quote calculator and JSON rate endpoint for SV municipalities.
 */
class ShippingRateTest extends TestCase
{
    use RefreshDatabase;
    use SeedsSvLogistics;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(ElSalvadorGeoSeeder::class);
    }

    public function test_shipping_quote_page_lists_municipalities(): void
    {
        $this->get(route('shipping.quote'))
            ->assertOk()
            ->assertSee('Calculadora de envío', false)
            ->assertSee('San Salvador Centro', false);
    }

    public function test_calculate_returns_flat_municipality_rate_without_zones(): void
    {
        $destination = SvMunicipality::query()->where('code', 'MO-N')->firstOrFail();

        $response = $this->getJson(route('shipping.quote.calculate', [
            'destination_municipality_id' => $destination->id,
        ]));

        $response
            ->assertOk()
            ->assertJsonPath('method', 'flat')
            ->assertJsonPath('fee', 12);
    }

    public function test_calculate_uses_zone_matrix_when_demo_zones_are_seeded(): void
    {
        $this->seedSvLogisticsDemo();

        $warehouse = SvMunicipality::query()->where('code', 'SS-C')->firstOrFail();
        $libertadSur = SvMunicipality::query()->where('code', 'LI-S')->firstOrFail();

        config(['store.warehouse_municipality_id' => $warehouse->id]);

        $response = $this->getJson(route('shipping.quote.calculate', [
            'destination_municipality_id' => $libertadSur->id,
        ]));

        $response
            ->assertOk()
            ->assertJsonPath('method', 'zone_matrix')
            ->assertJsonStructure([
                'fee',
                'distance_km',
                'eta_hours',
                'shipping_zone_rate_id',
            ]);

        $this->assertNotNull($response->json('shipping_zone_rate_id'));
        $this->assertGreaterThan(0, (float) $response->json('fee'));
    }

    public function test_calculate_rejects_unknown_municipality(): void
    {
        $this->getJson(route('shipping.quote.calculate', [
            'destination_municipality_id' => 999999,
        ]))
            ->assertStatus(422)
            ->assertJsonValidationErrors(['destination_municipality_id']);
    }
}
