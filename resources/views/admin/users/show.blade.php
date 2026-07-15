@extends('layouts.admin')

@section('title', $user->name . ' — Users')
@section('page-title', $user->name)

@section('content')
<div class="card" style="max-width:40rem;">
    <p><strong>Email:</strong> {{ $user->email }}</p>
    <p><strong>Role:</strong> <span class="badge badge--{{ $user->role->value === 'admin' ? 'accent' : 'primary' }}">{{ ucfirst($user->role->value) }}</span></p>
    <p><strong>Status:</strong> <span class="badge badge--{{ $user->is_active ? 'success' : 'warning' }}">{{ $user->is_active ? 'Active' : 'Inactive' }}</span></p>
    <p><strong>Orders:</strong> {{ $user->orders_count }}</p>
    <p><strong>Joined:</strong> {{ $user->created_at?->format('Y-m-d') }}</p>
    <div style="margin-top:var(--space-lg);display:flex;gap:var(--space-md);">
        <a href="{{ route('admin.users.edit', $user) }}" class="btn btn--primary">Edit</a>
        <a href="{{ route('admin.users.index') }}" class="btn btn--ghost">Back</a>
    </div>
</div>
@endsection
