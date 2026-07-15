<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Concerns\LoadsServiceableMunicipalities;
use App\Http\Controllers\Controller;
use App\Models\LogisticsCompany;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LogisticsCompanyController extends Controller
{
    use LoadsServiceableMunicipalities;

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
            'departments' => $this->serviceableDepartmentsWithMunicipalities(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $company = LogisticsCompany::query()->create($this->validated($request));

        return redirect()
            ->route('admin.logistics.companies.show', $company)
            ->with('success', "Company {$company->name} created.");
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
            'departments' => $this->serviceableDepartmentsWithMunicipalities(),
        ]);
    }

    public function update(Request $request, LogisticsCompany $company): RedirectResponse
    {
        $company->update($this->validated($request));

        return redirect()
            ->route('admin.logistics.companies.show', $company)
            ->with('success', 'Company updated.');
    }

    public function destroy(LogisticsCompany $company): RedirectResponse
    {
        $company->delete();

        return redirect()
            ->route('admin.logistics.companies.index')
            ->with('success', 'Company removed.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validated(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'legal_name' => ['nullable', 'string', 'max:200'],
            'trade_name' => ['nullable', 'string', 'max:150'],
            'tax_id' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:150'],
            'phone' => ['nullable', 'string', 'max:30'],
            'address_line' => ['nullable', 'string', 'max:255'],
            'sv_municipality_id' => ['nullable', 'integer', 'exists:sv_municipalities,id'],
            'website' => ['nullable', 'url', 'max:255'],
            'contact_person' => ['nullable', 'string', 'max:150'],
            'is_active' => ['sometimes', 'boolean'],
            'notes' => ['nullable', 'string'],
        ]) + ['is_active' => $request->boolean('is_active')];
    }
}
