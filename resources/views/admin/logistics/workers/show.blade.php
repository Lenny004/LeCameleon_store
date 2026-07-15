@extends('layouts.admin')

@section('title', $worker->fullName() . ' — Workers')
@section('page-title', $worker->fullName())

@section('content')
<div class="card logistics-detail">
    <dl class="logistics-detail__list">
        <div><dt>DUI</dt><dd>{{ $worker->document_id ?: '—' }}</dd></div>
        <div><dt>Role</dt><dd>{{ ucfirst($worker->role->value) }}</dd></div>
        <div><dt>Company</dt><dd>{{ $worker->company?->name ?? '—' }}</dd></div>
    </dl>
</div>
@endsection
