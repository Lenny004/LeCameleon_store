@extends('layouts.store')

@section('title', 'Tienda — Le Cameleon')

@section('content')
<div class="container shop-layout" x-data="filterPanel()">
    <aside class="shop-layout__sidebar">
        @php
            $activeFilterCount = count((array) request('category', []))
                + count((array) request('era', []))
                + count((array) request('condition', []))
                + (request()->filled('min_rating') ? 1 : 0);
        @endphp
        <button type="button" class="filter-panel__toggle" @click="toggle()" :aria-expanded="open">
            <svg class="filter-panel__toggle-icon" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                <path d="M4 6h16M4 12h16M4 18h10"/>
            </svg>
            <span x-text="open ? 'Ocultar filtros' : 'Mostrar filtros'"></span>
            @if ($activeFilterCount > 0)
                <span class="filter-panel__toggle-badge" aria-label="{{ $activeFilterCount }} filtros activos">{{ $activeFilterCount }}</span>
            @endif
        </button>
        <form method="GET" action="{{ Route::has('shop.index') ? route('shop.index') : '#' }}" class="filter-panel filter-panel--mobile-hidden" :class="{ 'filter-panel--open': open }">
            <div class="filter-panel__header">
                <h2 class="filter-panel__title">Filtros</h2>
                <a href="{{ Route::has('shop.index') ? route('shop.index') : '#' }}" class="filter-panel__clear">Limpiar</a>
            </div>

            <details class="filter-panel__group" open>
                <summary class="filter-panel__label">Categoría</summary>
                <div class="filter-panel__options">
                    @foreach ($categories ?? ['Ropa', 'Accesorios', 'Calzado', 'Hogar'] as $cat)
                        <label class="filter-panel__option">
                            <input type="checkbox" name="category[]" value="{{ is_object($cat) ? $cat->slug : strtolower($cat) }}" {{ in_array(is_object($cat) ? $cat->slug : strtolower($cat), (array) request('category', [])) ? 'checked' : '' }}>
                            {{ is_object($cat) ? $cat->name : $cat }}
                        </label>
                    @endforeach
                </div>
            </details>

            <details class="filter-panel__group" open>
                <summary class="filter-panel__label">Época</summary>
                <div class="filter-panel__options filter-panel__options--grid">
                    @foreach (['1960s', '1970s', '1980s', '1990s', '2000s'] as $era)
                        <label class="filter-panel__option">
                            <input type="checkbox" name="era[]" value="{{ $era }}" {{ in_array($era, (array) request('era', [])) ? 'checked' : '' }}>
                            {{ $era }}
                        </label>
                    @endforeach
                </div>
            </details>

            <details class="filter-panel__group" open>
                <summary class="filter-panel__label">Condición</summary>
                <div class="filter-panel__options filter-panel__options--grid">
                    @foreach (['Excelente', 'Muy bueno', 'Bueno', 'Aceptable'] as $cond)
                        <label class="filter-panel__option">
                            <input type="checkbox" name="condition[]" value="{{ strtolower(str_replace(' ', '-', $cond)) }}" {{ in_array(strtolower(str_replace(' ', '-', $cond)), (array) request('condition', [])) ? 'checked' : '' }}>
                            {{ $cond }}
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
                                type="radio"
                                name="min_rating"
                                value="{{ $stars }}"
                                {{ (string) request('min_rating', $filters['min_rating'] ?? '') === (string) $stars ? 'checked' : '' }}
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
                <a href="{{ route('home') }}">Inicio</a>
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
                @foreach ((array) request('category', []) as $catSlug)
                    <span class="shop-active-filters__chip">{{ $catSlug }}</span>
                @endforeach
                @foreach ((array) request('era', []) as $eraVal)
                    <span class="shop-active-filters__chip">{{ $eraVal }}</span>
                @endforeach
                @foreach ((array) request('condition', []) as $condVal)
                    <span class="shop-active-filters__chip">{{ str_replace('-', ' ', $condVal) }}</span>
                @endforeach
                @if (request()->filled('min_rating'))
                    <span class="shop-active-filters__chip">{{ request('min_rating') }} ★+</span>
                @endif
                <a href="{{ Route::has('shop.index') ? route('shop.index') : '#' }}" class="shop-active-filters__clear">Limpiar todo</a>
            </div>
        @endif

        <div class="shop-toolbar">
            <div class="shop-toolbar__meta">
                @php
                    $total = $products->total() ?? count($products ?? []);
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
                        <input type="hidden" name="query_params" value="{{ json_encode($filters ?? []) }}">
                        <input type="text" name="name" class="form-input shop-toolbar__save-input" placeholder="Nombre de la búsqueda" maxlength="120" required>
                        <button type="submit" class="btn btn--ghost btn--sm">Guardar búsqueda</button>
                    </form>
                @endif
            @endauth

            <div class="shop-toolbar__sort">
                <label for="sort" class="shop-toolbar__sort-label">Ordenar por</label>
                <select id="sort" name="sort" class="shop-toolbar__sort-select" onchange="if(this.value) window.location.href=this.value">
                    @php
                        $base = Route::has('shop.index') ? route('shop.index') : '#';
                        $sortQuery = collect($filters ?? request()->only(['q', 'category', 'brand', 'era_decade', 'condition_grade', 'size_label', 'color', 'price_min', 'price_max', 'in_stock', 'min_rating']))
                            ->filter(fn ($v) => $v !== null && $v !== '')
                            ->all();
                        $sortUrl = fn (string $sort) => $base.'?'.http_build_query(array_merge($sortQuery, ['sort' => $sort]));
                        $currentSort = request('sort', $filters['sort'] ?? 'newest');
                    @endphp
                    <option value="{{ $sortUrl('newest') }}" {{ in_array($currentSort, ['newest', 'new', ''], true) ? 'selected' : '' }}>Más recientes</option>
                    <option value="{{ $sortUrl('price_asc') }}" {{ $currentSort === 'price_asc' ? 'selected' : '' }}>Precio: menor a mayor</option>
                    <option value="{{ $sortUrl('price_desc') }}" {{ $currentSort === 'price_desc' ? 'selected' : '' }}>Precio: mayor a menor</option>
                    <option value="{{ $sortUrl('rating') }}" {{ $currentSort === 'rating' ? 'selected' : '' }}>Mejor valorados</option>
                </select>
            </div>
        </div>

        @php
            $items = $products ?? collect([
                (object) ['id' => 1, 'slug' => 'chaqueta-denim-80s', 'name' => 'Chaqueta Denim 80s', 'price' => 89.00, 'era' => '1980s', 'condition' => 'Excelente'],
                (object) ['id' => 2, 'slug' => 'vestido-floral-70s', 'name' => 'Vestido Floral 70s', 'price' => 120.00, 'era' => '1970s', 'condition' => 'Muy bueno'],
                (object) ['id' => 3, 'slug' => 'bolso-cuero-vintage', 'name' => 'Bolso Cuero Vintage', 'price' => 65.00, 'era' => '1990s', 'condition' => 'Bueno'],
                (object) ['id' => 4, 'slug' => 'camisa-rayas-60s', 'name' => 'Camisa Rayas 60s', 'price' => 45.00, 'era' => '1960s', 'condition' => 'Excelente'],
                (object) ['id' => 5, 'slug' => 'falda-plisada-90s', 'name' => 'Falda Plisada 90s', 'price' => 38.00, 'era' => '1990s', 'condition' => 'Muy bueno'],
                (object) ['id' => 6, 'slug' => 'abrigo-lana-70s', 'name' => 'Abrigo Lana 70s', 'price' => 155.00, 'era' => '1970s', 'condition' => 'Excelente'],
            ]);
            if (is_object($items) && method_exists($items, 'items')) {
                $items = collect($items->items());
            } elseif (!($items instanceof \Illuminate\Support\Collection)) {
                $items = collect($items);
            }
        @endphp

        @if ($items->count())
            <div class="shop-results">
                <div class="grid-products shop-results__grid">
                    @foreach ($items as $product)
                        @include('components.product-card', ['product' => $product])
                    @endforeach
                </div>
                @if (isset($products) && method_exists($products, 'links'))
                    <div class="shop-results__pagination pagination">{{ $products->links() }}</div>
                @endif
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
