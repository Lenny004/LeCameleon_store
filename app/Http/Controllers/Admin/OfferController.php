<?php

namespace App\Http\Controllers\Admin;

use App\Enums\OfferStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CounterOfferRequest;
use App\Mail\OfferResponded;
use App\Models\Offer;
use App\Support\RecordsActivity;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class OfferController extends Controller
{
    public function index(): View
    {
        $offers = Offer::query()
            ->with(['product', 'user'])
            ->orderByRaw("CASE WHEN status = 'pending' THEN 0 ELSE 1 END")
            ->orderByDesc('created_at')
            ->paginate(20);

        return view('admin.offers.index', compact('offers'));
    }

    public function accept(Request $request, Offer $offer): RedirectResponse
    {
        abort_unless($offer->isPending(), 422);

        $validated = $request->validate(['admin_notes' => ['nullable', 'string', 'max:2000']]);

        $offer->update([
            'status' => OfferStatus::Accepted,
            'accepted_amount' => $offer->amount,
            'responded_at' => now(),
            'expires_at' => now()->addHours((int) config('store.offer_acceptance_hours', 48)),
            'admin_notes' => $validated['admin_notes'] ?? null,
        ]);

        RecordsActivity::log('offer.accepted', $offer);
        $this->notifyBuyer($offer);

        return back()->with('success', 'Oferta aceptada.');
    }

    public function decline(Request $request, Offer $offer): RedirectResponse
    {
        abort_unless($offer->isPending(), 422);

        $validated = $request->validate(['admin_notes' => ['nullable', 'string', 'max:2000']]);

        $offer->update([
            'status' => OfferStatus::Declined,
            'responded_at' => now(),
            'admin_notes' => $validated['admin_notes'] ?? null,
        ]);

        RecordsActivity::log('offer.declined', $offer);
        $this->notifyBuyer($offer);

        return back()->with('success', 'Oferta rechazada.');
    }

    public function counter(CounterOfferRequest $request, Offer $offer): RedirectResponse
    {
        abort_unless($offer->isPending(), 422);

        $offer->update([
            'status' => OfferStatus::Countered,
            'counter_amount' => $request->validated('counter_amount'),
            'responded_at' => now(),
            'expires_at' => now()->addHours((int) config('store.offer_acceptance_hours', 48)),
            'admin_notes' => $request->validated('admin_notes'),
        ]);

        RecordsActivity::log('offer.countered', $offer, [
            'counter_amount' => $request->validated('counter_amount'),
        ]);
        $this->notifyBuyer($offer);

        return back()->with('success', 'Contraoferta enviada.');
    }

    private function notifyBuyer(Offer $offer): void
    {
        $offer->loadMissing(['product', 'user']);

        if ($offer->user?->email) {
            Mail::to($offer->user->email)->queue((new OfferResponded($offer))->afterCommit());
        }
    }
}
