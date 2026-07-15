@extends('layouts.admin')

@section('title', ($user->name ?? 'New user') . ' — Users')
@section('page-title', isset($user) ? 'Edit user' : 'New user')

@section('content')
<form method="POST" action="{{ isset($user) ? route('admin.users.update', $user) : route('admin.users.store') }}" style="max-width:40rem;display:flex;flex-direction:column;gap:var(--space-xl);">
    @csrf
    @if (isset($user))
        @method('PUT')
    @endif

    <div class="card">
        <h2 class="card__title" style="margin-bottom:var(--space-lg);">Account</h2>
        <div style="display:flex;flex-direction:column;gap:var(--space-md);">
            <div class="form-group">
                <label class="form-label" for="name">Name</label>
                <input type="text" id="name" name="name" class="form-input" value="{{ old('name', $user->name ?? '') }}" required>
            </div>
            <div class="form-group">
                <label class="form-label" for="email">Email</label>
                <input type="email" id="email" name="email" class="form-input" value="{{ old('email', $user->email ?? '') }}" required>
            </div>
            <div class="form-group">
                <label class="form-label" for="password">Password{{ isset($user) ? ' (leave blank to keep)' : '' }}</label>
                <input type="password" id="password" name="password" class="form-input" {{ isset($user) ? '' : 'required' }}>
            </div>
            <div class="form-group">
                <label class="form-label" for="role">Role</label>
                <select id="role" name="role" class="form-select" required>
                    @foreach ($roles as $role)
                        <option value="{{ $role->value }}" {{ old('role', $user->role->value ?? '') === $role->value ? 'selected' : '' }}>{{ ucfirst($role->value) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $user->is_active ?? true) ? 'checked' : '' }}>
                    Active
                </label>
            </div>
        </div>
    </div>

    <div style="display:flex;gap:var(--space-sm);">
        <button type="submit" class="btn btn--primary">{{ isset($user) ? 'Save user' : 'Create user' }}</button>
        <a href="{{ route('admin.users.index') }}" class="btn btn--ghost">Cancel</a>
    </div>
</form>
@endsection
