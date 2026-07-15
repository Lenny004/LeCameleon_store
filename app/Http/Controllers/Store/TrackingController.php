<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Services\ShipmentTrackingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Guest shipment tracking lookup by public tracking code (e.g. LC-SV-XXXXXX).
 */
class TrackingController extends Controller
{
  public function __construct(
    private readonly ShipmentTrackingService $trackingService,
  ) {}

  public function index(): View
  {
    return view('store.tracking.index');
  }

  public function lookup(Request $request): RedirectResponse
  {
    $validated = $request->validate([
      'code' => ['required', 'string', 'max:50'],
    ]);

    return redirect()->route('tracking.show', [
      'code' => strtoupper(trim($validated['code'])),
    ]);
  }

  public function show(string $code): View
  {
    $normalizedCode = strtoupper(trim($code));
    $shipment = $this->trackingService->findByTrackingCode($normalizedCode);

    if ($shipment) {
      $shipment->load([
        'destinationMunicipality.department',
        'events' => fn ($query) => $query->orderBy('happened_at'),
      ]);
    }

    return view('store.tracking.show', [
      'code' => $normalizedCode,
      'shipment' => $shipment,
    ]);
  }
}
