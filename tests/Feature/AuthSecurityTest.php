<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Tests\TestCase;

class AuthSecurityTest extends TestCase
{
    use RefreshDatabase;

    public function test_staff_cannot_promote_themselves_through_user_administration(): void
    {
        $staff = User::factory()->staff()->create();

        $this->actingAs($staff)
            ->put(route('admin.users.update', $staff), [
                'name' => $staff->name,
                'email' => $staff->email,
                'role' => UserRole::Admin->value,
                'is_active' => true,
                'password' => 'new-password',
            ])
            ->assertForbidden();

        $this->assertSame(UserRole::Staff, $staff->fresh()->role);
    }

    public function test_profile_update_normalizes_email_and_ignores_administrative_fields(): void
    {
        $customer = User::factory()->customer()->create();
        $originalPassword = $customer->password;

        $this->actingAs($customer)
            ->put(route('account.profile.update'), [
                'name' => 'Updated Customer',
                'email' => '  CUSTOMER@Example.COM ',
                'phone' => '555-0100',
                'role' => UserRole::Admin->value,
                'is_active' => false,
                'password' => 'changed-by-profile',
            ])
            ->assertRedirect();

        $customer->refresh();

        $this->assertSame('customer@example.com', $customer->email);
        $this->assertSame(UserRole::Customer, $customer->role);
        $this->assertTrue($customer->is_active);
        $this->assertSame($originalPassword, $customer->password);
    }

    public function test_login_normalizes_email_and_clears_attempts_after_success(): void
    {
        $user = User::factory()->create([
            'email' => 'customer@example.com',
            'password' => 'correct-password',
        ]);

        $this->post('/login', [
            'email' => ' CUSTOMER@EXAMPLE.COM ',
            'password' => 'wrong-password',
        ])->assertSessionHasErrors(['email' => 'Las credenciales no son correctas.']);

        $this->post('/login', [
            'email' => ' CUSTOMER@EXAMPLE.COM ',
            'password' => 'correct-password',
        ])->assertRedirect(route('home'));

        $this->assertAuthenticatedAs($user);
        $this->assertSame(0, RateLimiter::attempts('customer@example.com|127.0.0.1'));
    }

    public function test_registration_normalizes_email_before_unique_validation(): void
    {
        User::factory()->create(['email' => 'existing@example.com']);

        $this->post('/register', [
            'name' => 'Duplicate User',
            'email' => ' EXISTING@Example.COM ',
            'password' => 'valid-password',
            'password_confirmation' => 'valid-password',
        ])->assertSessionHasErrors([
            'email' => 'No pudimos crear la cuenta con este correo. Si ya tienes una cuenta, inicia sesión.',
        ]);

        $this->assertDatabaseCount('users', 1);
    }

    public function test_login_is_rate_limited_by_normalized_email_and_ip(): void
    {
        Event::fake([Lockout::class]);

        for ($attempt = 0; $attempt < 5; $attempt++) {
            $email = $attempt % 2 === 0 ? ' Target@Example.COM ' : 'target@example.com';

            $this->withServerVariables(['REMOTE_ADDR' => '203.0.113.10'])
                ->post('/login', [
                    'email' => $email,
                    'password' => 'wrong-password',
                ])
                ->assertSessionHasErrors('email');
        }

        $this->withServerVariables(['REMOTE_ADDR' => '203.0.113.10'])
            ->post('/login', [
                'email' => 'target@example.com',
                'password' => 'wrong-password',
            ])
            ->assertSessionHasErrors('email');

        Event::assertDispatched(Lockout::class);
    }

    public function test_inactive_accounts_cannot_login_or_keep_an_authenticated_session(): void
    {
        $inactive = User::factory()->inactive()->create([
            'email' => 'inactive@example.com',
            'password' => 'correct-password',
        ]);

        $this->post('/login', [
            'email' => 'inactive@example.com',
            'password' => 'correct-password',
        ])->assertSessionHasErrors(['email' => 'Las credenciales no son correctas.']);

        $this->assertGuest();

        $this->actingAs($inactive)
            ->get(route('account.profile'))
            ->assertRedirect(route('login'));

        $this->assertGuest();
    }

    public function test_login_uses_the_same_generic_message_for_missing_and_wrong_credentials(): void
    {
        $user = User::factory()->create([
            'email' => 'known@example.com',
            'password' => 'correct-password',
        ]);

        $missingUserResponse = $this->post('/login', [
            'email' => 'missing@example.com',
            'password' => 'wrong-password',
        ]);

        $wrongPasswordResponse = $this->post('/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        $missingUserResponse->assertSessionHasErrors(['email' => 'Las credenciales no son correctas.']);
        $wrongPasswordResponse->assertSessionHasErrors(['email' => 'Las credenciales no son correctas.']);
    }

    public function test_registration_empty_fields_are_named_in_spanish(): void
    {
        $this->post('/register', [])->assertSessionHasErrors([
            'name' => 'El campo nombre es obligatorio.',
            'email' => 'El campo correo electrónico es obligatorio.',
            'password' => 'El campo contraseña es obligatorio.',
            'password_confirmation' => 'El campo confirmación de contraseña es obligatorio.',
        ]);
    }

    public function test_registration_rejects_a_name_longer_than_the_database_column(): void
    {
        $this->post('/register', [
            'name' => str_repeat('A', 256),
            'email' => 'long-name@example.com',
            'password' => 'valid-password',
            'password_confirmation' => 'valid-password',
        ])->assertSessionHasErrors([
            'name' => 'El campo nombre no debe tener más de 255 caracteres.',
        ]);
    }

    public function test_admin_can_manage_user_security_fields(): void
    {
        $admin = User::factory()->admin()->create();
        $customer = User::factory()->customer()->create();

        $this->actingAs($admin)
            ->put(route('admin.users.update', $customer), [
                'name' => $customer->name,
                'email' => ' STAFF@Example.COM ',
                'role' => UserRole::Staff->value,
                'is_active' => false,
                'password' => 'updated-password',
            ])
            ->assertRedirect(route('admin.users.show', $customer));

        $customer->refresh();

        $this->assertSame('staff@example.com', $customer->email);
        $this->assertSame(UserRole::Staff, $customer->role);
        $this->assertFalse($customer->is_active);
        $this->assertTrue(Hash::check('updated-password', $customer->password));
    }
}
