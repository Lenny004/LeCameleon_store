@extends('layouts.error')

@section('title', 'Demasiadas solicitudes — Le Cameleon')

@section('content')
<section class="error-page" aria-labelledby="error-title-429">
    <div class="error-page__card">
        <div class="error-page__brand">@include('components.brand-logo', ['variant' => 'compact', 'size' => 'md'])</div>
        <p class="error-page__code" aria-hidden="true">429</p>
        <h1 id="error-title-429" class="error-page__title">Demasiadas solicitudes</h1>
        <p class="error-page__text">Espera un momento antes de volver a intentarlo.</p>
        <div class="error-page__actions">
            <a href="{{ url('/') }}" class="btn btn--primary">Volver al inicio</a>
        </div>
    </div>
</section>
@endsection
