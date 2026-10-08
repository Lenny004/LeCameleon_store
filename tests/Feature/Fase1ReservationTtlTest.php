<?php

namespace Tests\Feature;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Jobs\ReleaseExpiredReservations;
use App\Models\Order;
use App\Models\Payment;
use App\Services\OrderService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class Fase1ReservationTtlTest extends TestCase
{
    use RefreshDatabase;

    public function test_expired_stripe_reservation_is_cancelled(): void
    {
        Mail::fake();
        config()->set('store.reservation_ttl_minutes', 30);
        config()->set('store.manual_payment_ttl_hours', 72);

        $order = $this->orderWithPayment('stripe', now()->subMinutes(31));

        $this->runJob();

        $this->assertSame(OrderStatus::Cancelled, $order->fresh()->status);
    }

    public function test_manual_reservation_at_31_minutes_is_not_cancelled(): void
    {
        Mail::fake();
        config()->set('store.manual_payment_ttl_hours', 72);
        $order = $this->orderWithPayment('manual', now()->subMinutes(31));

        $this->runJob();

        $this->assertSame(OrderStatus::Pending, $order->fresh()->status);
    }

    public function test_manual_reservation_at_73_hours_is_cancelled(): void
    {
        Mail::fake();
        config()->set('store.manual_payment_ttl_hours', 72);
        $order = $this->orderWithPayment('manual', now()->subHours(73));

        $this->runJob();

        $this->assertSame(OrderStatus::Cancelled, $order->fresh()->status);
    }

    public function test_zero_manual_ttl_never_cancels_manual_reservations(): void
    {
        Mail::fake();
        config()->set('store.manual_payment_ttl_hours', 0);
        $order = $this->orderWithPayment('manual', now()->subDays(30));

        $this->runJob();

        $this->assertSame(OrderStatus::Pending, $order->fresh()->status);
    }

    private function orderWithPayment(string $provider, Carbon $createdAt): Order
    {
        $order = Order::factory()->create();

        $payment = Payment::query()->create([
            'order_id' => $order->id,
            'provider' => $provider,
            'status' => PaymentStatus::Pending,
            'amount' => $order->grand_total,
        ]);
        $payment->forceFill(['created_at' => $createdAt, 'updated_at' => $createdAt])->saveQuietly();

        return $order;
    }

    private function runJob(): void
    {
        app(ReleaseExpiredReservations::class)->handle(app(OrderService::class));
    }
}
