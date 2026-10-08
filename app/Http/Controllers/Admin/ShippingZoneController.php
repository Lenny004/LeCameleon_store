<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Concerns\LoadsServiceableMunicipalities;
use App\Http\Controllers\Controller;
use App\Models\ShippingZone;
use App\Models\ShippingZoneRate;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ShippingZoneController extends Controller
{
    use LoadsServiceableMunicipalities;

    public function index(): View
    {
        $zones = ShippingZone::query()
            ->with('municipality')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->paginate(20);

        return view('admin.logistics.zones.index', [
            'zones' => $zones,
            'departments' => $this->serviceableDepartmentsWithMunicipalities(),
        ]);
    }

    public function ratesMatrix(): View
    {
        $zones = ShippingZone::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        $rates = ShippingZoneRate::query()
            ->get()
            ->keyBy(fn (ShippingZoneRate $rate) => "{$rate->origin_zone_id}:{$rate->destination_zone_id}");

        return view('admin.logistics.zones.rates-matrix', compact('zones', 'rates'));
    }

    public function updateRatesMatrix(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'rates' => ['required', 'array'],
            'rates.*' => ['nullable', 'numeric', 'min:0'],
        ]);

        foreach ($validated['rates'] as $key => $fee) {
            if ($fee === null || $fee === '') {
                continue;
            }

            [$originId, $destinationId] = explode(':', $key, 2);

            ShippingZoneRate::query()->updateOrCreate(
                [
                    'origin_zone_id' => (int) $originId,
                    'destination_zone_id' => (int) $destinationId,
                ],
                ['base_fee' => $fee, 'is_active' => true],
            );
        }

        return back()->with('success', 'Zone rates matrix saved.');
    }

    public function store(Request $request): RedirectResponse
    {
        ShippingZone::query()->create($this->validated($request));

        return back()->with('success', 'Shipping zone created.');
    }

    public function update(Request $request, ShippingZone $zone): RedirectResponse
    {
        $zone->update($this->validated($request));

        return back()->with('success', 'Shipping zone updated.');
    }

    public function destroy(ShippingZone $zone): RedirectResponse
    {
        $zone->delete();

        return back()->with('success', 'Shipping zone removed.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validated(Request $request): array
    {
        return $request->validate([
            'code' => ['required', 'string', 'max:30'],
            'name' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string'],
            'sv_municipality_id' => ['nullable', 'integer', 'exists:sv_municipalities,id'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['sometimes', 'boolean'],
        ]) + ['is_active' => $request->boolean('is_active')];
    }
}
