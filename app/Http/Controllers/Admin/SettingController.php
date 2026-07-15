<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SettingController extends Controller
{
    public function index(): View
    {
        return view('admin.settings.index', [
            'settings' => Setting::query()->orderBy('key')->get(),
            'storeConfig' => config('store'),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'settings' => ['required', 'array'],
            'settings.*.key' => ['required', 'string', 'max:100'],
            'settings.*.value' => ['nullable'],
        ]);

        foreach ($data['settings'] as $entry) {
            Setting::query()->updateOrCreate(
                ['key' => $entry['key']],
                ['value' => $entry['value'] ?? null],
            );
        }

        return back()->with('success', 'Settings saved.');
    }
}
