<?php

namespace App\Services;

use App\Enums\RecipientOutcome;
use App\Enums\ShipmentStatus;
use App\Models\Order;
use App\Models\Shipment;
use App\Models\ShipmentEvent;
use App\Models\User;
use Illuminate\Support\Str;

/**
 * Public tracking lookup and shipment event timeline management.
 */
class ShipmentTrackingService
{
    public function findByTrackingCode(string $code): ?Shipment
    {
        $normalized = strtoupper(trim($code));

        return Shipment::query()
            ->with(['events', 'order', 'destinationMunicipality'])
            ->where('tracking_number', $normalized)
            ->first();
    }

    public function appendEvent(
        Shipment $shipment,
        ShipmentStatus|string $status,
        RecipientOutcome|string|null $outcome = null,
        ?string $note = null,
        ?User $user = null,
    ): ShipmentEvent {
        $statusEnum = $status instanceof ShipmentStatus
            ? $status
            : ShipmentStatus::from($status);

        $outcomeEnum = match (true) {
            $outcome === null => null,
            $outcome instanceof RecipientOutcome => $outcome,
            default => RecipientOutcome::from($outcome),
        };

        $event = $shipment->events()->create([
            'status' => $statusEnum,
            'recipient_outcome' => $outcomeEnum,
            'note' => $note,
            'happened_at' => now(),
            'created_by' => $user?->id,
        ]);

        $shipment->update(['status' => $statusEnum]);

        if (in_array($statusEnum, [ShipmentStatus::Shipped, ShipmentStatus::InTransit], true) && ! $shipment->shipped_at) {
            $shipment->update(['shipped_at' => now()]);
        }

        if ($statusEnum === ShipmentStatus::Delivered && ! $shipment->delivered_at) {
            $shipment->update(['delivered_at' => now()]);
        }

        return $event;
    }

    /**
     * Create a shipment for an order and assign a public tracking code (LC-SV-XXXXXX).
     */
    public function createForOrder(
        Order $order,
        ?int $destinationMunicipalityId = null,
        ?int $originMunicipalityId = null,
        ?string $carrier = null,
    ): Shipment {
        $shipment = $order->shipments()->create([
            'carrier' => $carrier,
            'tracking_number' => $this->generateTrackingCode(),
            'status' => ShipmentStatus::Pending,
            'origin_municipality_id' => $originMunicipalityId,
            'destination_municipality_id' => $destinationMunicipalityId,
        ]);

        $this->appendEvent($shipment, ShipmentStatus::Pending, note: 'Shipment created.');

        return $shipment->load('events');
    }

    public function generateTrackingCode(): string
    {
        do {
            $code = sprintf('LC-SV-%s', strtoupper(Str::random(6)));
        } while (Shipment::query()->where('tracking_number', $code)->exists());

        return $code;
    }
}
