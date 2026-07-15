<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Http\Requests\Store\ContactRequest;
use App\Models\ContactMessage;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ContactController extends Controller
{
    public function show(): View
    {
        return view('store.contact');
    }

    public function store(ContactRequest $request): RedirectResponse
    {
        ContactMessage::query()->create($request->validated());

        return back()->with('success', 'Gracias por escribirnos. Te responderemos pronto.');
    }
}
