<?php

namespace Tests\Feature;

use App\Models\Offer;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FormsF4Test extends TestCase
{
    use RefreshDatabase;

    public function test_counter_offer_rejects_negative_and_non_numeric_amounts_in_spanish(): void
    {
        $admin = User::factory()->admin()->create();
        $offer = Offer::factory()->create();

        foreach ([-1, 'no-es-un-numero'] as $amount) {
            $response = $this->actingAs($admin)->patch(route('admin.offers.counter', $offer), [
                'counter_amount' => $amount,
            ]);

            $response->assertSessionHasErrors('counter_amount');
            $this->assertStringContainsString(
                'monto de la contraoferta',
                session('errors')->get('counter_amount')[0],
            );
        }
    }

    public function test_logistics_company_name_over_database_limit_fails_with_maximum_message(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->post(route('admin.logistics.companies.store'), [
            'name' => str_repeat('a', 151),
        ]);

        $response->assertSessionHasErrors('name');
        $this->assertSame(
            'El campo nombre no debe tener más de 150 caracteres.',
            session('errors')->get('name')[0],
        );
        $this->assertDatabaseCount('logistics_companies', 0);
    }

    public function test_order_status_rejects_an_invalid_state(): void
    {
        $admin = User::factory()->admin()->create();
        $order = Order::factory()->create();

        $response = $this->actingAs($admin)->patch(route('admin.orders.status', $order), [
            'status' => 'estado-inexistente',
        ]);

        $response->assertSessionHasErrors('status');
        $this->assertStringContainsString('estado', session('errors')->get('status')[0]);
    }
}
