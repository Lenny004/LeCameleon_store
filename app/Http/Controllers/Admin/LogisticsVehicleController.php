<?php

namespace App\Http\Controllers\Admin;

use App\Enums\VehicleType;
use App\Http\Controllers\Controller;
use App\Models\LogisticsCompany;
use App\Models\LogisticsVehicle;
use App\Models\LogisticsWorker;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class LogisticsVehicleController extends Controller
{
    public function index(): View
    {
        $vehicles = LogisticsVehicle::query()
            ->with(['company', 'driver'])
            ->orderBy('plate_number')
            ->paginate(20);

        return view('admin.logistics.vehicles.index', compact('vehicles'));
    }

    public function create(): View
    {
        return view('admin.logistics.vehicles.create', [
            'companies' => $this->companyOptions(),
            'workers' => $this->workerOptions(),
            'types' => VehicleType::cases(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $vehicle = LogisticsVehicle::query()->create($this->validated($request));

        return redirect()
            ->route('admin.logistics.vehicles.show', $vehicle)
            ->with('success', 'Vehicle created.');
    }

    public function show(LogisticsVehicle $vehicle): View
    {
        return view('admin.logistics.vehicles.show', [
            'vehicle' => $vehicle->load(['company', 'driver']),
        ]);
    }

    public function edit(LogisticsVehicle $vehicle): View
    {
        return view('admin.logistics.vehicles.edit', [
            'vehicle' => $vehicle,
            'companies' => $this->companyOptions(),
            'workers' => $this->workerOptions(),
            'types' => VehicleType::cases(),
        ]);
    }

    public function update(Request $request, LogisticsVehicle $vehicle): RedirectResponse
    {
        $vehicle->update($this->validated($request));

        return redirect()
            ->route('admin.logistics.vehicles.show', $vehicle)
            ->with('success', 'Vehicle updated.');
    }

    public function destroy(LogisticsVehicle $vehicle): RedirectResponse
    {
        $vehicle->delete();

        return redirect()
            ->route('admin.logistics.vehicles.index')
            ->with('success', 'Vehicle removed.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validated(Request $request): array
    {
        return $request->validate([
            'logistics_company_id' => ['required', 'uuid', 'exists:logistics_companies,id'],
            'logistics_worker_id' => ['nullable', 'uuid', 'exists:logistics_workers,id'],
            'plate_number' => ['required', 'string', 'max:20'],
            'brand' => ['nullable', 'string', 'max:80'],
            'model' => ['nullable', 'string', 'max:80'],
            'year' => ['nullable', 'integer', 'min:1980', 'max:2100'],
            'color' => ['nullable', 'string', 'max:40'],
            'vehicle_type' => ['required', Rule::enum(VehicleType::class)],
            'capacity_kg' => ['nullable', 'numeric', 'min:0'],
            'is_active' => ['sometimes', 'boolean'],
            'notes' => ['nullable', 'string'],
        ]) + ['is_active' => $request->boolean('is_active')];
    }

    private function companyOptions()
    {
        return LogisticsCompany::query()->where('is_active', true)->orderBy('name')->get();
    }

    private function workerOptions()
    {
        return LogisticsWorker::query()
            ->with('company')
            ->where('is_active', true)
            ->orderBy('last_name')
            ->get();
    }
}
