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
            ->with(['company', 'worker'])
            ->orderBy('plate')
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
            'vehicle' => $vehicle->load(['company', 'worker']),
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
            ->with('success', 'Vehicle deleted.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validated(Request $request): array
    {
        return $request->validate([
            'logistics_company_id' => ['required', 'exists:logistics_companies,id'],
            'logistics_worker_id' => ['nullable', 'exists:logistics_workers,id'],
            'plate' => ['required', 'string', 'max:20'],
            'type' => ['required', Rule::enum(VehicleType::class)],
            'description' => ['nullable', 'string', 'max:255'],
            'is_active' => ['sometimes', 'boolean'],
        ]) + ['is_active' => $request->boolean('is_active')];
    }

    /**
     * @return \Illuminate\Support\Collection<int, LogisticsCompany>
     */
    private function companyOptions()
    {
        return LogisticsCompany::query()->orderBy('name')->get();
    }

    /**
     * @return \Illuminate\Support\Collection<int, LogisticsWorker>
     */
    private function workerOptions()
    {
        return LogisticsWorker::query()->with('company')->orderBy('full_name')->get();
    }
}
