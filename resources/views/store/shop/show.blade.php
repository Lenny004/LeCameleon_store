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
    use App\Models\ProductImage;

    $images = collect($product->images ?? [])
        ->map(fn ($img) => $img instanceof ProductImage ? $img->url() : ProductImage::urlFor(is_string($img) ? $img : ($img->path ?? null)))
        ->filter()
        ->values()
        ->all();

    $stock = max(0, (int) $product->quantity_available - (int) $product->quantity_reserved);
    $condition = $product->condition_grade?->value ?? $product->condition_grade;
    $brandName = $product->brand?->name;
    $brandSlug = $product->brand?->slug;
    $categoryName = $product->category?->name;
    $categorySlug = $product->category?->slug;
    $era = $product->era_decade;
    $avgRating = $reviewSummary['average'] ?? ($product->approved_reviews_avg ? round((float) $product->approved_reviews_avg, 1) : null);
    $reviewsCount = $reviewSummary['count'] ?? (int) ($product->approved_reviews_count ?? 0);
    $isLowStock = $stock > 0 && $stock <= (int) ($product->low_stock_threshold ?? 3);
@endphp

<div class="container product-page">
    <nav class="breadcrumb product-page__breadcrumb" aria-label="Breadcrumb">
        @if (Route::has('home'))
            <a href="{{ route('home') }}">Inicio</a>
            <span class="breadcrumb__sep" aria-hidden="true">/</span>
        @endif
        @if (Route::has('shop.index'))
            <a href="{{ route('shop.index') }}">Tienda</a>
            <span class="breadcrumb__sep" aria-hidden="true">/</span>
        @endif
        @if ($categoryName && $categorySlug && Route::has('shop.index'))
            <a href="{{ route('shop.index', ['category' => [$categorySlug]]) }}">{{ $categoryName }}</a>
            <span class="breadcrumb__sep" aria-hidden="true">/</span>
        @endif
        <span aria-current="page">{{ $product->name }}</span>
    </nav>

    <div class="product-detail">
        <div class="product-detail__media" x-data="productGallery({{ json_encode($images) }})">
            <div class="product-gallery">
                <div class="product-gallery__main">
                    <template x-if="images.length">
                        <img :src="images[active]" alt="{{ $product->name }}">
                    </template>
                    <template x-if="!images.length">
                        <div class="product-gallery__placeholder" aria-hidden="true"></div>
                    </template>
                </div>
                <div class="product-gallery__thumbs" x-show="images.length > 1" x-cloak>
                    <template x-for="(img, i) in images" :key="i">
                        <button
                            type="button"
                            class="product-gallery__thumb"
                            :class="{ 'product-gallery__thumb--active': active === i }"
                            @click="select(i)"
                            :aria-label="'Imagen ' + (i + 1)"
                            :aria-current="active === i ? 'true' : 'false'"
                        >
                            <img :src="img" alt="">
                        </button>
                    </template>
                </div>
            </div>
        </div>

        <div class="product-detail__content">
            <header class="product-header">
                @if ($era)
                    <p class="product-header__era">{{ $era }}</p>
                @endif
                <h1 class="product-header__title">{{ $product->name }}</h1>

                <div class="product-header__meta">
                    @if ($brandName && Route::has('shop.index'))
                        <span class="product-header__meta-item">
                            Marca:
                            <a href="{{ route('shop.index', ['brand' => [$brandSlug ?? strtolower($brandName)]]) }}">{{ $brandName }}</a>
                        </span>
                    @elseif ($brandName)
                        <span class="product-header__meta-item">Marca: {{ $brandName }}</span>
                    @endif
                    @if ($categoryName && $categorySlug && Route::has('shop.index'))
                        <span class="product-header__meta-item">
                            Categoría:
                            <a href="{{ route('shop.index', ['category' => [$categorySlug]]) }}">{{ $categoryName }}</a>
                        </span>
                    @elseif ($categoryName)
                        <span class="product-header__meta-item">Categoría: {{ $categoryName }}</span>
                    @endif
                    @if ($condition)
                        <span class="product-header__meta-item">Condición: {{ $condition }}</span>
                    @endif
                </div>

                @if ($avgRating)
                    <p class="product-header__rating">
                        <span class="product-rating" aria-label="{{ $avgRating }} de 5">
                            @for ($i = 1; $i <= 5; $i++)
                                <span class="product-rating__star {{ $i <= round($avgRating) ? 'product-rating__star--on' : '' }}" aria-hidden="true">★</span>
                            @endfor
                        </span>
                        <strong>{{ number_format($avgRating, 1) }}</strong>
                        <a href="#product-reviews" class="product-header__rating-link">{{ $reviewsCount }} {{ $reviewsCount === 1 ? 'reseña' : 'reseñas' }}</a>
                    </p>
                @endif

                <div class="product-header__tags">
                    @if ($product->is_authenticated)
                        <span class="badge badge--success">{{ __('store.verified_piece') }}</span>
                    @endif
                    @if ($product->is_unique_piece || $stock <= 1)
                        <span class="badge badge--warning">Pieza única</span>
                    @endif
                </div>
            </header>

            <aside class="product-buybox" aria-label="Comprar producto">
                <div class="product-buybox__price-block">
                    <div class="product-buybox__price-row">
                        <p class="product-buybox__price">${{ number_format((float) $product->price, 2) }}</p>
                        @if ($product->compare_at_price && (float) $product->compare_at_price > (float) $product->price)
                            <p class="product-buybox__compare">${{ number_format((float) $product->compare_at_price, 2) }}</p>
                            @php
                                $savingsPct = round((1 - (float) $product->price / (float) $product->compare_at_price) * 100);
                            @endphp
                            <span class="product-buybox__savings">−{{ $savingsPct }}%</span>
                        @endif
                    </div>
                    <p class="product-buybox__price-note">Precio final · impuestos incluidos</p>
                </div>

                <p class="product-buybox__stock product-buybox__stock--{{ $stock <= 0 ? 'out' : ($isLowStock ? 'low' : 'in') }}">
                    @if ($stock <= 0)
                        Agotado
                    @elseif ($isLowStock)
                        Solo quedan {{ $stock }} — ¡date prisa!
                    @else
                        En stock ({{ $stock }} disponibles)
                    @endif
                </p>

                <div class="product-buybox__divider" aria-hidden="true"></div>

                <div class="product-buybox__actions" x-data="quantityInput(1, {{ max($stock, 1) }})">
                    @if ($stock > 0)
                        <label class="product-buybox__qty-label" for="product-qty">Cantidad</label>
                        <div class="product-buybox__qty" id="product-qty">
                            <button type="button" @click="decrement()" aria-label="Disminuir cantidad">−</button>
                            <input type="number" name="quantity" x-model="qty" min="1" :max="max" readonly aria-label="Cantidad">
                            <button type="button" @click="increment()" aria-label="Aumentar cantidad">+</button>
                        </div>
                    @endif

                    @if (Route::has('cart.store') && $stock > 0)
                        <form action="{{ route('cart.store') }}" method="POST" class="product-buybox__form product-buybox__cta">
                            @csrf
                            <input type="hidden" name="product_id" value="{{ $product->id }}">
                            <input type="hidden" name="quantity" :value="qty">
                            <button type="submit" class="btn btn--primary btn--lg btn--block">{{ __('store.add_to_cart') }}</button>
                        </form>
                    @else
                        <button type="button" class="btn btn--primary btn--lg btn--block" disabled>Agotado</button>
                    @endif

                    @if (Route::has('wishlist.store'))
                        <form action="{{ route('wishlist.store') }}" method="POST" class="product-buybox__secondary">
                            @csrf
                            <input type="hidden" name="product_id" value="{{ $product->id }}">
                            <button type="submit" class="btn btn--ghost btn--block">Guardar en favoritos</button>
                        </form>
                    @endif
                </div>

                @if ($stock <= 0 && Route::has('shop.stock-alert'))
                    <div class="product-panel product-panel--compact">
                        <h2 class="product-panel__title">Avisarme cuando haya stock</h2>
                        <form action="{{ route('shop.stock-alert', $product) }}" method="POST">
                            @csrf
                            @guest
                                <div class="form-group">
                                    <label class="form-label" for="stock_alert_email">Correo electrónico</label>
                                    <input type="email" id="stock_alert_email" name="email" class="form-input" value="{{ old('email') }}" required>
                                </div>
                            @endguest
                            <button type="submit" class="btn btn--ghost btn--block">Avisarme</button>
                        </form>
                    </div>
                @endif
            </aside>

            <div class="product-detail__body">
                @if ($product->is_authenticated && $product->authenticity_notes)
                    <p class="product-detail__authenticity">{{ \Illuminate\Support\Str::limit($product->authenticity_notes, 200) }}</p>
                @endif

                @if ($product->description ?? $product->short_description)
                    <section class="product-panel">
                        <h2 class="product-panel__title">Descripción</h2>
                        <div class="product-detail__description">{{ $product->description ?? $product->short_description }}</div>
                    </section>
                @endif

                <section class="product-panel">
                    <h2 class="product-panel__title">Detalles</h2>
                    <dl class="product-specs">
                        @if ($brandName)
                            <div class="product-specs__row"><dt>Marca</dt><dd>{{ $brandName }}</dd></div>
                        @endif
                        @if ($categoryName)
                            <div class="product-specs__row"><dt>Categoría</dt><dd>{{ $categoryName }}</dd></div>
                        @endif
                        @if ($product->size_label)
                            <div class="product-specs__row"><dt>Talla</dt><dd>{{ $product->size_label }}</dd></div>
                        @endif
                        @if ($era)
                            <div class="product-specs__row"><dt>Época</dt><dd>{{ $era }}</dd></div>
                        @endif
                        @if ($condition)
                            <div class="product-specs__row"><dt>Condición</dt><dd>{{ $condition }}</dd></div>
                        @endif
                        @if ($product->color)
                            <div class="product-specs__row"><dt>Color</dt><dd>{{ $product->color }}</dd></div>
                        @endif
                        @if ($product->material)
                            <div class="product-specs__row"><dt>Material</dt><dd>{{ $product->material }}</dd></div>
                        @endif
                    </dl>
                </section>

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
                    <section class="product-panel">
                        <h2 class="product-panel__title">Medidas (cm)</h2>
                        <div class="table-wrap">
                            <table class="table product-measurements">
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

                @php
                    $isPublished = ($product->status?->value ?? $product->status) === 'published'
                        && $product->published_at
                        && $product->published_at <= now();
                    $canMakeOffer = $isPublished && $stock > 0;
                @endphp

                @if ($canMakeOffer && Route::has('shop.offers.store'))
                    <section class="product-panel">
                        <h2 class="product-panel__title">{{ __('store.make_offer') }}</h2>
                        <p class="product-panel__lead">
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
    </div>
