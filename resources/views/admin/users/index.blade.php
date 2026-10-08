@extends('layouts.admin')

@section('title', 'Usuarios')
@section('page-title', 'Usuarios')
@section('page-subtitle', 'Cuentas de clientes y personal')

@section('content')
<div class="table-wrap">
    <table class="table admin-table">
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Correo</th>
                <th>Rol</th>
                <th>Pedidos</th>
                <th>Registro</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @foreach ($users ?? [
                ['name' => 'María García', 'email' => 'maria@email.com', 'role' => 'customer', 'orders' => 5, 'joined' => '2026-01-15'],
                ['name' => 'Carlos Ruiz', 'email' => 'carlos@email.com', 'role' => 'customer', 'orders' => 2, 'joined' => '2026-03-20'],
                ['name' => 'Usuario administrador', 'email' => 'admin@lecameleon.com', 'role' => 'admin', 'orders' => 0, 'joined' => '2025-12-01'],
            ] as $user)
                <tr>
                    <td>{{ $user['name'] }}</td>
                    <td>{{ $user['email'] }}</td>
                    <td><span class="badge badge--{{ $user['role'] === 'admin' ? 'accent' : 'primary' }}">{{ ['admin' => 'Administrador', 'staff' => 'Personal', 'customer' => 'Cliente'][$user['role']] ?? $user['role'] }}</span></td>
                    <td>{{ $user['orders'] }}</td>
                    <td>{{ $user['joined'] }}</td>
                    <td>
                        @if (Route::has('admin.users.show'))
                            <a class="admin-table__link btn btn--ghost btn--sm" href="{{ route('admin.users.show', $user['email']) }}">Ver</a>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
