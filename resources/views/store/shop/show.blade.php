@extends('layouts.store')

@php
    $metaTitle = $product->meta_title ?: $product->name;
    $metaDescription = $product->meta_description
        ?: \Illuminate\Support\Str::limit(strip_tags($product->short_description ?: $product->description ?: ''), 160);
@endphp

@section('title', $metaTitle . ' — Le Cameleon')
@section('meta_description', $metaDescription)

@push('meta')
    <meta property="og:title" content="{{ $metaTitle }}">
    <meta property="og:description" content="{{ $metaDescription }}">
@endpush

@section('content')
@php
    $images = collect($product->images ?? [])
        ->map(fn ($img) => is_string($img) ? $img : ($img->path ?? null))
        ->filter()
        ->map(fn ($path) => str_starts_with($path, 'http') || str_starts_with($path, '/')
            ? $path
            : asset('storage/'.$path))
        ->values()
        ->all();

    $stock = max(0, (int) $product->quantity_available - (int) $product->quantity_reserved);
    $condition = $product->condition_grade?->value ?? $product->condition_grade;
    $brandName = $product->brand?->name;
    $era = $product->era_decade;
@endphp

<div class="container product-detail">
    <nav class="breadcrumb" aria-label="Breadcrumb">
        @if (Route::has('home'))
            <a href="{{ route('home') }}">Inicio</a>
            <span class="breadcrumb__sep">/</span>
        @endif
        @if (Route::has('shop.index'))
            <a href="{{ route('shop.index') }}">Tienda</a>
            <span class="breadcrumb__sep">/</span>
        @endif
        <span>{{ $product->name }}</span>
    </nav>

    <div x-data="productGallery({{ json_encode($images) }})">
        <div class="product-gallery">
            <div class="product-gallery__main">
                <template x-if="images.length">
                    <img :src="images[active]" alt="{{ $product->name }}">
                </template>
                <template x-if="!images.length">
                    <div style="width:100%;height:100%;background:var(--secondary);"></div>
                </template>
            </div>
            <div class="product-gallery__thumbs" x-show="images.length > 1">
                <template x-for="(img, i) in images" :key="i">
                    <button type="button" class="product-gallery__thumb" :class="{ 'product-gallery__thumb--active': active === i }" @click="select(i)">
                        <img :src="img" alt="">
                    </button>
                </template>
            </div>
        </div>
    </div>

    <div class="product-info">
        @if ($era)
            <span class="product-info__era">{{ $era }}</span>
        @endif
        <h1 class="product-info__title">{{ $product->name }}</h1>
        <p class="product-info__price">${{ number_format((float) $product->price, 2) }}</p>

        <div class="product-info__meta">
            @if ($condition)
                <span class="badge badge--accent">{{ $condition }}</span>
            @endif
            @if ($product->is_authenticated)
                <span class="badge badge--success">Pieza verificada</span>
            @endif
            @if ($product->is_unique_piece || $stock <= 1)
                <span class="badge badge--warning">Pieza única</span>
            @endif
        </div>

        @if ($product->is_authenticated && $product->authenticity_notes)
            <p class="product-info__authenticity text-muted" style="font-size:0.9rem;margin-bottom:var(--space-md);">
                {{ \Illuminate\Support\Str::limit($product->authenticity_notes, 200) }}
            </p>
        @endif

        <p class="product-info__description">{{ $product->description ?? $product->short_description }}</p>

        <dl class="product-info__specs">
            @if ($brandName)
                <div class="product-info__spec"><dt>Marca</dt><dd>{{ $brandName }}</dd></div>
            @endif
            @if ($product->size_label)
                <div class="product-info__spec"><dt>Talla</dt><dd>{{ $product->size_label }}</dd></div>
            @endif
            @if ($era)
                <div class="product-info__spec"><dt>Época</dt><dd>{{ $era }}</dd></div>
            @endif
            @if ($condition)
                <div class="product-info__spec"><dt>Condición</dt><dd>{{ $condition }}</dd></div>
            @endif
        </dl>

        @php
            $measurementLabels = [
                'chest_cm' => 'Pecho',
                'waist_cm' => 'Cintura',
                'hips_cm' => 'Cadera',
                'length_cm' => 'Largo',
                'shoulder_cm' => 'Hombros',
                'sleeve_cm' => 'Manga',
            ];
            $measurements = collect($product->measurements ?? [])->filter(fn ($value) => $value !== null && $value !== '');
        @endphp

        @if ($measurements->isNotEmpty())
            <section style="margin:var(--space-lg) 0;">
                <h2 class="text-small" style="font-weight:700;margin-bottom:var(--space-sm);">Medidas (cm)</h2>
                <div class="table-wrap">
                    <table class="table">
                        <tbody>
                            @foreach ($measurementLabels as $key => $label)
                                @if ($measurements->has($key))
                                    <tr>
                                        <th scope="row">{{ $label }}</th>
                                        <td>{{ number_format((float) $measurements[$key], 1) }}</td>
                                    </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </section>
        @endif

        <div class="product-info__actions" x-data="quantityInput(1, {{ max($stock, 1) }})">
            <div class="product-info__qty">
                <button type="button" @click="decrement()" aria-label="Disminuir">−</button>
                <input type="number" name="quantity" x-model="qty" min="1" :max="max" readonly>
                <button type="button" @click="increment()" aria-label="Aumentar">+</button>
            </div>

            @if (Route::has('cart.store') && $stock > 0)
                <form action="{{ route('cart.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                    <input type="hidden" name="quantity" :value="qty">
                    <button type="submit" class="btn btn--primary">Agregar al carrito</button>
                </form>
            @else
                <button type="button" class="btn btn--primary" disabled>Agotado</button>
            @endif

            @if ($stock <= 0 && Route::has('shop.stock-alert'))
                <form action="{{ route('shop.stock-alert', $product) }}" method="POST" class="checkout-section" style="margin-top:var(--space-md);padding:var(--space-md);">
                    @csrf
                    <h3 class="checkout-section__title" style="font-size:1rem;">Avisarme cuando haya stock</h3>
                    @guest
                        <div class="form-group">
                            <label class="form-label" for="stock_alert_email">Correo electrónico</label>
                            <input type="email" id="stock_alert_email" name="email" class="form-input" value="{{ old('email') }}" required>
                        </div>
                    @endguest
                    <button type="submit" class="btn btn--ghost">Avisarme</button>
                </form>
            @endif

            @if (Route::has('wishlist.store'))
                <form action="{{ route('wishlist.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                    <button type="submit" class="btn btn--ghost">Favoritos</button>
                </form>
            @endif
        </div>

        @php
            $isPublished = ($product->status?->value ?? $product->status) === 'published'
                && $product->published_at
                && $product->published_at <= now();
            $canMakeOffer = $isPublished && $stock > 0;
        @endphp

        @if ($canMakeOffer && Route::has('shop.offers.store'))
            <section class="checkout-section" style="margin-top:var(--space-lg);padding:var(--space-md);">
                <h2 class="checkout-section__title" style="font-size:1.1rem;">Hacer oferta</h2>
                <p class="text-muted text-small" style="margin-bottom:var(--space-md);">
                    Propón un precio por debajo de ${{ number_format((float) $product->price, 2) }}.
                    @if ($product->is_unique_piece)
                        Pieza única — negociación disponible.
                    @endif
                </p>

                @auth
                    <form method="POST" action="{{ route('shop.offers.store', $product) }}">
                        @csrf
                        <div class="form-group">
                            <label class="form-label" for="offer_amount">Tu oferta (USD)</label>
                            <input
                                type="number"
                                id="offer_amount"
                                name="amount"
                                class="form-input"
                                step="0.01"
                                min="1"
                                max="{{ max(1, (float) $product->price - 0.01) }}"
                                value="{{ old('amount') }}"
                                required
                            >
                            @error('amount')
                                <p class="form-error">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="offer_message">Mensaje (opcional)</label>
                            <textarea id="offer_message" name="message" class="form-textarea" rows="3" maxlength="1000">{{ old('message') }}</textarea>
                        </div>
                        <button type="submit" class="btn btn--ghost">Enviar oferta</button>
                    </form>
                @else
                    <p class="text-muted">
                        <a href="{{ route('login') }}">Inicia sesión</a> para hacer una oferta.
                    </p>
                @endauth
            </section>
        @endif
    </div>
