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
        $stored = Setting::query()
            ->whereIn('key', ['store.name', 'store.contact', 'returns_policy'])
            ->get()
            ->keyBy('key');

        return view('admin.settings.index', [
            'settings' => [
                'store_name' => data_get($stored->get('store.name')?->value, 'en', config('app.name', 'Le Cameleon')),
                'contact_email' => data_get($stored->get('store.contact')?->value, 'email'),
                'contact_phone' => data_get($stored->get('store.contact')?->value, 'phone'),
                'returns_policy' => $stored->get('returns_policy')?->value,
            ],
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'store_name' => ['required', 'string', 'max:100'],
            'contact_email' => ['nullable', 'email', 'max:255'],
            'contact_phone' => ['nullable', 'string', 'max:50'],
            'returns_policy' => ['nullable', 'string', 'max:10000'],
        ]);

        Setting::query()->updateOrCreate(
            ['key' => 'store.name'],
            ['value' => ['en' => $data['store_name']]],
        );
        Setting::query()->updateOrCreate(
            ['key' => 'store.contact'],
            ['value' => [
                'email' => $data['contact_email'] ?? null,
                'phone' => $data['contact_phone'] ?? null,
            ]],
        );
        Setting::query()->updateOrCreate(
            ['key' => 'returns_policy'],
            ['value' => $data['returns_policy'] ?? ''],
        );

        return back()->with('success', 'Settings saved.');
    }
}
