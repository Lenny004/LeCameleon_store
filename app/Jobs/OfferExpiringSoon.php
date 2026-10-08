<?php

namespace App\Jobs;

use App\Enums\OfferStatus;
use App\Mail\OfferExpiringSoon as OfferExpiringSoonMail;
use App\Models\Offer;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;

class OfferExpiringSoon implements ShouldQueue
{
    use Queueable;

    public function handle(): void
    {
        $offers = Offer::query()->with(['user', 'product'])
            ->whereIn('status', [OfferStatus::Accepted, OfferStatus::Countered])
            ->whereNull('order_id')
            ->whereNull('expiry_notified_at')
            ->whereBetween('expires_at', [now(), now()->addHours(12)])
            ->get();

        foreach ($offers as $offer) {
            if ($offer->user?->email) {
                Mail::to($offer->user->email)->queue((new OfferExpiringSoonMail($offer))->afterCommit());
            }
            $offer->update(['expiry_notified_at' => now()]);
        }
    }
}
