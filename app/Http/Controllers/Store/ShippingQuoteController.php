<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Concerns\LoadsServiceableMunicipalities;
use App\Http\Controllers\Controller;
use App\Services\ShippingRateService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Public shipping fee calculator for El Salvador municipalities.
 */
class ShippingQuoteController extends Controller
{
  use LoadsServiceableMunicipalities;

  public function __construct(
    private readonly ShippingRateService $rateService,
  ) {}

  public function index(): View
  {
    return view('store.shipping.quote', [
      'departments' => $this->serviceableDepartmentsWithMunicipalities(),
      'flatRate' => (float) config('store.shipping_flat_rate', 0),
    ]);
  }

  /**
   * JSON quote for Alpine.js fetch (checkout + calculator).
   */
  public function calculate(Request $request): JsonResponse
  {
    $validated = $request->validate([
      'destination_municipality_id' => ['required', 'integer', 'exists:sv_municipalities,id'],
    ]);

    $originId = config('store.warehouse_municipality_id');
    $originId = is_numeric($originId) ? (int) $originId : null;

    $quote = $this->rateService->quote(
      $originId,
      (int) $validated['destination_municipality_id'],
    );

    return response()->json($quote);
  }
}
