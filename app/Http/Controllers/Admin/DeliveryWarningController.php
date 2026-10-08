<?php

namespace App\Http\Controllers\Admin;

use App\Enums\WarningAppliesTo;
use App\Enums\WarningSeverity;
use App\Http\Controllers\Controller;
use App\Models\DeliveryWarning;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class DeliveryWarningController extends Controller
{
    public function index(): View
    {
        $warnings = DeliveryWarning::query()->orderByDesc('created_at')->paginate(20);

        return view('admin.logistics.warnings.index', [
            'warnings' => $warnings,
            'severities' => WarningSeverity::cases(),
            'appliesTo' => WarningAppliesTo::cases(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        DeliveryWarning::query()->create($this->validated($request));

        return back()->with('success', 'Delivery warning created.');
    }

    public function update(Request $request, DeliveryWarning $warning): RedirectResponse
    {
        $warning->update($this->validated($request));

        return back()->with('success', 'Delivery warning updated.');
    }

    public function destroy(DeliveryWarning $warning): RedirectResponse
    {
        $warning->delete();

        return back()->with('success', 'Delivery warning removed.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validated(Request $request): array
    {
        return $request->validate([
            'code' => ['required', 'string', 'max:50'],
            'title' => ['required', 'string', 'max:200'],
            'body' => ['required', 'string'],
            'severity' => ['required', Rule::enum(WarningSeverity::class)],
            'applies_to' => ['required', Rule::enum(WarningAppliesTo::class)],
            'is_active' => ['sometimes', 'boolean'],
        ]) + ['is_active' => $request->boolean('is_active')];
    }
}
