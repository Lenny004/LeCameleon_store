@extends('layouts.admin')

@section('title', ($user->name ?? 'Nuevo usuario') . ' — Usuarios')
@section('page-title', isset($user) ? 'Editar usuario' : 'Nuevo usuario')

@section('content')
<form method="POST" action="{{ isset($user) ? route('admin.users.update', $user) : route('admin.users.store') }}" class="admin-form admin-form--narrow">
    @csrf
    @if (isset($user))
        @method('PUT')
    @endif

    <p class="form-required-note">Los campos con <span class="form-label__required" aria-hidden="true">*</span> son obligatorios.</p>

    <div class="card">
        <h2 class="card__title admin-form__title">Cuenta</h2>
        <div class="admin-form__fields">
            <div class="form-group">
                <label class="form-label" for="name">Nombre <span class="form-label__required" aria-hidden="true">*</span></label>
                <input type="text" id="name" name="name" class="form-input @error('name') form-input--error @enderror" value="{{ old('name', $user->name ?? '') }}" placeholder="María López" maxlength="255" required autocomplete="name" @error('name') aria-invalid="true" aria-describedby="name-error" @enderror>
                @error('name')<span class="form-error" id="name-error">{{ $message }}</span>@enderror
            </div>
            <div class="form-group">
                <label class="form-label" for="email">Correo electrónico <span class="form-label__required" aria-hidden="true">*</span></label>
                <input type="email" id="email" name="email" class="form-input @error('email') form-input--error @enderror" value="{{ old('email', $user->email ?? '') }}" placeholder="tu@correo.com" maxlength="255" inputmode="email" required autocomplete="email" @error('email') aria-invalid="true" aria-describedby="email-error" @enderror>
                @error('email')<span class="form-error" id="email-error">{{ $message }}</span>@enderror
            </div>
            <div class="form-group">
                <label class="form-label" for="password">Contraseña{{ isset($user) ? ' (deja vacío para conservarla)' : '' }}@unless(isset($user)) <span class="form-label__required" aria-hidden="true">*</span>@endunless</label>
                <input type="password" id="password" name="password" class="form-input @error('password') form-input--error @enderror" placeholder="Mínimo 8 caracteres" minlength="8" maxlength="255" autocomplete="new-password" {{ isset($user) ? '' : 'required' }} @error('password') aria-invalid="true" aria-describedby="password-error" @enderror>
                @error('password')<span class="form-error" id="password-error">{{ $message }}</span>@enderror
            </div>
            <div class="form-group">
                <label class="form-label" for="role">Rol <span class="form-label__required" aria-hidden="true">*</span></label>
                <select id="role" name="role" class="form-select @error('role') form-select--error @enderror" required @error('role') aria-invalid="true" aria-describedby="role-error" @enderror>
                    <option value="" disabled {{ old('role', $user->role->value ?? '') === '' ? 'selected' : '' }}>Selecciona un rol</option>
                    @foreach ($roles as $role)
                        <option value="{{ $role->value }}" {{ old('role', $user->role->value ?? '') === $role->value ? 'selected' : '' }}>{{ match ($role->value) { 'admin' => 'Administrador', 'staff' => 'Personal', default => 'Cliente' } }}</option>
                    @endforeach
                </select>
                @error('role')<span class="form-error" id="role-error">{{ $message }}</span>@enderror
            </div>
            <div class="form-group">
                <label class="form-label">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $user->is_active ?? true) ? 'checked' : '' }}>
                    Activo
                </label>
            </div>
        </div>
    </div>

    <div class="admin-form__actions">
        <button type="submit" class="btn btn--primary">{{ isset($user) ? 'Guardar usuario' : 'Crear usuario' }}</button>
        <a href="{{ route('admin.users.index') }}" class="btn btn--ghost">Cancelar</a>
    </div>
</form>
@endsection
