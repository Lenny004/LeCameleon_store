@extends('layouts.store')

@section('title', 'Tienda — Le Cameleon')

@section('content')
<div class="container shop-layout" x-data="filterPanel()">
    <aside>
        <button type="button" class="filter-panel__toggle" @click="toggle()">
            <span x-text="open ? 'Ocultar filtros' : 'Mostrar filtros'"></span>
        </button>
        <form method="GET" action="{{ Route::has('shop.index') ? route('shop.index') : '#' }}" class="filter-panel filter-panel--mobile-hidden" :class="{ 'filter-panel--open': open }">
            <div class="filter-panel__header">
                <h2 class="filter-panel__title">Filtros</h2>
                <a href="{{ Route::has('shop.index') ? route('shop.index') : '#' }}" class="text-small text-muted">Limpiar</a>
            </div>

            <div class="filter-panel__group">
                <p class="filter-panel__label">Categoría</p>
                <div class="filter-panel__options">
                    @foreach ($categories ?? ['Ropa', 'Accesorios', 'Calzado', 'Hogar'] as $cat)
                        <label class="filter-panel__option">
                            <input type="checkbox" name="category[]" value="{{ is_object($cat) ? $cat->slug : strtolower($cat) }}" {{ in_array(is_object($cat) ? $cat->slug : strtolower($cat), (array) request('category', [])) ? 'checked' : '' }}>
                            {{ is_object($cat) ? $cat->name : $cat }}
                        </label>
                    @endforeach
                </div>
            </div>

            <div class="filter-panel__group">
                <p class="filter-panel__label">Época</p>
                <div class="filter-panel__options">
                    @foreach (['1960s', '1970s', '1980s', '1990s', '2000s'] as $era)
                        <label class="filter-panel__option">
                            <input type="checkbox" name="era[]" value="{{ $era }}" {{ in_array($era, (array) request('era', [])) ? 'checked' : '' }}>
                            {{ $era }}
                        </label>
                    @endforeach
                </div>
            </div>

            <div class="filter-panel__group">
                <p class="filter-panel__label">Condición</p>
                <div class="filter-panel__options">
                    @foreach (['Excelente', 'Muy bueno', 'Bueno', 'Aceptable'] as $cond)
                        <label class="filter-panel__option">
                            <input type="checkbox" name="condition[]" value="{{ strtolower(str_replace(' ', '-', $cond)) }}" {{ in_array(strtolower(str_replace(' ', '-', $cond)), (array) request('condition', [])) ? 'checked' : '' }}>
                            {{ $cond }}
                        </label>
                    @endforeach
                </div>
            </div>

            <div class="filter-panel__group">
                <p class="filter-panel__label">Valoración mínima</p>
                <div class="filter-panel__options">
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
            </div>

            <button type="submit" class="btn btn--primary btn--block">Aplicar filtros</button>
        </form>
    </aside>

    <div>
        <nav class="breadcrumb" aria-label="Breadcrumb">
            @if (Route::has('home'))
                <a href="{{ route('home') }}">Inicio</a>
                <span class="breadcrumb__sep">/</span>
            @endif
            <span>Tienda</span>
        </nav>

        <div class="shop-toolbar">
            <p class="shop-toolbar__count">{{ $products->total() ?? count($products ?? []) }} productos</p>
            @auth
                @if (($hasActiveFilters ?? false) && Route::has('account.saved-searches.store'))
                    <form method="POST" action="{{ route('account.saved-searches.store') }}" class="shop-toolbar__save" style="display:flex;gap:var(--space-sm);align-items:center;">
                        @csrf
                        <input type="hidden" name="query_params" value="{{ json_encode($filters ?? []) }}">
                        <input type="text" name="name" class="form-input" placeholder="Nombre de la búsqueda" maxlength="120" required style="max-width:14rem;">
                        <button type="submit" class="btn btn--ghost btn--sm">Guardar búsqueda</button>
                    </form>
                @endif
            @endauth
            <div class="shop-toolbar__sort">
                <label for="sort" class="text-small">Ordenar:</label>
                <select id="sort" name="sort" onchange="if(this.value) window.location.href=this.value">
                    @php
                        $base = Route::has('shop.index') ? route('shop.index') : '#';
                        $sortQuery = collect($filters ?? request()->only(['q', 'category', 'brand', 'era_decade', 'condition_grade', 'size_label', 'color', 'price_min', 'price_max', 'in_stock', 'min_rating']))
                            ->filter(fn ($v) => $v !== null && $v !== '')
                            ->all();
                        $sortUrl = fn (string $sort) => $base.'?'.http_build_query(array_merge($sortQuery, ['sort' => $sort]));
                        $currentSort = request('sort', $filters['sort'] ?? 'newest');
                    @endphp
                    <option value="{{ $sortUrl('newest') }}" {{ in_array($currentSort, ['newest', 'new', ''], true) ? 'selected' : '' }}>Más recientes</option>
                    <option value="{{ $sortUrl('price_asc') }}" {{ $currentSort === 'price_asc' ? 'selected' : '' }}>Precio: menor</option>
                    <option value="{{ $sortUrl('price_desc') }}" {{ $currentSort === 'price_desc' ? 'selected' : '' }}>Precio: mayor</option>
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
            <div class="grid-products">
                @foreach ($items as $product)
                    @include('components.product-card', ['product' => $product])
                @endforeach
            </div>
            @if (isset($products) && method_exists($products, 'links'))
                <div class="pagination">{{ $products->links() }}</div>
            @endif
        @else
            @include('components.empty-state', [
                'title' => 'Sin resultados',
                'text' => 'Prueba ajustando los filtros o explora otras categorías.',
                'actionLabel' => 'Ver todo',
                'actionUrl' => Route::has('shop.index') ? route('shop.index') : '#',
            ])
        @endif
    </div>
</div>
@endsection