</div>

@if (($related ?? collect())->isNotEmpty())
    <section class="container section product-recs">
        <div class="section__header">
            <h2 class="section__title">También te puede gustar</h2>
        </div>
        <div class="grid-products">
            @foreach ($related as $item)
                @include('components.product-card', ['product' => $item])
            @endforeach
        </div>
    </section>
@endif

@if (($alsoViewed ?? collect())->isNotEmpty())
    <section class="container section product-recs">
        <div class="section__header">
            <h2 class="section__title">Clientes también vieron</h2>
        </div>
        <div class="grid-products">
            @foreach ($alsoViewed as $item)
                @include('components.product-card', ['product' => $item])
            @endforeach
        </div>
    </section>
@endif

<section class="container section product-reviews">
    <div class="section__header">
        <h2 class="section__title">Reseñas</h2>
    </div>

    @if ($product->reviews->isNotEmpty())
        <div class="product-reviews__list">
            @foreach ($product->reviews as $review)
                <article class="product-review">
                    <div class="product-review__header">
                        <strong>{{ $review->user?->name ?? 'Cliente' }}</strong>
                        <span class="product-review__rating" aria-label="{{ $review->rating }} de 5">{{ str_repeat('★', $review->rating) }}{{ str_repeat('☆', 5 - $review->rating) }}</span>
                    </div>
                    @if ($review->title)
                        <h3 class="product-review__title">{{ $review->title }}</h3>
                    @endif
                    <p class="product-review__body">{{ $review->body }}</p>
                </article>
            @endforeach
        </div>
    @else
        <p class="text-muted">Aún no hay reseñas aprobadas para este producto.</p>
    @endif

    @auth
        @if (Route::has('shop.reviews.store'))
            <form method="POST" action="{{ route('shop.reviews.store', $product) }}" class="product-review-form checkout-section" style="margin-top:var(--space-xl);">
                @csrf
                <h3 class="checkout-section__title">Escribe una reseña</h3>
                <div class="form-group">
                    <label class="form-label" for="rating">Calificación</label>
                    <select id="rating" name="rating" class="form-select" required>
                        <option value="">Selecciona</option>
                        @for ($i = 5; $i >= 1; $i--)
                            <option value="{{ $i }}" @selected(old('rating') == $i)>{{ $i }} estrella{{ $i > 1 ? 's' : '' }}</option>
                        @endfor
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label" for="title">Título</label>
                    <input type="text" id="title" name="title" class="form-input" value="{{ old('title') }}" maxlength="150" required>
                </div>
                <div class="form-group">
                    <label class="form-label" for="body">Comentario</label>
                    <textarea id="body" name="body" class="form-textarea" rows="4" required>{{ old('body') }}</textarea>
                </div>
                <button type="submit" class="btn btn--primary">Enviar reseña</button>
            </form>
        @endif
    @else
        <p class="text-muted" style="margin-top:var(--space-md);">
            <a href="{{ route('login') }}">Inicia sesión</a> para dejar una reseña.
        </p>
    @endauth
</section>
@endsection
