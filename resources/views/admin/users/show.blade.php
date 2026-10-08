@extends('layouts.admin')

@section('title', $user->name . ' — Usuarios')
@section('page-title', $user->name)

@section('content')
@php $roleLabels = ['admin' => 'Administrador', 'staff' => 'Personal', 'customer' => 'Cliente']; @endphp
<div class="card admin-panel admin-panel--narrow">
    <p><strong>Correo:</strong> {{ $user->email }}</p>
    <p><strong>Rol:</strong> <span class="badge badge--{{ $user->role->value === 'admin' ? 'accent' : 'primary' }}">{{ $roleLabels[$user->role->value] ?? $user->role->value }}</span></p>
    <p><strong>Estado:</strong> <span class="badge badge--{{ $user->is_active ? 'success' : 'warning' }}">{{ $user->is_active ? 'Activo' : 'Inactivo' }}</span></p>
    <p><strong>Pedidos:</strong> {{ $user->orders_count }}</p>
    <p><strong>Registro:</strong> {{ $user->created_at?->format('Y-m-d') }}</p>
    <div class="admin-actions admin-actions--spaced">
        <a href="{{ route('admin.users.edit', $user) }}" class="btn btn--primary">Editar</a>
        <a href="{{ route('admin.users.index') }}" class="btn btn--ghost">Volver</a>
    </div>
</div>
@endsection
