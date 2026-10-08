@extends('layouts.error')

@section('title', 'Mantenimiento — Le Cameleon')

@section('content')
<section class="error-page" aria-labelledby="error-title-503">
    <div class="error-page__card">
        <div class="error-page__brand">@include('components.brand-logo', ['variant' => 'compact', 'size' => 'md'])</div>
        <p class="error-page__code" aria-hidden="true">503</p>
        <h1 id="error-title-503" class="error-page__title">Estamos en mantenimiento</h1>
        <p class="error-page__text">Regresaremos pronto. Gracias por tu paciencia.</p>
        <div class="error-page__actions">
            <a href="{{ url('/') }}" class="btn btn--primary">Volver al inicio</a>
        </div>
    </div>
</section>
@endsection
