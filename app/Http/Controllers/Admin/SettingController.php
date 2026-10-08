<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Models\ShippingZone;
use App\Services\PaymentSettingsService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SettingController extends Controller
{
    public function __construct(private readonly PaymentSettingsService $paymentSettingsService) {}

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
            'transfer' => $this->paymentSettingsService->transfer(),
            'cod' => $this->paymentSettingsService->cod(),
            'shippingZones' => ShippingZone::query()->where('is_active', true)->with('municipality')->orderBy('name')->get(),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'store_name' => ['required', 'string', 'max:100'],
            'contact_email' => ['nullable', 'email', 'max:255'],
            'contact_phone' => ['nullable', 'string', 'max:50'],
            'returns_policy' => ['nullable', 'string', 'max:10000'],
            'transfer.enabled' => ['nullable', 'boolean'],
            'transfer.bank' => ['nullable', 'string', 'max:150'],
            'transfer.account_holder' => ['nullable', 'string', 'max:150'],
            'transfer.account_number' => ['nullable', 'string', 'max:100'],
            'transfer.account_type' => ['nullable', 'in:savings,checking'],
            'transfer.instructions' => ['nullable', 'string', 'max:1000'],
            'cod.enabled' => ['nullable', 'boolean'],
            'cod.zone_ids' => ['nullable', 'array'],
            'cod.zone_ids.*' => ['integer', 'exists:shipping_zones,id'],
            'cod.max_amount' => ['nullable', 'numeric', 'min:0', 'max:9999999999.99'],
            'cod.note' => ['nullable', 'string', 'max:1000'],
        ]);

        Setting::query()->updateOrCreate(
            ['key' => 'store.name'],
            ['value' => ['en' => $data['store_name']]],
        );

        Setting::query()->updateOrCreate(['key' => 'payments.transfer'], ['value' => [
            'enabled' => (bool) data_get($data, 'transfer.enabled', false),
            'bank' => data_get($data, 'transfer.bank', ''),
            'account_holder' => data_get($data, 'transfer.account_holder', ''),
            'account_number' => data_get($data, 'transfer.account_number', ''),
            'account_type' => data_get($data, 'transfer.account_type', 'savings'),
            'instructions' => data_get($data, 'transfer.instructions', ''),
        ]]);
        Setting::query()->updateOrCreate(['key' => 'payments.cod'], ['value' => [
            'enabled' => (bool) data_get($data, 'cod.enabled', false),
            'zone_ids' => array_values(array_map('intval', data_get($data, 'cod.zone_ids', []))),
            'max_amount' => data_get($data, 'cod.max_amount'),
            'note' => data_get($data, 'cod.note', ''),
        ]]);
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

        return back()->with('success', 'Configuración guardada.');
    }
}
