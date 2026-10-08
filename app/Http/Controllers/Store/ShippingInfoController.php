<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

/**
 * Static shipping information for the storefront.
 */
class ShippingInfoController extends Controller
{
    public function __invoke(): View
    {
        return view('store.shipping');
    }
}