</div>

@if (($related ?? collect())->isNotEmpty())
    <section class="container section product-recs product-recs--related" aria-labelledby="product-recs-related">
        <div class="section__header product-recs__header">
            <h2 class="section__title" id="product-recs-related">También te puede gustar</h2>
            <p class="product-recs__lead">Piezas seleccionadas de la misma época y estilo.</p>
        </div>
        <div class="product-recs__row">
            @foreach ($related as $item)
                @include('components.product-card', ['product' => $item])
            @endforeach
        </div>
    </section>
@endif

@if (($alsoViewed ?? collect())->isNotEmpty())
    <section class="container section product-recs product-recs--viewed" aria-labelledby="product-recs-viewed">
        <div class="section__header product-recs__header">
            <h2 class="section__title" id="product-recs-viewed">Clientes también vieron</h2>
            <p class="product-recs__lead">Exploraciones recientes de otros compradores.</p>
        </div>
        <div class="product-recs__row">
            @foreach ($alsoViewed as $item)
                @include('components.product-card', ['product' => $item])
            @endforeach
        </div>
    </section>
@endif

<section class="container section product-reviews" id="product-reviews">
    <div class="section__header product-reviews__intro">
        <h2 class="section__title">Valoraciones y comentarios</h2>
        @if (($reviewSummary['count'] ?? 0) > 0)
            <p class="product-reviews__intro-text">Opiniones verificadas de compradores de Le Cameleon.</p>
        @endif
    </div>

    @php
        $summary = $reviewSummary ?? ['count' => 0, 'average' => null, 'distribution' => [5 => 0, 4 => 0, 3 => 0, 2 => 0, 1 => 0]];
        $reviewList = $reviews ?? collect();
        $activeRating = $reviewFilters['rating'] ?? null;
        $activeSort = $reviewFilters['sort'] ?? 'newest';
        $reviewBaseUrl = route('shop.show', $product->slug);
    @endphp

    @if ($summary['count'] > 0)
        <div class="product-reviews__layout">
            <div class="product-reviews__summary">
                <div class="product-reviews__score">
                    <p class="product-reviews__average">{{ number_format((float) $summary['average'], 1) }}</p>
                    <p class="product-rating product-rating--lg" aria-label="{{ $summary['average'] }} de 5">
                        @for ($i = 1; $i <= 5; $i++)
                            <span class="product-rating__star {{ $i <= round((float) $summary['average']) ? 'product-rating__star--on' : '' }}" aria-hidden="true">★</span>
                        @endfor
                    </p>
                    <p class="product-reviews__count">Basado en {{ $summary['count'] }} {{ $summary['count'] === 1 ? 'reseña' : 'reseñas' }}</p>
                </div>
                <ul class="product-reviews__bars" aria-label="Distribución de valoraciones">
                    @foreach ($summary['distribution'] as $stars => $total)
                        @php $pct = $summary['count'] > 0 ? round(($total / $summary['count']) * 100) : 0; @endphp
                        <li class="product-reviews__bar-row">
                            <a
                                href="{{ $reviewBaseUrl }}?review_rating={{ $stars }}&review_sort={{ urlencode($activeSort) }}#product-reviews"
                                class="product-reviews__bar-label {{ (string) $activeRating === (string) $stars ? 'is-active' : '' }}"
                            >
                                {{ $stars }} ★
                            </a>
                            <div class="product-reviews__bar-track" aria-hidden="true">
                                <span class="product-reviews__bar-fill" style="width: {{ $pct }}%;"></span>
                            </div>
                            <span class="product-reviews__bar-count">{{ $total }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>

            <div class="product-reviews__content">
                <form method="GET" action="{{ $reviewBaseUrl }}" class="product-reviews__filters">
                    <input type="hidden" name="review_rating" value="{{ $activeRating }}">
                    <div class="product-reviews__chips" role="group" aria-label="Filtrar por estrellas">
                        <a
                            href="{{ $reviewBaseUrl }}?review_sort={{ urlencode($activeSort) }}#product-reviews"
                            class="product-reviews__chip {{ $activeRating === null || $activeRating === '' ? 'is-active' : '' }}"
                        >
                            Todas
                        </a>
                        @for ($stars = 5; $stars >= 1; $stars--)
                            <a
                                href="{{ $reviewBaseUrl }}?review_rating={{ $stars }}&review_sort={{ urlencode($activeSort) }}#product-reviews"
                                class="product-reviews__chip {{ (string) $activeRating === (string) $stars ? 'is-active' : '' }}"
                            >
                                {{ $stars }} ★
                            </a>
                        @endfor
                    </div>
                    <div class="product-reviews__sort">
                        <label for="review_sort" class="product-reviews__sort-label">Ordenar</label>
                        <select id="review_sort" name="review_sort" class="form-select" onchange="this.form.submit()">
                            <option value="newest" @selected($activeSort === 'newest')>Más recientes</option>
                            <option value="oldest" @selected($activeSort === 'oldest')>Más antiguas</option>
                            <option value="highest" @selected($activeSort === 'highest')>Mejor valoración</option>
                            <option value="lowest" @selected($activeSort === 'lowest')>Menor valoración</option>
                        </select>
                    </div>
                </form>

                @if ($reviewList->isNotEmpty())
                    <div class="product-reviews__list">
                        @foreach ($reviewList as $review)
                            <article class="product-review">
                                <div class="product-review__header">
                                    <div>
                                        <strong class="product-review__author">{{ $review->user?->name ?? 'Cliente' }}</strong>
                                        @if ($review->created_at)
                                            <time class="product-review__date" datetime="{{ $review->created_at->toDateString() }}">
                                                {{ $review->created_at->translatedFormat('d M Y') }}
                                            </time>
                                        @endif
                                    </div>
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
                    <p class="text-muted">No hay comentarios con ese filtro.</p>
                    <p>
                        <a href="{{ $reviewBaseUrl }}#product-reviews" class="btn btn--ghost btn--sm">Ver todas las reseñas</a>
                    </p>
                @endif
            </div>
        </div>
    @else
        <p class="text-muted product-reviews__empty">Aún no hay reseñas aprobadas para este producto.</p>
    @endif

    @auth
        @if (Route::has('shop.reviews.store'))
            <form method="POST" action="{{ route('shop.reviews.store', $product) }}" class="product-review-form product-panel">
                @csrf
                <h3 class="product-panel__title">Escribe una reseña</h3>
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
        <p class="text-muted product-reviews__login">
            <a href="{{ route('login') }}">Inicia sesión</a> para dejar una reseña.
        </p>
    @endauth
</section>
@endsection
