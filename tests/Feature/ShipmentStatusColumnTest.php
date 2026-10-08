<?php

namespace Tests\Feature;

use App\Enums\ShipmentStatus;
use App\Models\Order;
use App\Models\Shipment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShipmentStatusColumnTest extends TestCase
{
    use RefreshDatabase;

    public function test_returned_to_warehouse_status_can_be_saved_and_read(): void
    {
        $shipment = Shipment::query()->create([
            'order_id' => Order::factory()->create()->id,
            'status' => ShipmentStatus::ReturnedToWarehouse,
        ]);

        $this->assertSame(ShipmentStatus::ReturnedToWarehouse, $shipment->fresh()->status);
    }
}
