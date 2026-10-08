<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SvDepartment;
use App\Models\SvMunicipality;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SvMunicipalityController extends Controller
{
    public function index(): View
    {
        $departments = SvDepartment::query()
            ->where('is_active', true)
            ->with(['municipalities' => fn ($q) => $q->orderBy('name')])
            ->orderBy('name')
            ->get();

        return view('admin.logistics.municipalities.index', compact('departments'));
    }

    public function edit(SvMunicipality $svMunicipality): View
    {
        return view('admin.logistics.municipalities.edit', [
            'municipality' => $svMunicipality->load('department'),
        ]);
    }

    public function update(Request $request, SvMunicipality $svMunicipality): RedirectResponse
    {
        $data = $request->validate([
            'base_shipping_cost' => ['required', 'numeric', 'min:0'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $svMunicipality->update($data + ['is_active' => $request->boolean('is_active', $svMunicipality->is_active)]);

        return redirect()
            ->route('admin.logistics.municipalities.index')
            ->with('success', "Municipality {$svMunicipality->name} updated.");
    }
}
