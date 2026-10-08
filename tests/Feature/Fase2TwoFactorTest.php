<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PragmaRX\Google2FA\Google2FA;
use Tests\TestCase;

class Fase2TwoFactorTest extends TestCase
{
    use RefreshDatabase;

    public function test_required_staff_without_two_factor_is_sent_to_setup(): void
    {
        config(['security.admin_2fa_required' => true]);
        $staff = User::factory()->staff()->create();

        $this->actingAs($staff)
            ->get(route('admin.dashboard'))
            ->assertRedirect(route('admin.two-factor.setup'));
    }

    public function test_invalid_setup_code_returns_validation_error(): void
    {
        config(['security.admin_2fa_required' => true]);
        $staff = User::factory()->staff()->create();

        $this->actingAs($staff)->get(route('admin.two-factor.setup'));

        $this->actingAs($staff)
            ->from(route('admin.two-factor.setup'))
            ->post(route('admin.two-factor.confirm'), ['code' => '000000'])
            ->assertRedirect(route('admin.two-factor.setup'))
            ->assertSessionHasErrors(['code']);
    }

    public function test_confirmed_staff_is_challenged_even_when_setting_is_disabled(): void
    {
        config(['security.admin_2fa_required' => false]);
        $staff = User::factory()->staff()->create();
        $secret = app(Google2FA::class)->generateSecretKey();
        $staff->forceFill([
            'two_factor_secret' => $secret,
            'two_factor_confirmed_at' => now(),
            'two_factor_recovery_codes' => ['RECOVERY-1'],
        ])->save();

        $this->actingAs($staff)
            ->get(route('admin.dashboard'))
            ->assertRedirect(route('two-factor.challenge'));
    }

    public function test_challenge_grants_dashboard_access_and_consumes_recovery_code_once(): void
    {
        $staff = User::factory()->staff()->create();
        $staff->forceFill([
            'two_factor_secret' => app(Google2FA::class)->generateSecretKey(),
            'two_factor_confirmed_at' => now(),
            'two_factor_recovery_codes' => ['RECOVERY-1'],
        ])->save();

        $this->actingAs($staff)
            ->post(route('two-factor.challenge.verify'), ['code' => 'RECOVERY-1'])
            ->assertRedirect(route('admin.dashboard'));

        $this->assertSame([], $staff->fresh()->two_factor_recovery_codes);
        $this->withSession(['two_factor_passed' => false])
            ->from(route('two-factor.challenge'))
            ->post(route('two-factor.challenge.verify'), ['code' => 'RECOVERY-1'])
            ->assertRedirect(route('two-factor.challenge'))
            ->assertSessionHasErrors(['code']);
    }

    public function test_pending_challenge_cannot_disable_two_factor(): void
    {
        $staff = User::factory()->staff()->create();
        $staff->forceFill([
            'two_factor_secret' => app(Google2FA::class)->generateSecretKey(),
            'two_factor_confirmed_at' => now(),
        ])->save();

        $this->actingAs($staff)
            ->post(route('admin.two-factor.disable'), ['current_password' => 'password'])
            ->assertRedirect(route('two-factor.challenge'));

        $this->assertNotNull($staff->fresh()->two_factor_confirmed_at);
    }

    public function test_customer_is_not_affected_by_staff_two_factor(): void
    {
        $customer = User::factory()->customer()->create();

        $this->actingAs($customer)
            ->get(route('account.index'))
            ->assertOk()
            ->assertDontSee('Verificación de seguridad');
    }
}
