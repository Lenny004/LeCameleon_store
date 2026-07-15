<?php

namespace App\Http\Controllers\Admin;

use App\Enums\ReturnRequestStatus;
use App\Http\Controllers\Controller;
use App\Models\ReturnRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReturnRequestController extends Controller
{
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

        return back()->with('success', 'Return request approved.');
    }

    public function deny(Request $request, ReturnRequest $returnRequest): RedirectResponse
    {
        $returnRequest->update([
            'status' => ReturnRequestStatus::Denied,
            'admin_notes' => $request->input('admin_notes'),
        ]);

        return back()->with('success', 'Return request denied.');
    }
}
