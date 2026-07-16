@extends('layouts.guest')

@section('title', 'Página no encontrada — Le Cameleon')

@section('content')
<section class="error-page" aria-labelledby="error-title-404">
    <div class="error-page__card">
        <p class="error-page__code" aria-hidden="true">404</p>
        <h1 id="error-title-404" class="error-page__title">Esa pieza no está en el catálogo</h1>
        <p class="error-page__text">
            La página o el producto que buscas no existe, se agotó o cambió de dirección.
        </p>
        <div class="error-page__actions">
            @if (Route::has('home'))
                <a href="{{ route('home') }}" class="btn btn--primary">Volver al inicio</a>
            @endif
            @if (Route::has('shop.index'))
                <a href="{{ route('shop.index') }}" class="btn btn--ghost">Explorar tienda</a>
            @endif
        </div>
    </div>
</section>
@endsection
