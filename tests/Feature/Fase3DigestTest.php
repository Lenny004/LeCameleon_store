<?php

namespace Tests\Feature;

use App\Jobs\SendSavedSearchDigests;
use App\Mail\SavedSearchDigest;
use App\Models\Product;
use App\Models\SavedSearch;
use App\Models\User;
use App\Services\CatalogService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class Fase3DigestTest extends TestCase
{
    use RefreshDatabase;

    public function test_digest_sends_only_new_matching_products_and_updates_timestamp(): void
    {
        Mail::fake();
        $user = User::factory()->create();
        $search = SavedSearch::query()->create([
            'user_id' => $user->id,
            'name' => 'Vestidos',
            'query_params' => ['q' => 'vestido'],
            'last_notified_at' => now()->subDay(),
        ]);
        $product = Product::factory()->create([
            'name' => 'Vestido nuevo',
            'published_at' => now(),
        ]);
        Product::factory()->create([
            'name' => 'Vestido anterior',
            'published_at' => now()->subDays(2),
        ]);

        (new SendSavedSearchDigests)->handle(app(CatalogService::class));

        Mail::assertQueued(SavedSearchDigest::class, function (SavedSearchDigest $mail) use ($product): bool {
            return collect($mail->products)->contains(fn (Product $item): bool => $item->id === $product->id);
        });
        $this->assertTrue($search->fresh()->last_notified_at->greaterThan(now()->subMinute()));
    }

    public function test_digest_skips_no_matches_disabled_notifications_and_unverified_users(): void
    {
        Mail::fake();
        $verified = User::factory()->create();
        $unverified = User::factory()->unverified()->create();
        SavedSearch::query()->create([
            'user_id' => $verified->id,
            'name' => 'Sin coincidencias',
            'query_params' => ['q' => 'no existe'],
            'last_notified_at' => now()->subDay(),
        ]);
        SavedSearch::query()->create([
            'user_id' => $verified->id,
            'name' => 'Desactivada',
            'query_params' => ['q' => 'pieza'],
            'notify' => false,
            'last_notified_at' => now()->subDay(),
        ]);
        SavedSearch::query()->create([
            'user_id' => $unverified->id,
            'name' => 'Sin verificar',
            'query_params' => ['q' => 'pieza'],
            'last_notified_at' => now()->subDay(),
        ]);
        Product::factory()->create(['name' => 'Pieza nueva', 'published_at' => now()]);

        (new SendSavedSearchDigests)->handle(app(CatalogService::class));

        Mail::assertNothingQueued();
    }

    public function test_user_cannot_toggle_notifications_for_another_users_search(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $search = SavedSearch::query()->create([
            'user_id' => $owner->id,
            'name' => 'Privada',
            'query_params' => ['q' => 'vintage'],
        ]);

        $this->actingAs($other)
            ->patch(route('account.saved-searches.notify', $search))
            ->assertForbidden();
    }
}
