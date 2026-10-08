@extends('layouts.admin')

@section('title', $vehicle->plate_number . ' — Vehículos')
@section('page-title', $vehicle->plate_number)

@section('content')
<div class="card logistics-detail">
    <dl class="logistics-detail__list">
        <div><dt class="logistics-detail__term">Tipo</dt><dd class="logistics-detail__value">{{ ucfirst($vehicle->vehicle_type->value) }}</dd></div>
        <div><dt class="logistics-detail__term">Empresa</dt><dd class="logistics-detail__value">{{ $vehicle->company?->name ?? '—' }}</dd></div>
        <div><dt class="logistics-detail__term">Personal</dt><dd class="logistics-detail__value">{{ $vehicle->driver?->fullName() ?? '—' }}</dd></div>
    </dl>
</div>
@endsection
