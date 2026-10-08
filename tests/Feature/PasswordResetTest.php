<?php

namespace Tests\Feature;

use App\Models\User;
use App\Notifications\ResetPasswordNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class PasswordResetTest extends TestCase
{
    use RefreshDatabase;

    public function test_existing_and_unknown_email_have_the_same_reset_response(): void
    {
        Notification::fake();
        $user = User::factory()->create(['email' => 'known@example.com']);

        $known = $this->post(route('password.email'), ['email' => $user->email]);
        $unknown = $this->post(route('password.email'), ['email' => 'unknown@example.com']);

        $known->assertRedirect()->assertSessionHas('success', __('passwords.sent'));
        $unknown->assertRedirect()->assertSessionHas('success', __('passwords.sent'));
        Notification::assertSentTo($user, ResetPasswordNotification::class);
    }

    public function test_valid_reset_token_changes_password(): void
    {
        $user = User::factory()->create(['password' => 'old-password']);
        $token = Password::broker()->createToken($user);

        $this->post(route('password.update'), [
            'token' => $token,
            'email' => $user->email,
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
        ])->assertRedirect(route('login'));

        $this->assertTrue(Hash::check('new-password', $user->fresh()->password));
    }

    public function test_invalid_reset_token_fails(): void
    {
        $user = User::factory()->create();

        $this->from(route('password.reset', 'invalid'))
            ->post(route('password.update'), [
                'token' => 'invalid',
                'email' => $user->email,
                'password' => 'new-password',
                'password_confirmation' => 'new-password',
            ])
            ->assertRedirect()
            ->assertSessionHasErrors('email');
    }

    public function test_account_password_change_requires_current_password(): void
    {
        $user = User::factory()->create(['password' => 'old-password']);

        $this->actingAs($user)
            ->put(route('account.password.update'), [
                'current_password' => 'wrong-password',
                'password' => 'new-password',
                'password_confirmation' => 'new-password',
            ])
            ->assertSessionHasErrors('current_password');

        $this->assertTrue(Hash::check('old-password', $user->fresh()->password));
    }
}
