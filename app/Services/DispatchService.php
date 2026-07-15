<?php

namespace App\Services;

use App\Models\DispatchSchedule;
use Illuminate\Support\Carbon;

/**
 * Resolves the next outbound dispatch window for shipping ETA copy.
 */
class DispatchService
{
    public function nextDispatchAt(): ?Carbon
    {
        $schedule = DispatchSchedule::query()
            ->active()
            ->whereNotNull('next_dispatch_at')
            ->orderBy('next_dispatch_at')
            ->first();

        return $schedule?->next_dispatch_at;
    }
}
