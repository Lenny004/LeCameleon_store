<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class SizeGuideController extends Controller
{
    public function __invoke(): View
    {
        return view('store.size-guide');
    }
}
