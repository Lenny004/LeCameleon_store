<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

/**
 * Terms of use page for the storefront.
 */
class TermsController extends Controller
{
    public function __invoke(): View
    {
        return view('store.terms');
    }
}
