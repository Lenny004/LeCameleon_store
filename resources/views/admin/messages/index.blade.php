@extends('layouts.admin')

@section('title', 'Mensajes')
@section('page-title', 'Mensajes')
@section('page-subtitle', 'Bandeja del formulario de contacto')

@section('content')
<div class="table-wrap">
    <table class="table admin-table">
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
                        <br><a class="admin-table__link" href="mailto:{{ $message->email }}">{{ $message->email }}</a>
                    </td>
                    <td class="admin-table__cell admin-table__cell--message">{{ Str::limit($message->message, 200) }}</td>
                    <td>
                        @if ($message->read_at)
                            <span class="badge">Leído</span>
                        @else
                            <span class="badge badge--warning">No leído</span>
                        @endif
                    </td>
                    <td>{{ $message->created_at?->format('Y-m-d H:i') }}</td>
                    <td>
                        @if ($message->isUnread() && Route::has('admin.messages.read'))
                            <form method="POST" action="{{ route('admin.messages.read', $message) }}">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn--ghost btn--sm">Marcar como leído</button>
                            </form>
                        @else
                            —
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5">Aún no hay mensajes.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if ($messages->hasPages())
    <div class="admin-pagination">
        {{ $messages->links() }}
    </div>
@endif
@endsection
