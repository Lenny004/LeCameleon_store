@extends('layouts.store')

@section('title', 'Tienda — Le Cameleon')

@if (filled(request('category')))
    @php
        $categorySlugs = (array) request('category');
        $categoryForSchema = collect($filterOptions['categories'] ?? [])->firstWhere('slug', $categorySlugs[0] ?? null);
    @endphp
    @push('meta')
        @php
            $catalogBreadcrumbSchema = [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => [
                ['@type' => 'ListItem', 'position' => 1, 'name' => 'Inicio', 'item' => route('home')],
                ['@type' => 'ListItem', 'position' => 2, 'name' => 'Tienda', 'item' => route('shop.index')],
                ['@type' => 'ListItem', 'position' => 3, 'name' => $categoryForSchema?->name ?? $categorySlugs[0], 'item' => route('shop.index', ['category' => $categorySlugs])],
            ],
            ];
        @endphp
        <script type="application/ld+json" nonce="{{ Vite::cspNonce() }}">{!! json_encode($catalogBreadcrumbSchema, JSON_UNESCAPED_SLASHES|JSON_HEX_TAG) !!}</script>
    @endpush
@endif

@section('content')
@php
    $categories = $filterOptions['categories'] ?? collect();
    $eraDecades = $filterOptions['era_decades'] ?? collect();
    $conditionGrades = $filterOptions['condition_grades'] ?? [];
    $conditionLabels = [
        'mint' => 'Como nuevo',
        'excellent' => 'Excelente',
        'good' => 'Bueno',
        'fair' => 'Aceptable',
        'poor' => 'Usado',
    ];

    $activeCategories = array_values(array_filter((array) ($filters['category'] ?? request('category', []))));
    $activeEras = array_values(array_filter((array) ($filters['era_decade'] ?? request('era_decade', []))));
    $activeConditions = array_values(array_filter((array) ($filters['condition_grade'] ?? request('condition_grade', []))));
    $activeMinRating = $filters['min_rating'] ?? request('min_rating');
    $activePriceMin = $filters['price_min'] ?? request('price_min');
    $activePriceMax = $filters['price_max'] ?? request('price_max');

    $activeFilterCount = count($activeCategories)
        + count($activeEras)
        + count($activeConditions)
        + ($activeMinRating !== null && $activeMinRating !== '' ? 1 : 0)
        + ($activePriceMin !== null && $activePriceMin !== '' ? 1 : 0)
        + ($activePriceMax !== null && $activePriceMax !== '' ? 1 : 0)
        + (filled($filters['q'] ?? request('q')) ? 1 : 0);
