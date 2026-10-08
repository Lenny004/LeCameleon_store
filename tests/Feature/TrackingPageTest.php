<?php

namespace Tests\Feature;

use App\Enums\ShipmentStatus;
use App\Models\Order;
use App\Models\Shipment;
use App\Models\ShipmentEvent;
use App\Models\SvMunicipality;
use Database\Seeders\ElSalvadorGeoSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Public shipment tracking lookup pages (LC-SV-XXXXXX).
 */
class TrackingPageTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(ElSalvadorGeoSeeder::class);
    }

    public function test_tracking_index_page_is_reachable(): void
    {
        $this->get(route('tracking.index'))
            ->assertOk()
            ->assertSee('Rastrear tu envío', false)
            ->assertSee('LC-SV-XXXXXX', false);
    }

    public function test_tracking_lookup_redirects_to_show_route(): void
    {
        $this->post(route('tracking.lookup'), [
            'code' => 'lc-sv-abc123',
        ])
            ->assertRedirect(route('tracking.show', ['code' => 'LC-SV-ABC123']));
    }

    public function test_tracking_show_displays_shipment_timeline(): void
    {
        $municipality = SvMunicipality::query()->where('code', 'SS-C')->firstOrFail();
        $trackingCode = 'LC-SV-QATEST';

        $order = Order::factory()->paid()->create();
        $shipment = Shipment::query()->create([
            'order_id' => $order->id,
            'tracking_number' => $trackingCode,
            'status' => ShipmentStatus::InTransit,
            'destination_municipality_id' => $municipality->id,
            'carrier' => 'Cameleon Express',
        ]);

        ShipmentEvent::query()->create([
            'shipment_id' => $shipment->id,
            'status' => ShipmentStatus::Pending,
            'happened_at' => now()->subHours(2),
        ]);

        ShipmentEvent::query()->create([
            'shipment_id' => $shipment->id,
            'status' => ShipmentStatus::InTransit,
            'happened_at' => now()->subHour(),
            'note' => 'Salió de bodega.',
        ]);

        $this->get(route('tracking.show', ['code' => $trackingCode]))
            ->assertOk()
            ->assertSee($trackingCode, false)
            ->assertSee('San Salvador Centro', false)
            ->assertSee('En tránsito', false)
            ->assertSee('Salió de bodega.', false);
    }

    public function test_tracking_show_shows_not_found_for_unknown_code(): void
    {
        $this->get(route('tracking.show', ['code' => 'LC-SV-NOTFOUND']))
            ->assertOk()
            ->assertSee('No encontramos ese código', false)
            ->assertSee('LC-SV-NOTFOUND', false);
    }
}
