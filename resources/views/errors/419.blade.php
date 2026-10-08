@extends('layouts.error')

@section('title', 'Sesión expirada — Le Cameleon')

@section('content')
<section class="error-page" aria-labelledby="error-title-419">
    <div class="error-page__card">
        <div class="error-page__brand">@include('components.brand-logo', ['variant' => 'compact', 'size' => 'md'])</div>
        <p class="error-page__code" aria-hidden="true">419</p>
        <h1 id="error-title-419" class="error-page__title">Tu sesión expiró</h1>
        <p class="error-page__text">Recarga la página e inténtalo de nuevo.</p>
        <div class="error-page__actions">
            <a href="{{ request()->fullUrl() }}" class="btn btn--primary">Recargar</a>
            <a href="{{ url('/') }}" class="btn btn--ghost">Volver al inicio</a>
        </div>
    </div>
</section>
@endsection