@endphp
<div class="container shop-layout" x-data="filterPanel()">
    <aside class="shop-layout__sidebar">
        <button type="button" class="filter-panel__toggle" @click="toggle()" :aria-expanded="open">
            <svg class="filter-panel__toggle-icon" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                <path d="M4 6h16M4 12h16M4 18h10"/>
            </svg>
            <span x-text="open ? 'Ocultar filtros' : 'Mostrar filtros'"></span>
            @if ($activeFilterCount > 0)
                <span class="filter-panel__toggle-badge" aria-label="{{ $activeFilterCount }} filtros activos">{{ $activeFilterCount }}</span>
            @endif
        </button>
        <form method="GET" action="{{ Route::has('shop.index') ? route('shop.index') : '#' }}" class="filter-panel filter-panel--mobile-hidden" :class="{ 'filter-panel--open-mobile': open }">
            <div class="filter-panel__header">
                <h2 class="filter-panel__title">Filtros</h2>
                <a href="{{ Route::has('shop.index') ? route('shop.index') : '#' }}" class="filter-panel__clear">Limpiar</a>
            </div>

            @if (filled($filters['sort'] ?? request('sort')))
                <input type="hidden" name="sort" value="{{ $filters['sort'] ?? request('sort') }}">
            @endif

            <div class="form-group">
                <label class="form-label" for="shop-search">Buscar</label>
                <input type="search" id="shop-search" name="q" class="form-input" value="{{ old('q', $filters['q'] ?? request('q')) }}" placeholder="Ej. chaqueta de cuero" maxlength="100" inputmode="search" autocomplete="off">
            </div>

            <div class="form-row form-row--cols-2">
                <div class="form-group">
                    <label class="form-label" for="price_min">Precio mínimo</label>
                    <input type="number" id="price_min" name="price_min" class="form-input @error('price_min') form-input--error @enderror" value="{{ old('price_min', $activePriceMin) }}" placeholder="25.00" min="0" max="9999999999.99" step="0.01" inputmode="decimal" @error('price_min') aria-invalid="true" aria-describedby="price_min-error" @enderror>
                    @error('price_min')<span class="form-error" id="price_min-error">{{ $message }}</span>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label" for="price_max">Precio máximo</label>
                    <input type="number" id="price_max" name="price_max" class="form-input @error('price_max') form-input--error @enderror" value="{{ old('price_max', $activePriceMax) }}" placeholder="100.00" min="0" max="9999999999.99" step="0.01" inputmode="decimal" @error('price_max') aria-invalid="true" aria-describedby="price_max-error" @enderror>
                    @error('price_max')<span class="form-error" id="price_max-error">{{ $message }}</span>@enderror
                </div>
            </div>

            <details class="filter-panel__group" open>
                <summary class="filter-panel__label">Categoría</summary>
                <div class="filter-panel__options">
                    @forelse ($categories as $cat)
                        <label class="filter-panel__option">
                            <input class="filter-panel__option-input" type="checkbox" name="category[]" value="{{ $cat->slug }}" {{ in_array($cat->slug, $activeCategories, true) ? 'checked' : '' }}>
                            {{ $cat->name }}
                        </label>
                    @empty
                        <p class="text-muted">Sin categorías disponibles.</p>
                    @endforelse
                </div>
            </details>

            <details class="filter-panel__group" open>
                <summary class="filter-panel__label">Época</summary>
                <div class="filter-panel__options filter-panel__options--grid">
                    @forelse ($eraDecades as $era)
                        <label class="filter-panel__option">
                            <input class="filter-panel__option-input" type="checkbox" name="era_decade[]" value="{{ $era }}" {{ in_array($era, $activeEras, true) ? 'checked' : '' }}>
                            {{ $era }}
                        </label>
                    @empty
                        <p class="text-muted">Sin épocas disponibles.</p>
                    @endforelse
                </div>
            </details>

            <details class="filter-panel__group" open>
                <summary class="filter-panel__label">Condición</summary>
                <div class="filter-panel__options filter-panel__options--grid">
                    @foreach ($conditionGrades as $grade)
                        <label class="filter-panel__option">
                            <input class="filter-panel__option-input" type="checkbox" name="condition_grade[]" value="{{ $grade->value }}" {{ in_array($grade->value, $activeConditions, true) ? 'checked' : '' }}>
                            {{ $conditionLabels[$grade->value] ?? ucfirst($grade->value) }}
                        </label>
                    @endforeach
                </div>
            </details>

            <details class="filter-panel__group" open>
                <summary class="filter-panel__label">Valoración mínima</summary>
                <div class="filter-panel__options filter-panel__options--rating">
                    @foreach ([5, 4, 3, 2, 1] as $stars)
                        <label class="filter-panel__option">
                            <input
                                class="filter-panel__option-input"
                                type="radio"
                                name="min_rating"
                                value="{{ $stars }}"
                                {{ (string) $activeMinRating === (string) $stars ? 'checked' : '' }}
                            >
                            {{ $stars }} ★ o más
                        </label>
                    @endforeach
                </div>
            </details>

            <button type="submit" class="btn btn--primary btn--block filter-panel__submit">Aplicar filtros</button>
        </form>
    </aside>

    <main class="shop-layout__main">
        <nav class="breadcrumb" aria-label="Breadcrumb">
            @if (Route::has('home'))
                <a class="breadcrumb__link" href="{{ route('home') }}">Inicio</a>
                <span class="breadcrumb__sep">/</span>
            @endif
            <span aria-current="page">Tienda</span>
        </nav>

        <header class="shop-header">
            <h1 class="shop-header__title">Catálogo</h1>
            <p class="shop-header__lead">Piezas vintage seleccionadas, listas para encontrar su nueva historia.</p>
        </header>

        @if ($activeFilterCount > 0)
            <div class="shop-active-filters" aria-label="Filtros activos">
                <span class="shop-active-filters__label">Filtros:</span>
                @if (filled($filters['q'] ?? request('q')))
                    <span class="shop-active-filters__chip">«{{ $filters['q'] ?? request('q') }}»</span>
                @endif
                @foreach ($activeCategories as $catSlug)
                    @php $catLabel = $categories->firstWhere('slug', $catSlug)?->name ?? $catSlug; @endphp
                    <span class="shop-active-filters__chip">{{ $catLabel }}</span>
                @endforeach
                @foreach ($activeEras as $eraVal)
                    <span class="shop-active-filters__chip">{{ $eraVal }}</span>
                @endforeach
                @foreach ($activeConditions as $condVal)
                    <span class="shop-active-filters__chip">{{ $conditionLabels[$condVal] ?? $condVal }}</span>
                @endforeach
                @if ($activeMinRating !== null && $activeMinRating !== '')
                    <span class="shop-active-filters__chip">{{ $activeMinRating }} ★+</span>
                @endif
                @if ($activePriceMin !== null && $activePriceMin !== '')
                    <span class="shop-active-filters__chip">Desde ${{ number_format((float) $activePriceMin, 2) }}</span>
                @endif
                @if ($activePriceMax !== null && $activePriceMax !== '')
                    <span class="shop-active-filters__chip">Hasta ${{ number_format((float) $activePriceMax, 2) }}</span>
                @endif
                <a href="{{ Route::has('shop.index') ? route('shop.index') : '#' }}" class="shop-active-filters__clear">Limpiar todo</a>
            </div>
        @endif

        <div class="shop-toolbar">
            <div class="shop-toolbar__meta">
                @php
                    $total = $products->total() ?? 0;
                @endphp
                <p class="shop-toolbar__count">
                    <span class="shop-toolbar__count-value">{{ $total }}</span>
                    {{ $total === 1 ? 'resultado' : 'resultados' }}
                </p>
            </div>

            @auth
                @if (($hasActiveFilters ?? false) && Route::has('account.saved-searches.store'))
                    <form method="POST" action="{{ route('account.saved-searches.store') }}" class="shop-toolbar__save">
                        @csrf
                        <p class="form-required-note">Los campos con <span class="form-label__required" aria-hidden="true">*</span> son obligatorios.</p>
                        @foreach ($filters ?? [] as $key => $value)
                            @if (is_array($value))
                                @foreach ($value as $entry)
                                    <input type="hidden" name="query_params[{{ $key }}][]" value="{{ $entry }}">
                                @endforeach
                            @elseif ($value !== null && $value !== '')
                                <input type="hidden" name="query_params[{{ $key }}]" value="{{ $value }}">
                            @endif
                        @endforeach
                        @error('query_params')<span class="form-error" id="query-params-error">{{ $message }}</span>@enderror
                        <label class="sr-only" for="saved-search-name">Nombre de la búsqueda <span class="form-label__required" aria-hidden="true">*</span></label>
                        <input type="text" id="saved-search-name" name="name" class="form-input shop-toolbar__save-input @error('name') form-input--error @enderror" value="{{ old('name') }}" placeholder="Ej. Chaquetas de los años 80" maxlength="255" required @error('name') aria-invalid="true" aria-describedby="saved-search-name-error" @enderror>
                        @error('name')<span class="form-error" id="saved-search-name-error">{{ $message }}</span>@enderror
                        <button type="submit" class="btn btn--ghost btn--sm">Guardar búsqueda</button>
                    </form>
                @endif
            @endauth

            <div class="shop-toolbar__sort">
                <label for="sort" class="shop-toolbar__sort-label">Ordenar por</label>
                <select id="sort" name="sort" class="shop-toolbar__sort-select" x-on:change="if ($event.target.value) window.location.href = $event.target.value">
                    @php
                        $base = Route::has('shop.index') ? route('shop.index') : '#';
                        $sortQuery = collect($filters ?? request()->only(['q', 'category', 'brand', 'era_decade', 'condition_grade', 'size_label', 'color', 'price_min', 'price_max', 'in_stock', 'min_rating']))
                            ->filter(fn ($v) => $v !== null && $v !== '' && $v !== [])
                            ->all();
                        $sortUrl = fn (string $sort) => $base.'?'.http_build_query(array_merge($sortQuery, ['sort' => $sort]));
                        $currentSort = $filters['sort'] ?? request('sort', 'newest');
                    @endphp
                    <option value="{{ $sortUrl('newest') }}" {{ in_array($currentSort, ['newest', 'new', ''], true) ? 'selected' : '' }}>Más recientes</option>
                    <option value="{{ $sortUrl('price_asc') }}" {{ $currentSort === 'price_asc' ? 'selected' : '' }}>Precio: menor a mayor</option>
                    <option value="{{ $sortUrl('price_desc') }}" {{ $currentSort === 'price_desc' ? 'selected' : '' }}>Precio: mayor a menor</option>
                    <option value="{{ $sortUrl('rating') }}" {{ $currentSort === 'rating' ? 'selected' : '' }}>Mejor valorados</option>
                </select>
            </div>
        </div>

        @if ($products->count())
            <div class="shop-results">
                <div class="grid-products shop-results__grid">
                    @foreach ($products as $product)
                        @include('components.product-card', ['product' => $product])
                    @endforeach
                </div>
                <div class="shop-results__pagination pagination">{{ $products->links() }}</div>
            </div>
        @else
            <div class="shop-results shop-results--empty">
                @include('components.empty-state', [
                    'title' => 'Sin resultados',
                    'text' => 'Prueba ajustando los filtros o explora otras categorías.',
                    'actionLabel' => 'Ver todo',
                    'actionUrl' => Route::has('shop.index') ? route('shop.index') : '#',
                ])
            </div>
        @endif
    </main>
</div>
@endsection
