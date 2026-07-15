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
        NewsletterSubscriber::query()->create([
            'email' => $request->validated('email'),
            'subscribed_at' => now(),
        ]);

        return back()->with('success', '¡Gracias! Te avisaremos cuando haya novedades y hallazgos vintage.');
    }
}
