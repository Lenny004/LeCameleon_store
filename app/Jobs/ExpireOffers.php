<?php

namespace App\Jobs;

use App\Enums\OfferStatus;
use App\Models\Offer;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class ExpireOffers implements ShouldQueue
{
    use Queueable;

    public function handle(): void
    {
        Offer::query()
            ->whereIn('status', [OfferStatus::Accepted, OfferStatus::Countered])
            ->whereNotNull('expires_at')
            ->where('expires_at', '<=', now())
            ->whereNull('order_id')
            ->update(['status' => OfferStatus::Expired]);
    }
}
