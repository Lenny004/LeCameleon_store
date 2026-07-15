@extends('layouts.store')

@section('title', 'Le Cameleon — Vintage con alma')

@section('content')
    <section class="hero">
        <div class="hero__content">
            <h1 class="hero__brand">
                Le
                <span class="hero__brand-accent">Cameleon</span>
            </h1>
            <p class="hero__headline">Piezas únicas que cuentan historias</p>
            <p class="hero__tagline">Descubre moda y objetos vintage seleccionados pieza a pieza, con autenticidad y estilo atemporal.</p>
            @if (Route::has('shop.index'))
                <div class="hero__cta">
                    <a href="{{ route('shop.index') }}" class="btn btn--primary btn--lg">Explorar tienda</a>
                </div>
            @endif
        </div>
        <span class="hero__scroll-hint" aria-hidden="true">Scroll</span>
    </section>

    <section class="featured-strip">
        <div class="container section">
            <div class="section__header">
                <h2 class="section__title">Destacados</h2>
                @if (Route::has('shop.index'))
                    <a href="{{ route('shop.index') }}" class="btn btn--ghost btn--sm">Ver todo</a>
                @endif
            </div>

            @php
                $featured = $featuredProducts ?? collect([
                    (object) ['slug' => 'chaqueta-denim-80s', 'name' => 'Chaqueta Denim 80s', 'price' => 89.00, 'era' => '1980s', 'condition' => 'Excelente', 'image' => null],
                    (object) ['slug' => 'vestido-floral-70s', 'name' => 'Vestido Floral 70s', 'price' => 120.00, 'era' => '1970s', 'condition' => 'Muy bueno', 'image' => null],
                    (object) ['slug' => 'bolso-cuero-vintage', 'name' => 'Bolso Cuero Vintage', 'price' => 65.00, 'era' => '1990s', 'condition' => 'Bueno', 'image' => null],
                    (object) ['slug' => 'camisa-rayas-60s', 'name' => 'Camisa Rayas 60s', 'price' => 45.00, 'era' => '1960s', 'condition' => 'Excelente', 'image' => null],
                ]);
            @endphp

            @if ($featured->count())
                <div class="grid-products">
                    @foreach ($featured as $product)
                        @include('components.product-card', ['product' => $product])
                    @endforeach
                </div>
            @else
                @include('components.empty-state', [
                    'title' => 'Próximamente',
                    'text' => 'Estamos preparando piezas increíbles para ti.',
                ])
            @endif
        </div>
    </section>

    <section class="container section">
        <div class="collection-banner">
            <div class="collection-banner__text">
                <h2 class="heading-2">Nueva colección</h2>
                <p class="text-lead">Prendas y accesorios de décadas pasadas, restaurados y listos para volver a brillar en tu guardarropa.</p>
                @if (Route::has('shop.index'))
                    <a href="{{ route('shop.index', ['sort' => 'new']) }}" class="btn btn--primary">Ver novedades</a>
                @endif
            </div>
            <div class="collection-banner__image" role="img" aria-label="Colección vintage"></div>
        </div>
    </section>
@endsection
