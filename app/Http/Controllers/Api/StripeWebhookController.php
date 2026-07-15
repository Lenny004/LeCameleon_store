<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\PaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class StripeWebhookController extends Controller
{
    public function __construct(
        private readonly PaymentService $paymentService,
    ) {}

    public function __invoke(Request $request): JsonResponse
    {
        $rawPayload = $request->getContent();
        $payload = json_decode($rawPayload, true);

        if (! is_array($payload)) {
            return response()->json(['error' => 'Invalid JSON payload'], 400);
        }

        $webhookSecret = config('services.stripe.webhook_secret');

        if (empty($webhookSecret)) {
            // Local/dev only: accept unsigned webhooks when no secret is configured.
            Log::warning('Stripe webhook received without STRIPE_WEBHOOK_SECRET; signature not verified.');
        } elseif (! $this->verifyStripeSignature($request->header('Stripe-Signature'), $rawPayload, $webhookSecret)) {
            return response()->json(['error' => 'Invalid signature'], 400);
        }

        $this->paymentService->handleStripeWebhookPayload($payload);

        return response()->json(['received' => true]);
    }

    /**
     * Naive Stripe webhook signature check (timestamp.payload HMAC-SHA256).
     *
     * TODO: For production, prefer stripe-php Webhook::constructEvent() which handles
     * clock skew tolerance and multiple v1 signatures. Empty webhook_secret = local only.
     */
    private function verifyStripeSignature(?string $signatureHeader, string $payload, string $secret): bool
    {
        if (! is_string($signatureHeader) || $signatureHeader === '') {
            return false;
        }

        $timestamp = null;
        $signatures = [];

        foreach (explode(',', $signatureHeader) as $part) {
            [$key, $value] = array_pad(explode('=', trim($part), 2), 2, null);

            if ($key === 't') {
                $timestamp = $value;
            }

            if ($key === 'v1' && is_string($value)) {
                $signatures[] = $value;
            }
        }

        if ($timestamp === null || $signatures === []) {
            return false;
        }

        $signedPayload = $timestamp.'.'.$payload;
        $expected = hash_hmac('sha256', $signedPayload, $secret);

        foreach ($signatures as $signature) {
            if (hash_equals($expected, $signature)) {
                return true;
            }
        }

        return false;
    }
}
