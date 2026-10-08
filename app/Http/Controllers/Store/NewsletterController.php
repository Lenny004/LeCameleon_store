<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Http\Requests\Store\NewsletterRequest;
use App\Mail\NewsletterConfirm;
use App\Models\NewsletterSubscriber;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\View\View;

class NewsletterController extends Controller
{
    public function store(NewsletterRequest $request): RedirectResponse
    {
        // Respuesta idéntica exista o no la suscripción: no revela qué correos están suscritos.
        $subscriber = NewsletterSubscriber::query()->firstOrNew(['email' => $request->validated('email')]);
        if ($subscriber->exists && $subscriber->confirmed_at !== null && $subscriber->unsubscribed_at === null) {
            return back()->with('success', '¡Gracias! Te avisaremos cuando haya novedades y hallazgos vintage.');
        }

        $wasUnsubscribed = $subscriber->unsubscribed_at !== null;
        $subscriber->forceFill([
            'token' => $wasUnsubscribed || ! $subscriber->token ? Str::random(64) : $subscriber->token,
            'subscribed_at' => now(),
            'confirmed_at' => null,
            'unsubscribed_at' => null,
        ])->save();
        Mail::to($subscriber->email)->queue((new NewsletterConfirm($subscriber))->afterCommit());

        return back()->with('success', '¡Gracias! Te avisaremos cuando haya novedades y hallazgos vintage.');
    }

    public function confirm(string $token): View
    {
        $subscriber = NewsletterSubscriber::query()->where('token', $token)->firstOrFail();
        $subscriber->forceFill(['confirmed_at' => now(), 'unsubscribed_at' => null])->save();

        return view('store.newsletter-confirmed');
    }

    public function unsubscribe(string $token): View
    {
        $subscriber = NewsletterSubscriber::query()->where('token', $token)->firstOrFail();
        $subscriber->forceFill(['unsubscribed_at' => now()])->save();

        return view('store.newsletter-unsubscribed');
    }
}
