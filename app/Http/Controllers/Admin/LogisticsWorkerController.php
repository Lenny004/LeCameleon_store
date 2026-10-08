<?php

namespace App\Http\Controllers\Admin;

use App\Enums\LogisticsWorkerRole;
use App\Http\Controllers\Controller;
use App\Models\LogisticsCompany;
use App\Models\LogisticsWorker;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class LogisticsWorkerController extends Controller
{
    public function index(): View
    {
        $workers = LogisticsWorker::query()
            ->with('company')
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->paginate(20);

        return view('admin.logistics.workers.index', compact('workers'));
    }

    public function create(): View
    {
        return view('admin.logistics.workers.create', [
            'companies' => $this->companyOptions(),
            'roles' => LogisticsWorkerRole::cases(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $worker = LogisticsWorker::query()->create($this->validated($request));

        return redirect()
            ->route('admin.logistics.workers.show', $worker)
            ->with('success', 'Worker created.');
    }

    public function show(LogisticsWorker $worker): View
    {
        return view('admin.logistics.workers.show', [
            'worker' => $worker->load(['company', 'assignedVehicles']),
        ]);
    }

    public function edit(LogisticsWorker $worker): View
    {
        return view('admin.logistics.workers.edit', [
            'worker' => $worker,
            'companies' => $this->companyOptions(),
            'roles' => LogisticsWorkerRole::cases(),
        ]);
    }

    public function update(Request $request, LogisticsWorker $worker): RedirectResponse
    {
        $worker->update($this->validated($request));

        return redirect()
            ->route('admin.logistics.workers.show', $worker)
            ->with('success', 'Worker updated.');
    }

    public function destroy(LogisticsWorker $worker): RedirectResponse
    {
        $worker->delete();

        return redirect()
            ->route('admin.logistics.workers.index')
            ->with('success', 'Worker removed.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validated(Request $request): array
    {
        return $request->validate([
            'logistics_company_id' => ['nullable', 'uuid', 'exists:logistics_companies,id'],
            'employee_code' => ['nullable', 'string', 'max:30'],
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'document_id' => ['nullable', 'string', 'max:20'],
            'phone' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:150'],
            'role' => ['required', Rule::enum(LogisticsWorkerRole::class)],
            'hire_date' => ['nullable', 'date'],
            'is_active' => ['sometimes', 'boolean'],
            'notes' => ['nullable', 'string'],
        ]) + ['is_active' => $request->boolean('is_active')];
    }

    private function companyOptions()
    {
        return LogisticsCompany::query()->where('is_active', true)->orderBy('name')->get();
    }
}
