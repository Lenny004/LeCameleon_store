<?php

namespace App\Services;

use App\Enums\WarningAppliesTo;
use App\Models\DeliveryWarning;
use App\Models\ShippingZone;
use App\Models\ShippingZoneRate;
use App\Models\SvMunicipality;
use Illuminate\Validation\ValidationException;

/**
 * Quotes local El Salvador shipping from warehouse to destination municipality.
 */
class ShippingRateService
{
    public function __construct(
        private readonly DispatchService $dispatchService,
    ) {}

    /**
     * @return array{
     *     fee: float,
     *     distance_km: float,
     *     method: 'flat'|'zone_matrix',
     *     warnings: list<array<string, mixed>>,
     *     eta_hours: int,
     *     next_dispatch_at: string|null,
     *     shipping_zone_rate_id: int|null
     * }
     */
    public function quote(
        ?int $originMunicipalityId,
        int $destinationMunicipalityId,
        ?float $overrideDistanceKm = null,
    ): array {
        $destination = SvMunicipality::query()
            ->where('is_active', true)
            ->find($destinationMunicipalityId);

        if (! $destination) {
            throw ValidationException::withMessages([
                'destination_municipality_id' => 'Invalid destination municipality.',
            ]);
        }

        $origin = $originMunicipalityId
            ? SvMunicipality::query()->find($originMunicipalityId)
            : null;

        $distanceKm = $this->resolveDistanceKm($origin, $destination, $overrideDistanceKm);
        $zoneRate = $this->findZoneRate($originMunicipalityId, $destinationMunicipalityId);

        if ($zoneRate) {
            $rawFee = (float) $zoneRate->base_fee + ((float) $zoneRate->per_km_fee * $distanceKm);
            $fee = max((float) $zoneRate->min_fee, $rawFee);

            if ($zoneRate->max_fee !== null) {
                $fee = min((float) $zoneRate->max_fee, $fee);
            }

            $method = 'zone_matrix';
            $etaHours = (int) ceil((float) ($zoneRate->estimated_hours ?? config('store.default_shipping_eta_hours', 48)));
        } else {
            // Municipality flat baseline when no A→B matrix row exists.
            $fee = (float) ($destination->base_shipping_cost ?? config('store.shipping_flat_rate', 0));
            $method = 'flat';
            $etaHours = (int) config('store.default_shipping_eta_hours', 48);
        }

        $nextDispatch = $this->dispatchService->nextDispatchAt();

        return [
            'fee' => round($fee, 2),
            'distance_km' => round($distanceKm, 2),
            'method' => $method,
            'warnings' => $this->activeWarnings(),
            'eta_hours' => $etaHours,
            'next_dispatch_at' => $nextDispatch?->toIso8601String(),
            'shipping_zone_rate_id' => $zoneRate?->id,
        ];
    }

    private function resolveDistanceKm(
        ?SvMunicipality $origin,
        SvMunicipality $destination,
        ?float $overrideDistanceKm,
    ): float {
        if ($overrideDistanceKm !== null) {
            return max($overrideDistanceKm, 0);
        }

        if (
            $origin
            && $origin->latitude !== null
            && $origin->longitude !== null
            && $destination->latitude !== null
            && $destination->longitude !== null
        ) {
            return $this->haversineKm(
                (float) $origin->latitude,
                (float) $origin->longitude,
                (float) $destination->latitude,
                (float) $destination->longitude,
            );
        }

        return (float) config('store.default_shipping_distance_km', 10);
    }

    private function findZoneRate(?int $originMunicipalityId, int $destinationMunicipalityId): ?ShippingZoneRate
    {
        if (! $originMunicipalityId) {
            return null;
        }

        $originZone = $this->zoneForMunicipality($originMunicipalityId);
        $destinationZone = $this->zoneForMunicipality($destinationMunicipalityId);

        if (! $originZone || ! $destinationZone) {
            return null;
        }

        return ShippingZoneRate::query()
            ->where('origin_zone_id', $originZone->id)
            ->where('destination_zone_id', $destinationZone->id)
            ->where('is_active', true)
            ->first();
    }

    private function zoneForMunicipality(int $municipalityId): ?ShippingZone
    {
        return ShippingZone::query()
            ->where('is_active', true)
            ->where('sv_municipality_id', $municipalityId)
            ->first();
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function activeWarnings(): array
    {
        return DeliveryWarning::query()
            ->where('is_active', true)
            ->where('applies_to', WarningAppliesTo::Checkout)
            ->orderBy('severity')
            ->get()
            ->map(fn (DeliveryWarning $warning) => [
                'id' => $warning->id,
                'code' => $warning->code,
                'title' => $warning->title,
                'message' => $warning->body,
                'severity' => $warning->severity->value,
            ])
            ->values()
            ->all();
    }

    private function haversineKm(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $earthRadiusKm = 6371.0;
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        $a = sin($dLat / 2) ** 2
            + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLon / 2) ** 2;

        return $earthRadiusKm * 2 * atan2(sqrt($a), sqrt(1 - $a));
    }
}
