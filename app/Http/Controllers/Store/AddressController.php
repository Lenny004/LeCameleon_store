<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Concerns\LoadsServiceableMunicipalities;
use App\Http\Controllers\Controller;
use App\Http\Requests\Store\AddressRequest;
use App\Models\Address;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AddressController extends Controller
{
    use LoadsServiceableMunicipalities;

    public function index(Request $request): View
    {
        return view('store.account.addresses.index', [
            'addresses' => $request->user()->addresses()->with('municipality')->orderByDesc('is_default')->latest()->get(),
        ]);
    }

    public function create(): View
    {
        return view('store.account.addresses.create', ['address' => new Address(['country' => 'SV']), 'departments' => $this->serviceableDepartmentsWithMunicipalities()]);
    }

    public function store(AddressRequest $request): RedirectResponse
    {
        $user = $request->user();
        if ($user->addresses()->count() >= 10) {
            return back()
                ->withErrors(['addresses' => 'Puedes guardar como máximo 10 direcciones.'])
                ->withInput();
        }

        $data = $request->validated();
        $data['is_default'] = (bool) ($data['is_default'] ?? false) || ! $user->addresses()->exists();
        $address = $user->addresses()->create($data);
        if ($address->is_default) {
            $user->addresses()->whereKeyNot($address->id)->update(['is_default' => false]);
        }

        return redirect()->route('account.addresses.index')->with('success', 'Dirección guardada.');
    }

    public function edit(Request $request, Address $address): View
    {
        $this->authorize('view', $address);

        return view('store.account.addresses.edit', ['address' => $address, 'departments' => $this->serviceableDepartmentsWithMunicipalities()]);
    }

    public function update(AddressRequest $request, Address $address): RedirectResponse
    {
        $this->authorize('update', $address);
        $address->update($request->validated());
        if ($address->is_default) {
            $request->user()->addresses()->whereKeyNot($address->id)->update(['is_default' => false]);
        }

        return redirect()->route('account.addresses.index')->with('success', 'Dirección actualizada.');
    }

    public function destroy(Request $request, Address $address): RedirectResponse
    {
        $this->authorize('delete', $address);
        $wasDefault = $address->is_default;
        $address->delete();
        if ($wasDefault) {
            $request->user()->addresses()->latest()->first()?->update(['is_default' => true]);
        }

        return back()->with('success', 'Dirección eliminada.');
    }

    public function makeDefault(Request $request, Address $address): RedirectResponse
    {
        $this->authorize('update', $address);
        $request->user()->addresses()->update(['is_default' => false]);
        $address->update(['is_default' => true]);

        return back()->with('success', 'Dirección predeterminada actualizada.');
    }
}
