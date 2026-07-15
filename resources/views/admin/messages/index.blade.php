@extends('layouts.admin')

@section('title', 'Messages')
@section('page-title', 'Messages')
@section('page-subtitle', 'Contact form inbox')

@section('content')
@if (session('success'))
    <div class="alert alert--success" style="margin-bottom:var(--space-md);">{{ session('success') }}</div>
@endif

<div class="table-wrap">
    <table class="table">
        <thead>
            <tr>
                <th>From</th>
                <th>Message</th>
                <th>Status</th>
                <th>Date</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @forelse ($messages as $message)
                <tr @class(['table__row--muted' => $message->read_at])>
                    <td>
                        <strong>{{ $message->name }}</strong>
                        <br><a href="mailto:{{ $message->email }}">{{ $message->email }}</a>
                    </td>
                    <td style="max-width:24rem;white-space:pre-wrap;">{{ Str::limit($message->message, 200) }}</td>
                    <td>
                        @if ($message->read_at)
                            <span class="badge">Read</span>
                        @else
                            <span class="badge badge--warning">Unread</span>
                        @endif
                    </td>
                    <td>{{ $message->created_at?->format('Y-m-d H:i') }}</td>
                    <td>
                        @if ($message->isUnread() && Route::has('admin.messages.read'))
                            <form method="POST" action="{{ route('admin.messages.read', $message) }}">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn--ghost btn--sm">Mark read</button>
                            </form>
                        @else
                            —
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5">No messages yet.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if ($messages->hasPages())
    <div style="margin-top:var(--space-lg);">
        {{ $messages->links() }}
    </div>
@endif
@endsection
