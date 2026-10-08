@extends('layouts.admin')

@section('title', $vehicle->plate_number . ' — Vehicles')
@section('page-title', $vehicle->plate_number)

@section('content')
<div class="card logistics-detail">
    <dl class="logistics-detail__list">
        <div><dt class="logistics-detail__term">Type</dt><dd class="logistics-detail__value">{{ ucfirst($vehicle->vehicle_type->value) }}</dd></div>
        <div><dt class="logistics-detail__term">Company</dt><dd class="logistics-detail__value">{{ $vehicle->company?->name ?? '—' }}</dd></div>
        <div><dt class="logistics-detail__term">Worker</dt><dd class="logistics-detail__value">{{ $vehicle->driver?->fullName() ?? '—' }}</dd></div>
    </dl>
</div>
@endsection
