<?php

namespace Tests\Feature;

use App\Enums\OrderStatus;
use App\Enums\ReturnRequestStatus;
use App\Mail\OrderDelivered;
use App\Mail\PaymentReceiptRejected;
use App\Mail\PaymentReceiptUploaded;
use App\Mail\ReturnRequestApproved;
use App\Mail\ReturnRequestDenied;
use App\Mail\ReturnRequestRefunded;
use App\Mail\WelcomeMail;
use App\Models\Order;
use App\Models\Payment;
use App\Models\ReturnRequest;
use App\Models\User;
use App\Services\OrderService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class Fase2MailsTest extends TestCase
{
    use RefreshDatabase;

    public function test_order_delivered_mail_is_sent_on_status_change(): void
    {
        Mail::fake();
        $order = Order::factory()->create(['status' => OrderStatus::Shipped]);

        app(OrderService::class)->transition($order, OrderStatus::Delivered);

        Mail::assertQueued(OrderDelivered::class, 1);
    }

    public function test_welcome_mail_is_sent_only_on_first_email_verification(): void
    {
        Mail::fake();
        $user = User::factory()->unverified()->create();
        $url = URL::temporarySignedRoute('verification.verify', now()->addMinutes(10), [
            'id' => $user->id,
            'hash' => sha1($user->getEmailForVerification()),
        ]);

        $this->get($url)->assertRedirect(route('login'));
        $this->get($url)->assertRedirect(route('login'));
        Mail::assertQueued(WelcomeMail::class, 1);
    }

    public function test_return_mails_are_sent_and_include_admin_note_data(): void
    {
        Mail::fake();
        $admin = User::factory()->admin()->create();
        $customer = User::factory()->customer()->create();
        $order = Order::factory()->for($customer)->create(['status' => OrderStatus::Paid]);
        Payment::create(['order_id' => $order->id, 'provider' => 'transfer', 'status' => 'captured', 'amount' => $order->grand_total]);

        $approved = ReturnRequest::create(['order_id' => $order->id, 'user_id' => $customer->id, 'reason' => 'No era lo esperado']);
        $this->actingAs($admin)->patch(route('admin.return-requests.approve', $approved), ['admin_notes' => 'Aprobada'])->assertRedirect();
        Mail::assertQueued(ReturnRequestApproved::class, 1);

        $denied = ReturnRequest::create(['order_id' => $order->id, 'user_id' => $customer->id, 'reason' => 'Otra razón']);
        $this->actingAs($admin)->patch(route('admin.return-requests.deny', $denied), ['admin_notes' => 'No aplica'])->assertRedirect();
        Mail::assertQueued(ReturnRequestDenied::class, 1);

        $this->actingAs($admin)->patch(route('admin.return-requests.refund', $approved), ['admin_notes' => 'Reembolso emitido'])->assertRedirect();
        Mail::assertQueued(ReturnRequestRefunded::class, 1);
        $this->assertDatabaseHas('return_requests', ['id' => $approved->id, 'status' => ReturnRequestStatus::Refunded->value]);
    }

    public function test_receipt_upload_and_rejection_mails_are_sent(): void
    {
        Mail::fake();
        Storage::fake('local');
        $customer = User::factory()->customer()->create();
        $admin = User::factory()->admin()->create();
        $order = Order::factory()->for($customer)->create();
        $payment = Payment::create(['order_id' => $order->id, 'provider' => 'transfer', 'status' => 'pending', 'amount' => $order->grand_total]);

        $this->actingAs($customer)
            ->post(route('account.orders.receipts.store', $order), ['receipt' => UploadedFile::fake()->create('pago.pdf', 100, 'application/pdf')])
            ->assertRedirect();
        Mail::assertQueued(PaymentReceiptUploaded::class, 1);

        $receipt = $order->paymentReceipts()->firstOrFail();
        $this->actingAs($admin)
            ->patch(route('admin.orders.receipts.reject', [$order, $receipt]), ['admin_notes' => 'La imagen no es legible'])
            ->assertRedirect();
        Mail::assertQueued(PaymentReceiptRejected::class, 1);
    }
}
