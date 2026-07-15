<?php

namespace App\Http\Controllers\Admin;

use App\Enums\WorkerRole;
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
            ->orderBy('full_name')
            ->paginate(20);

        return view('admin.logistics.workers.index', compact('workers'));
    }

    public function create(): View
    {
        return view('admin.logistics.workers.create', [
            'companies' => $this->companyOptions(),
            'roles' => WorkerRole::cases(),
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
            'worker' => $worker->load(['company', 'vehicles']),
        ]);
    }

    public function edit(LogisticsWorker $worker): View
    {
        return view('admin.logistics.workers.edit', [
            'worker' => $worker,
            'companies' => $this->companyOptions(),
            'roles' => WorkerRole::cases(),
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
            ->with('success', 'Worker deleted.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validated(Request $request): array
    {
        return $request->validate([
            'logistics_company_id' => ['required', 'exists:logistics_companies,id'],
            'full_name' => ['required', 'string', 'max:255'],
            'dui' => ['required', 'string', 'max:15'],
            'role' => ['required', Rule::enum(WorkerRole::class)],
            'phone' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:255'],
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
}
