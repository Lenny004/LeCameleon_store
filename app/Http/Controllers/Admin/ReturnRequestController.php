<?php

namespace App\Http\Controllers\Admin;

use App\Enums\ReturnRequestStatus;
use App\Http\Controllers\Controller;
use App\Mail\ReturnRequestApproved;
use App\Mail\ReturnRequestDenied;
use App\Mail\ReturnRequestRefunded;
use App\Models\ReturnRequest;
use App\Services\PaymentService;
use App\Support\RecordsActivity;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class ReturnRequestController extends Controller
{
    public function __construct(
        private readonly PaymentService $paymentService,
    ) {}

    public function index(): View
    {
        $returnRequests = ReturnRequest::query()
            ->with(['order', 'orderItem', 'user'])
            ->orderByDesc('created_at')
            ->paginate(20);

        return view('admin.return-requests.index', compact('returnRequests'));
    }

    public function approve(Request $request, ReturnRequest $returnRequest): RedirectResponse
    {
        $validated = $request->validate(['admin_notes' => ['nullable', 'string', 'max:2000']]);

        $returnRequest->update([
            'status' => ReturnRequestStatus::Approved,
            'admin_notes' => $validated['admin_notes'] ?? null,
        ]);
        $this->notifyCustomer($returnRequest, ReturnRequestApproved::class);

        RecordsActivity::log('return.approved', $returnRequest->order, [
            'return_request_id' => $returnRequest->id,
        ]);

        return back()->with('success', 'Solicitud de devolución aprobada.');
    }

    public function deny(Request $request, ReturnRequest $returnRequest): RedirectResponse
    {
        $validated = $request->validate(['admin_notes' => ['nullable', 'string', 'max:2000']]);

        $returnRequest->update([
            'status' => ReturnRequestStatus::Denied,
            'admin_notes' => $validated['admin_notes'] ?? null,
        ]);
        $this->notifyCustomer($returnRequest, ReturnRequestDenied::class);

        RecordsActivity::log('return.denied', $returnRequest->order, [
            'return_request_id' => $returnRequest->id,
        ]);

        return back()->with('success', 'Solicitud de devolución rechazada.');
    }

    public function refund(Request $request, ReturnRequest $returnRequest): RedirectResponse
    {
        $validated = $request->validate(['admin_notes' => ['nullable', 'string', 'max:2000']]);

        if ($returnRequest->status !== ReturnRequestStatus::Approved) {
            return back()->with('error', 'Solo las devoluciones aprobadas pueden marcarse como reembolsadas.');
        }

        $returnRequest->load(['order.payments', 'orderItem']);

        $order = $returnRequest->order;

        if (! $order) {
            return back()->with('error', 'No se encontró el pedido de esta devolución.');
        }

        $amount = $returnRequest->orderItem
            ? (float) $returnRequest->orderItem->line_total
            : (float) $order->grand_total;

        $note = (string) (($validated['admin_notes'] ?? null)
            ?: "Solicitud de devolución #{$returnRequest->id} reembolsada");

        $this->paymentService->recordRefund($order, $amount, $note);

        $returnRequest->update([
            'status' => ReturnRequestStatus::Refunded,
            'admin_notes' => $validated['admin_notes'] ?? $returnRequest->admin_notes,
        ]);
        $this->notifyCustomer($returnRequest, ReturnRequestRefunded::class);

        RecordsActivity::log('return.refunded', $order, [
            'return_request_id' => $returnRequest->id,
            'refund_amount' => $amount,
        ]);

        return back()->with('success', 'Devolución marcada como reembolsada.');
    }

    private function notifyCustomer(ReturnRequest $returnRequest, string $mailClass): void
    {
        $returnRequest->loadMissing(['order', 'user']);
        $email = $returnRequest->order?->customerEmail() ?? $returnRequest->user?->email;
        if ($email) {
            Mail::to($email)->queue((new $mailClass($returnRequest))->afterCommit());
        }
    }
}
