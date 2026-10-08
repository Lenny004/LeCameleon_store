<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Http\Requests\Store\StockAlertRequest;
use App\Models\Product;
use App\Services\StockAlertService;
use Illuminate\Http\RedirectResponse;

class StockAlertController extends Controller
{
    public function __construct(
        private readonly StockAlertService $stockAlertService,
    ) {}

    public function store(StockAlertRequest $request, Product $product): RedirectResponse
    {
        $this->stockAlertService->subscribe(
            $product,
            $request->validated('email'),
            $request->user(),
        );

        return back()->with('success', 'Te avisaremos cuando vuelva a estar disponible.');
    }
}
