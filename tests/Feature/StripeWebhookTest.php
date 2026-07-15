<?php

namespace Tests\Feature;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StripeWebhookTest extends TestCase
{
    use RefreshDatabase;

    public function test_webhook_without_secret_captures_pending_payment_by_order_number(): void
    {
        config([
            'services.stripe.webhook_secret' => null,
        ]);

        $product = Product::factory()->create([
            'quantity_available' => 3,
            'quantity_reserved' => 1,
        ]);

        $order = Order::factory()->create([
            'number' => 'LC-TEST-001',
            'status' => OrderStatus::Pending,
            'grand_total' => 150.00,
        ]);

        $payment = Payment::query()->create([
            'order_id' => $order->id,
            'provider' => 'stripe',
            'status' => PaymentStatus::Pending,
            'amount' => 150.00,
            'transaction_reference' => 'pi_test_123',
        ]);

        $order->items()->create([
            'product_id' => $product->id,
            'name' => $product->name,
            'sku' => $product->sku,
            'quantity' => 1,
            'unit_price' => 150.00,
            'line_total' => 150.00,
        ]);

        $payload = [
            'type' => 'payment_intent.succeeded',
            'data' => [
                'object' => [
                    'id' => 'pi_test_123',
                    'metadata' => [
                        'order_number' => $order->number,
                    ],
                ],
            ],
        ];

        $this->postJson(route('api.webhooks.stripe'), $payload)
            ->assertOk()
            ->assertJson(['received' => true]);

        $payment->refresh();
        $order->refresh();

        $this->assertSame(PaymentStatus::Captured, $payment->status);
        $this->assertSame(OrderStatus::Paid, $order->status);
    }

    public function test_webhook_without_secret_captures_pending_payment_by_transaction_reference(): void
    {
        config([
            'services.stripe.webhook_secret' => null,
        ]);

        $order = Order::factory()->create([
            'status' => OrderStatus::Pending,
            'grand_total' => 99.00,
        ]);

        $payment = Payment::query()->create([
            'order_id' => $order->id,
            'provider' => 'stripe',
            'status' => PaymentStatus::Pending,
            'amount' => 99.00,
            'transaction_reference' => 'pi_ref_only',
        ]);

        $payload = [
            'type' => 'checkout.session.completed',
            'data' => [
                'object' => [
                    'id' => 'cs_test_session',
                    'payment_intent' => 'pi_ref_only',
                ],
            ],
        ];

        $this->postJson(route('api.webhooks.stripe'), $payload)
            ->assertOk();

        $payment->refresh();
        $order->refresh();

        $this->assertSame(PaymentStatus::Captured, $payment->status);
        $this->assertSame(OrderStatus::Paid, $order->status);
    }
}
