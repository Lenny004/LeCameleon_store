<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Http\Requests\Store\OfferRequest;
use App\Models\Product;
use App\Services\OfferService;
use Illuminate\Http\RedirectResponse;

class OfferController extends Controller
{
    public function __construct(
        private readonly OfferService $offerService,
    ) {}

    public function store(OfferRequest $request, Product $product): RedirectResponse
    {
        $this->offerService->submit(
            $product,
            $request->user(),
            (float) $request->validated('amount'),
            $request->validated('message'),
        );

        return back()->with('success', 'Tu oferta fue enviada. Te avisaremos cuando respondamos.');
    }
}
