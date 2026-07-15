<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

/**
 * Static care guide for vintage garments and objects.
 */
class CareGuideController extends Controller
{
    public function __invoke(): View
    {
        return view('store.care');
    }
}
