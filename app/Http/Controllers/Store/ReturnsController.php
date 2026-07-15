<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\View\View;

class ReturnsController extends Controller
{
    public function __invoke(): View
    {
        return view('store.returns', [
            'policyContent' => Setting::getValue('returns_policy'),
        ]);
    }
}
