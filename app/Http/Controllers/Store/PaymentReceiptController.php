<?php

namespace App\Http\Controllers\Store;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Mail\PaymentReceiptUploaded;
use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class PaymentReceiptController extends Controller
{
    public function create(Request $request, Order $order): View
    {
        abort_unless($order->paymentMethod() === 'transfer', 404);

        return view('store.checkout.receipt', [
            'order' => $order->load(['payments', 'paymentReceipts']),
            'paymentInstructions' => $order->paymentInstructions(),
            'receiptUploadUrl' => url()->temporarySignedRoute(
                'checkout.receipts.store',
                now()->addDays(7),
                ['order' => $order],
            ),
        ]);
    }

    public function store(Request $request, Order $order): RedirectResponse
    {
        $isOwner = $request->user()?->id === $order->user_id;
        $isSessionOrder = $request->session()->get('last_order_id') === $order->id;
        abort_unless($isOwner || $isSessionOrder || $request->hasValidSignature(), 403);

        if (in_array($order->status, [OrderStatus::Paid, OrderStatus::Cancelled], true)) {
            throw ValidationException::withMessages([
                'receipt' => 'Este pedido ya no admite comprobantes.',
            ]);
        }

        abort_unless($order->paymentMethod() === 'transfer', 422, 'Los comprobantes solo aplican a transferencias bancarias.');

        if ($order->paymentReceipts()->where('status', 'pending')->count() >= 3) {
            throw ValidationException::withMessages(['receipt' => 'Ya tienes 3 comprobantes pendientes para este pedido.']);
        }

        $data = $request->validate([
            'receipt' => ['required', 'file', 'mimes:jpg,jpeg,png,webp,pdf', 'max:5120'],
        ], [], ['receipt' => 'comprobante de pago']);

        $file = $data['receipt'];
        $path = $file->store('receipts/'.$order->id, 'local');
        $receipt = $order->paymentReceipts()->create([
            'payment_id' => $order->payments()->where('provider', 'transfer')->latest()->value('id'),
            'user_id' => $request->user()?->id,
            'path' => $path,
            'original_name' => $file->getClientOriginalName(),
            'mime' => $file->getMimeType() ?: 'application/octet-stream',
            'size' => $file->getSize(),
            'status' => 'pending',
        ]);

        Mail::to(config('mail.from.address'))->queue((new PaymentReceiptUploaded($receipt->load('order')))->afterCommit());

        return back()->with('success', 'Comprobante enviado. Lo revisaremos pronto.');
    }
}
