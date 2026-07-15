@extends('layouts.store')

@section('title', 'Nuestra historia — Le Cameleon')

@section('content')
<div class="container" style="padding-block:var(--space-2xl);max-width:46rem;">
    <p class="text-small text-muted" style="letter-spacing:0.12em;text-transform:uppercase;margin-bottom:var(--space-sm);">Desde 2020</p>
    <h1 class="heading-2" style="margin-bottom:var(--space-lg);">Le Cameleon</h1>

    <div class="checkout-section" style="display:flex;flex-direction:column;gap:var(--space-lg);">
        <p class="text-lead">
            Somos un refugio para piezas con alma: moda vintage, objetos de colección y accesorios que atravesaron décadas
            y llegaron hasta ti con historias que merecen seguir contándose.
        </p>

        <p>
            Cada artículo pasa por un proceso de selección cuidadoso. Buscamos calidad de materiales, autenticidad de época
            y ese detalle irrepetible que convierte una prenda en un hallazgo. No vendemos volumen; curamos un guardarropa
            con carácter.
        </p>

        <h2 class="text-small" style="font-weight:700;">Nuestra promesa</h2>
        <ul class="text-muted" style="padding-left:1.25rem;display:flex;flex-direction:column;gap:var(--space-xs);">
            <li>Descripciones honestas del estado y la procedencia de cada pieza.</li>
            <li>Verificación de autenticidad en artículos de alto valor.</li>
            <li>Embalaje cuidadoso para que tu compra llegue como se merece.</li>
            <li>Atención personal antes y después de cada pedido.</li>
        </ul>

        <p>
            Le Cameleon nació de la pasión por lo atemporal: el denim de los ochenta, el vestido floral de los setenta,
            el bolso de cuero que aún huele a aventuras. Creemos que la moda puede ser sostenible cuando elegimos
            piezas que ya existen y les damos una nueva vida.
        </p>

        <p class="text-muted">
            Gracias por formar parte de esta comunidad de coleccionistas, soñadores y amantes del vintage.
        </p>
    </div>

    @if (Route::has('contact.show'))
        <p style="margin-top:var(--space-xl);">
            <a href="{{ route('contact.show') }}" class="btn btn--primary">Escríbenos</a>
            @if (Route::has('shop.index'))
                <a href="{{ route('shop.index') }}" class="btn btn--ghost" style="margin-left:var(--space-sm);">Explorar tienda</a>
            @endif
        </p>
    @endif
</div>
@endsection
