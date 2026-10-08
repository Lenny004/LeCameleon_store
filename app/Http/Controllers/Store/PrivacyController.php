<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

/**
 * Privacy policy page (storefront trust content).
 */
class PrivacyController extends Controller
{
    public function __invoke(): View
    {
        return view('store.privacy');
    }
}
