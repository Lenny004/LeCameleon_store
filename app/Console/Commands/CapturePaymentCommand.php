<?php

namespace App\Console\Commands;

use App\Models\Order;
use App\Services\PaymentService;
use Illuminate\Console\Command;

class CapturePaymentCommand extends Command
{
    protected $signature = 'payment:capture {order : Order number or UUID}';

    protected $description = 'Mark a pending manual payment as captured and transition the order to paid';

    public function handle(PaymentService $paymentService): int
    {
        $identifier = $this->argument('order');

        $order = Order::query()
            ->where('number', $identifier)
            ->orWhere('id', $identifier)
            ->first();

        if (! $order) {
            $this->error("Order not found: {$identifier}");

            return self::FAILURE;
        }

        $paymentService->capturePayment($order);

        $this->info("Payment captured for order {$order->number}.");

        return self::SUCCESS;
    }
}
