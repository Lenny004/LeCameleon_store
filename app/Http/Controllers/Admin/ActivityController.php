<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\View\View;

class ActivityController extends Controller
{
    public function index(): View
    {
        $logs = ActivityLog::query()
            ->with('user')
            ->orderByDesc('created_at')
            ->limit(100)
            ->get();

        return view('admin.activity.index', compact('logs'));
    }
}
