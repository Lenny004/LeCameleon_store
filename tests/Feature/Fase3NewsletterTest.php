<?php

namespace Tests\Feature;

use App\Mail\NewsletterConfirm;
use App\Models\NewsletterSubscriber;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class Fase3NewsletterTest extends TestCase
{
    use RefreshDatabase;

    public function test_subscription_queues_confirmation_and_token_can_confirm_or_unsubscribe(): void
    {
        Mail::fake();
        $this->post(route('newsletter.store'), ['email' => 'cliente@example.com'])->assertRedirect();
        $subscriber = NewsletterSubscriber::query()->firstOrFail();
        Mail::assertQueued(NewsletterConfirm::class);
        $this->get(route('newsletter.confirm', $subscriber->token))->assertOk();
        $this->assertNotNull($subscriber->fresh()->confirmed_at);
        $this->get(route('newsletter.unsubscribe', $subscriber->token))->assertOk();
        $this->assertNotNull($subscriber->fresh()->unsubscribed_at);
        $this->get('/newsletter/confirm/invalid-token')->assertNotFound();
        $this->post('/newsletter/unsubscribe/invalid-token')->assertNotFound();
    }

    public function test_active_confirmed_subscriber_stays_confirmed_without_a_second_email(): void
    {
        Mail::fake();
        $subscriber = NewsletterSubscriber::query()->create([
            'email' => 'confirmado@example.com',
            'token' => 'token-confirmado',
            'subscribed_at' => now()->subDay(),
            'confirmed_at' => now()->subDay(),
        ]);

        $this->post(route('newsletter.store'), ['email' => $subscriber->email])->assertRedirect();

        Mail::assertNothingQueued();
        $this->assertNotNull($subscriber->fresh()->confirmed_at);
        $this->assertNull($subscriber->fresh()->unsubscribed_at);
    }

    public function test_list_unsubscribe_post_works_without_csrf_token(): void
    {
        $subscriber = NewsletterSubscriber::query()->create([
            'email' => 'baja@example.com',
            'token' => 'token-baja',
            'subscribed_at' => now()->subDay(),
            'confirmed_at' => now()->subDay(),
        ]);

        $this->withMiddleware(PreventRequestForgery::class)
            ->post(route('newsletter.unsubscribe.post', $subscriber->token))
            ->assertOk();

        $this->assertNotNull($subscriber->fresh()->unsubscribed_at);
    }

    public function test_confirmation_mailable_exposes_list_unsubscribe_headers(): void
    {
        $subscriber = NewsletterSubscriber::query()->create([
            'email' => 'cabecera@example.com',
            'token' => 'token-cabecera',
            'subscribed_at' => now(),
        ]);

        $headers = (new NewsletterConfirm($subscriber))->headers()->text;

        $this->assertSame('<'.route('newsletter.unsubscribe', $subscriber->token).'>', $headers['List-Unsubscribe']);
        $this->assertSame('List-Unsubscribe=One-Click', $headers['List-Unsubscribe-Post']);
    }
}
