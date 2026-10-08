<?php

namespace Tests\Feature;

use App\Mail\AccountAlreadyExists;
use App\Models\User;
use App\Notifications\VerifyEmailNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class EmailVerificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_new_and_duplicate_registration_have_the_same_response(): void
    {
        Notification::fake();
        Mail::fake();
        $existing = User::factory()->create(['email' => 'existing@example.com']);

        $new = $this->post(route('register'), [
            'name' => 'Nuevo Usuario',
            'email' => 'new@example.com',
            'password' => 'valid-password',
            'password_confirmation' => 'valid-password',
        ]);
        $duplicate = $this->post(route('register'), [
            'name' => 'Otro Usuario',
            'email' => $existing->email,
            'password' => 'valid-password',
            'password_confirmation' => 'valid-password',
        ]);

        $new->assertRedirect(route('login'))->assertSessionHas('success', 'Te enviamos un correo para continuar. Revisa tu bandeja de entrada.');
        $duplicate->assertRedirect(route('login'))->assertSessionHas('success', 'Te enviamos un correo para continuar. Revisa tu bandeja de entrada.');
        Notification::assertSentTo(User::query()->where('email', 'new@example.com')->first(), VerifyEmailNotification::class);
        Mail::assertQueued(AccountAlreadyExists::class);
    }

    public function test_signed_link_verifies_email_and_dispatches_verified_event(): void
    {
        Event::fake([Verified::class]);
        $user = User::factory()->unverified()->create();
        $url = URL::temporarySignedRoute('verification.verify', now()->addMinutes(10), [
            'id' => $user->id,
            'hash' => sha1($user->getEmailForVerification()),
        ]);

        $this->get($url)->assertRedirect(route('login'));

        $this->assertTrue($user->fresh()->hasVerifiedEmail());
        Event::assertDispatched(Verified::class);
    }

    public function test_unverified_user_is_redirected_from_account(): void
    {
        $user = User::factory()->unverified()->create();

        $this->actingAs($user)
            ->get(route('account.profile'))
            ->assertRedirect(route('verification.notice'));
    }

    public function test_changing_email_clears_verification(): void
    {
        Notification::fake();
        $user = User::factory()->create(['email' => 'old@example.com']);

        $this->actingAs($user)
            ->put(route('account.profile.update'), [
                'name' => $user->name,
                'email' => 'new@example.com',
                'phone' => $user->phone,
            ])
            ->assertRedirect();

        $this->assertNull($user->fresh()->email_verified_at);
        Notification::assertSentTo($user->fresh(), VerifyEmailNotification::class);
    }
}
