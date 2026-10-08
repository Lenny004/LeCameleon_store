<?php

namespace App\Jobs;

use App\Mail\SavedSearchDigest;
use App\Models\SavedSearch;
use App\Services\CatalogService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendSavedSearchDigests implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(CatalogService $catalogService): void
    {
        SavedSearch::query()
            ->where('notify', true)
            ->whereHas('user', fn ($query) => $query->whereNotNull('email_verified_at'))
            ->with('user')
            ->chunkById(100, function ($searches) use ($catalogService): void {
                foreach ($searches as $search) {
                    $since = $search->last_notified_at ?: $search->created_at;
                    $products = $catalogService->buildPublishedQuery($search->query_params ?? [])
                        ->where('products.published_at', '>', $since)
                        ->limit(8)
                        ->get();

                    if ($products->isEmpty()) {
                        continue;
                    }

                    Mail::to($search->user->email)->queue((new SavedSearchDigest($search, $products->all()))->afterCommit());
                    $search->forceFill(['last_notified_at' => now()])->save();
                }
            });
    }
}
