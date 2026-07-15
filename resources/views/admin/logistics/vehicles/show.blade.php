@extends('layouts.admin')

@section('title', $vehicle->plate_number . ' — Vehicles')
@section('page-title', $vehicle->plate_number)

@section('content')
<div class="card logistics-detail">
    <dl class="logistics-detail__list">
        <div><dt>Type</dt><dd>{{ ucfirst($vehicle->vehicle_type->value) }}</dd></div>
        <div><dt>Company</dt><dd>{{ $vehicle->company?->name ?? '—' }}</dd></div>
        <div><dt>Worker</dt><dd>{{ $vehicle->driver?->fullName() ?? '—' }}</dd></div>
    </dl>
</div>
@endsection
