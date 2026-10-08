<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Http\Requests\Store\NewsletterRequest;
use App\Models\NewsletterSubscriber;
use Illuminate\Http\RedirectResponse;

class NewsletterController extends Controller
{
    public function store(NewsletterRequest $request): RedirectResponse
    {
        // Respuesta idéntica exista o no la suscripción: no revela qué correos están suscritos.
        $subscriber = NewsletterSubscriber::query()->firstOrCreate(
            ['email' => $request->validated('email')],
            ['subscribed_at' => now()],
        );

        if ($subscriber->unsubscribed_at !== null) {
            $subscriber->update(['subscribed_at' => now(), 'unsubscribed_at' => null]);
        }

        return back()->with('success', '¡Gracias! Te avisaremos cuando haya novedades y hallazgos vintage.');
    }
}
