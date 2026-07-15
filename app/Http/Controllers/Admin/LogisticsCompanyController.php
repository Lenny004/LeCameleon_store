<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LogisticsCompany;
use App\Models\Municipality;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LogisticsCompanyController extends Controller
{
    public function index(): View
    {
        $companies = LogisticsCompany::query()
            ->with('municipality.department')
            ->withCount(['workers', 'vehicles'])
            ->orderBy('name')
            ->paginate(20);

        return view('admin.logistics.companies.index', compact('companies'));
    }

    public function create(): View
    {
        return view('admin.logistics.companies.create', [
            'municipalities' => $this->municipalityOptions(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $company = LogisticsCompany::query()->create($data);

        return redirect()
            ->route('admin.logistics.companies.show', $company)
            ->with('success', 'Logistics company created.');
    }

    public function show(LogisticsCompany $company): View
    {
        return view('admin.logistics.companies.show', [
            'company' => $company->load(['municipality.department', 'workers', 'vehicles']),
        ]);
    }

    public function edit(LogisticsCompany $company): View
    {
        return view('admin.logistics.companies.edit', [
            'company' => $company,
            'municipalities' => $this->municipalityOptions(),
        ]);
    }

    public function update(Request $request, LogisticsCompany $company): RedirectResponse
    {
        $company->update($this->validated($request));

        return redirect()
            ->route('admin.logistics.companies.show', $company)
            ->with('success', 'Logistics company updated.');
    }

    public function destroy(LogisticsCompany $company): RedirectResponse
    {
        $company->delete();

        return redirect()
            ->route('admin.logistics.companies.index')
            ->with('success', 'Logistics company deleted.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validated(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'nit' => ['required', 'string', 'max:20'],
            'contact_name' => ['required', 'string', 'max:255'],
            'contact_email' => ['nullable', 'email', 'max:255'],
            'contact_phone' => ['nullable', 'string', 'max:30'],
            'municipality_id' => ['nullable', 'exists:municipalities,id'],
            'notes' => ['nullable', 'string'],
            'is_active' => ['sometimes', 'boolean'],
        ]) + ['is_active' => $request->boolean('is_active')];
    }

    /**
     * @return \Illuminate\Support\Collection<int, Municipality>
     */
    private function municipalityOptions()
    {
        return Municipality::query()
            ->with('department')
            ->orderBy('name')
            ->get();
    }
}
