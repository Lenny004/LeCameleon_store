<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DispatchSchedule;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DispatchScheduleController extends Controller
{
    public function index(): View
    {
        $schedules = DispatchSchedule::query()->orderBy('next_dispatch_at')->paginate(20);

        return view('admin.logistics.dispatch.index', compact('schedules'));
    }

    public function store(Request $request): RedirectResponse
    {
        DispatchSchedule::query()->create($this->validated($request));

        return back()->with('success', 'Dispatch schedule created.');
    }

    public function update(Request $request, DispatchSchedule $schedule): RedirectResponse
    {
        $schedule->update($this->validated($request));

        return back()->with('success', 'Dispatch schedule updated.');
    }

    public function destroy(DispatchSchedule $schedule): RedirectResponse
    {
        $schedule->delete();

        return back()->with('success', 'Dispatch schedule removed.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validated(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'next_dispatch_at' => ['required', 'date'],
            'cutoff_at' => ['required', 'date'],
            'is_active' => ['sometimes', 'boolean'],
            'notes' => ['nullable', 'string'],
        ]) + ['is_active' => $request->boolean('is_active')];
    }
}
