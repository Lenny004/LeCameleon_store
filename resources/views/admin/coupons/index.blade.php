@extends('layouts.admin')

@section('title', 'Coupons')
@section('page-title', 'Coupons')

@section('content')
<div class="admin-page-header">
    <h2 class="admin-page-header__title">Discount codes</h2>
    @if (Route::has('admin.coupons.create'))
        <a href="{{ route('admin.coupons.create') }}" class="btn btn--primary">Create coupon</a>
    @endif
</div>

<div class="table-wrap">
    <table class="table">
        <thead>
            <tr>
                <th>Code</th>
                <th>Type</th>
                <th>Value</th>
                <th>Uses</th>
                <th>Expires</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($coupons ?? [
                ['code' => 'VINTAGE10', 'type' => 'percent', 'value' => '10%', 'uses' => '12/100', 'expires' => '2026-12-31', 'active' => true],
                ['code' => 'WELCOME20', 'type' => 'fixed', 'value' => '$20', 'uses' => '45/∞', 'expires' => '—', 'active' => true],
                ['code' => 'SUMMER24', 'type' => 'percent', 'value' => '15%', 'uses' => '89/200', 'expires' => '2026-08-31', 'active' => false],
            ] as $coupon)
                <tr>
                    <td><code>{{ $coupon['code'] }}</code></td>
                    <td>{{ ucfirst($coupon['type']) }}</td>
                    <td>{{ $coupon['value'] }}</td>
                    <td>{{ $coupon['uses'] }}</td>
                    <td>{{ $coupon['expires'] }}</td>
                    <td>
                        <span class="badge badge--{{ $coupon['active'] ? 'success' : 'warning' }}">
                            {{ $coupon['active'] ? 'Active' : 'Expired' }}
                        </span>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
