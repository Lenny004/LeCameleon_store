@extends('layouts.admin')

@section('title', $worker->fullName() . ' — Workers')
@section('page-title', $worker->fullName())

@section('content')
<div class="card logistics-detail">
    <dl class="logistics-detail__list">
        <div><dt class="logistics-detail__term">DUI</dt><dd class="logistics-detail__value">{{ $worker->document_id ?: '—' }}</dd></div>
        <div><dt class="logistics-detail__term">Role</dt><dd class="logistics-detail__value">{{ ucfirst($worker->role->value) }}</dd></div>
        <div><dt class="logistics-detail__term">Company</dt><dd class="logistics-detail__value">{{ $worker->company?->name ?? '—' }}</dd></div>
    </dl>
</div>
@endsection
