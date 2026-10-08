<?php

namespace App\Http\Controllers\Admin;

use App\Enums\ReturnRequestStatus;
use App\Http\Controllers\Controller;
use App\Models\ReturnRequest;
use App\Services\PaymentService;
use App\Support\RecordsActivity;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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
        $returnRequest->update([
            'status' => ReturnRequestStatus::Approved,
            'admin_notes' => $request->input('admin_notes'),
        ]);

        RecordsActivity::log('return.approved', $returnRequest->order, [
            'return_request_id' => $returnRequest->id,
        ]);

        return back()->with('success', 'Return request approved.');
    }

    public function deny(Request $request, ReturnRequest $returnRequest): RedirectResponse
    {
        $returnRequest->update([
            'status' => ReturnRequestStatus::Denied,
            'admin_notes' => $request->input('admin_notes'),
        ]);

        RecordsActivity::log('return.denied', $returnRequest->order, [
            'return_request_id' => $returnRequest->id,
        ]);

        return back()->with('success', 'Return request denied.');
    }

    public function refund(Request $request, ReturnRequest $returnRequest): RedirectResponse
    {
        if ($returnRequest->status !== ReturnRequestStatus::Approved) {
            return back()->with('error', 'Only approved returns can be marked refunded.');
        }

        $returnRequest->load(['order.payments', 'orderItem']);

        $order = $returnRequest->order;

        if (! $order) {
            return back()->with('error', 'Order not found for this return.');
        }

        $amount = $returnRequest->orderItem
            ? (float) $returnRequest->orderItem->line_total
            : (float) $order->grand_total;

        $note = (string) ($request->input('admin_notes')
            ?: "Return request #{$returnRequest->id} refunded");

        $this->paymentService->recordRefund($order, $amount, $note);

        $returnRequest->update([
            'status' => ReturnRequestStatus::Refunded,
            'admin_notes' => $request->input('admin_notes') ?? $returnRequest->admin_notes,
        ]);

        RecordsActivity::log('return.refunded', $order, [
            'return_request_id' => $returnRequest->id,
            'refund_amount' => $amount,
        ]);

        return back()->with('success', 'Return marked as refunded.');
    }
}
