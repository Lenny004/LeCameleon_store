<?php

namespace Tests\Feature;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Enums\ReturnRequestStatus;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Product;
use App\Models\ReturnRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReturnRefundTest extends TestCase
{
    use RefreshDatabase;

    public function test_staff_can_mark_approved_return_refunded_and_records_payment(): void
    {
        $staff = User::factory()->staff()->create();
        $customer = User::factory()->customer()->create();
        $product = Product::factory()->create();

        $order = Order::factory()->create([
            'user_id' => $customer->id,
            'status' => OrderStatus::Paid,
            'grand_total' => 120.00,
        ]);

        $orderItem = $order->items()->create([
            'product_id' => $product->id,
            'name' => $product->name,
            'sku' => $product->sku,
            'quantity' => 1,
            'unit_price' => 120.00,
            'line_total' => 120.00,
        ]);

        Payment::query()->create([
            'order_id' => $order->id,
            'provider' => 'manual',
            'status' => PaymentStatus::Captured,
            'amount' => 120.00,
            'transaction_reference' => 'manual-test',
        ]);

        $returnRequest = ReturnRequest::query()->create([
            'order_id' => $order->id,
            'order_item_id' => $orderItem->id,
            'user_id' => $customer->id,
            'reason' => 'Wrong size',
            'status' => ReturnRequestStatus::Approved,
        ]);

        $this->actingAs($staff)
            ->patch(route('admin.return-requests.refund', $returnRequest))
            ->assertRedirect()
            ->assertSessionHas('success');

        $returnRequest->refresh();

        $this->assertSame(ReturnRequestStatus::Refunded, $returnRequest->status);

        $this->assertDatabaseHas('payments', [
            'order_id' => $order->id,
            'provider' => 'manual',
            'status' => PaymentStatus::Refunded->value,
            'amount' => '120.00',
        ]);
    }

    public function test_refund_without_captured_payment_does_not_create_refund_row(): void
    {
        $staff = User::factory()->staff()->create();
        $customer = User::factory()->customer()->create();

        $order = Order::factory()->create([
            'user_id' => $customer->id,
            'status' => OrderStatus::Pending,
            'grand_total' => 80.00,
        ]);

        Payment::query()->create([
            'order_id' => $order->id,
            'provider' => 'manual',
            'status' => PaymentStatus::Pending,
            'amount' => 80.00,
        ]);

        $returnRequest = ReturnRequest::query()->create([
            'order_id' => $order->id,
            'user_id' => $customer->id,
            'reason' => 'Changed mind',
            'status' => ReturnRequestStatus::Approved,
        ]);

        $this->actingAs($staff)
            ->patch(route('admin.return-requests.refund', $returnRequest))
            ->assertRedirect()
            ->assertSessionHas('success');

        $returnRequest->refresh();

        $this->assertSame(ReturnRequestStatus::Refunded, $returnRequest->status);
        $this->assertDatabaseMissing('payments', [
            'order_id' => $order->id,
            'status' => PaymentStatus::Refunded->value,
        ]);
    }
}
