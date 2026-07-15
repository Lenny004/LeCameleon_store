@extends('layouts.admin')

@section('title', 'Users')
@section('page-title', 'Users')
@section('page-subtitle', 'Customers and staff accounts')

@section('content')
<div class="table-wrap">
    <table class="table">
        <thead>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Role</th>
                <th>Orders</th>
                <th>Joined</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @foreach ($users ?? [
                ['name' => 'María García', 'email' => 'maria@email.com', 'role' => 'customer', 'orders' => 5, 'joined' => '2026-01-15'],
                ['name' => 'Carlos Ruiz', 'email' => 'carlos@email.com', 'role' => 'customer', 'orders' => 2, 'joined' => '2026-03-20'],
                ['name' => 'Admin User', 'email' => 'admin@lecameleon.com', 'role' => 'admin', 'orders' => 0, 'joined' => '2025-12-01'],
            ] as $user)
                <tr>
                    <td>{{ $user['name'] }}</td>
                    <td>{{ $user['email'] }}</td>
                    <td><span class="badge badge--{{ $user['role'] === 'admin' ? 'accent' : 'primary' }}">{{ ucfirst($user['role']) }}</span></td>
                    <td>{{ $user['orders'] }}</td>
                    <td>{{ $user['joined'] }}</td>
                    <td>
                        @if (Route::has('admin.users.show'))
                            <a href="{{ route('admin.users.show', $user['email']) }}" class="btn btn--ghost btn--sm">View</a>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
