<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ContactMessageController extends Controller
{
    public function index(): View
    {
        $messages = ContactMessage::query()
            ->orderByRaw('read_at IS NOT NULL')
            ->orderByDesc('created_at')
            ->paginate(25);

        return view('admin.messages.index', compact('messages'));
    }

    public function markRead(ContactMessage $message): RedirectResponse
    {
        $message->markAsRead();

        return back()->with('success', 'Message marked as read.');
    }
}
