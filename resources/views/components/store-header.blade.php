<header class="header" x-data="mobileNav()">
    @php
        $storeLogo = config('store.brand_logo');
        $storeLogoIcon = config('store.brand_logo_icon');
        $hasStoreLogo = is_string($storeLogo) && $storeLogo !== '' && file_exists(public_path($storeLogo));
        $hasStoreLogoIcon = is_string($storeLogoIcon) && $storeLogoIcon !== '' && file_exists(public_path($storeLogoIcon));
    @endphp
    <div class="header__utility">
        <div class="header__utility-inner">
            <p class="header__utility-text">
                <svg class="header__utility-icon" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M5 12h14M12 5l7 7-7 7"/>
                </svg>
                Envíos en todo El Salvador
            </p>
            <p class="header__utility-text header__utility-text--muted">
                Piezas vintage curadas · Una sola unidad por artículo
            </p>
        </div>
    </div>

    <div class="header__main">
        <div class="header__inner">
            <a href="{{ Route::has('home') ? route('home') : '/' }}" class="header__logo" aria-label="Le Cameleon — Inicio">
                @if ($hasStoreLogoIcon)
                    <img
                        src="{{ asset($storeLogoIcon) }}"
                        alt=""
                        class="header__logo-img header__logo-img--icon"
                        width="40"
                        height="40"
                        decoding="async"
                        aria-hidden="true"
                    >
                @elseif ($hasStoreLogo)
                    <img
                        src="{{ asset($storeLogo) }}"
                        alt=""
                        class="header__logo-img header__logo-img--full"
                        width="148"
                        height="40"
                        decoding="async"
                        aria-hidden="true"
                    >
                @else
                    <span class="header__logo-mark" aria-hidden="true">LC</span>
                @endif
                <span class="header__logo-text">
                    Le <span class="header__logo-accent">Cameleon</span>
                </span>
            </a>

            @if (Route::has('search') || Route::has('shop.index'))
                <form
                    method="GET"
                    action="{{ Route::has('search') ? route('search') : route('shop.index') }}"
                    class="header__search"
                    role="search"
                >
                    <label for="header-search" class="header__search-label">Buscar en la tienda</label>
                    <div class="header__search-field">
                        <input
                            type="search"
                            id="header-search"
                            name="q"
                            class="header__search-input"
                            placeholder="Buscar piezas vintage, marcas, épocas…"
                            value="{{ request('q') }}"
                            autocomplete="off"
                        >
                        <button type="submit" class="header__search-btn" aria-label="Buscar">
                            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.25" viewBox="0 0 24 24" aria-hidden="true">
                                <circle cx="11" cy="11" r="7"/>
                                <path d="M20 20l-3.5-3.5"/>
                            </svg>
                        </button>
                    </div>
                </form>
            @endif

            <div class="header__actions">
                @auth
                    @if (Route::has('account.index'))
                        <a href="{{ route('account.index') }}" class="header__account">
                            <span class="header__account-line">Hola{{ auth()->user()->name ? ', ' . strtok(auth()->user()->name, ' ') : '' }}</span>
                            <span class="header__account-label">Cuenta</span>
                        </a>
                    @endif
                @else
                    @if (Route::has('login'))
                        <a href="{{ route('login') }}" class="header__account">
                            <span class="header__account-line">Identifícate</span>
                            <span class="header__account-label">Cuenta</span>
                        </a>
                    @endif
                @endauth

                @if (Route::has('cart.index'))
                    <a href="{{ route('cart.index') }}" class="header__cart" aria-label="Carrito">
                        <span class="header__cart-icon-wrap">
                            <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                                <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/>
                                <line x1="3" y1="6" x2="21" y2="6"/>
                                <path d="M16 10a4 4 0 0 1-8 0"/>
                            </svg>
                            @if (($cartCount ?? 0) > 0)
                                <span class="header__cart-count">{{ $cartCount }}</span>
                            @endif
                        </span>
                        <span class="header__cart-label">Carrito</span>
                    </a>
                @endif

                <div class="header__theme">
                    @include('components.theme-toggle')
                </div>

                <button type="button" class="header__menu-toggle" @click="toggle()" aria-label="Abrir menú" :aria-expanded="open">
                    <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                        <line x1="3" y1="12" x2="21" y2="12"/>
                        <line x1="3" y1="6" x2="21" y2="6"/>
                        <line x1="3" y1="18" x2="21" y2="18"/>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <nav class="header__subnav" aria-label="Principal">
        <div class="header__subnav-inner">
            @if (Route::has('shop.index'))
                <a href="{{ route('shop.index') }}" class="header__subnav-link {{ request()->routeIs('shop.*') && !request()->routeIs('shop.show') ? 'header__subnav-link--active' : '' }}">Tienda</a>
            @endif
            @if (Route::has('shop.index'))
                <a href="{{ route('shop.index', ['sort' => 'new']) }}" class="header__subnav-link">Novedades</a>
            @endif
            @if (Route::has('wishlist.index'))
                <a href="{{ route('wishlist.index') }}" class="header__subnav-link {{ request()->routeIs('wishlist.*') ? 'header__subnav-link--active' : '' }}">Favoritos</a>
            @endif
            @if (Route::has('shipping'))
                <a href="{{ route('shipping') }}" class="header__subnav-link {{ request()->routeIs('shipping') ? 'header__subnav-link--active' : '' }}">Envíos</a>
            @endif
            @if (Route::has('about'))
                <a href="{{ route('about') }}" class="header__subnav-link {{ request()->routeIs('about') ? 'header__subnav-link--active' : '' }}">Nuestra historia</a>
            @endif
        </div>
    </nav>

    <nav class="mobile-nav" :class="{ 'mobile-nav--open': open }" x-show="open" x-transition @click.outside="close()" x-cloak aria-label="Menú móvil">
        <div class="mobile-nav__header">
            <p class="mobile-nav__brand">
                @if ($hasStoreLogoIcon)
                    <img src="{{ asset($storeLogoIcon) }}" alt="" class="mobile-nav__logo" width="32" height="32" decoding="async" aria-hidden="true">
                @endif
                Le <span>Cameleon</span>
            </p>
            <button type="button" class="mobile-nav__close" @click="close()" aria-label="Cerrar menú">
                <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                    <line x1="18" y1="6" x2="6" y2="18"/>
                    <line x1="6" y1="6" x2="18" y2="18"/>
                </svg>
            </button>
        </div>

        @if (Route::has('search') || Route::has('shop.index'))
            <form
                method="GET"
                action="{{ Route::has('search') ? route('search') : route('shop.index') }}"
                class="mobile-nav__search"
                role="search"
            >
                <input
                    type="search"
                    name="q"
                    class="mobile-nav__search-input"
                    placeholder="Buscar piezas vintage…"
                    value="{{ request('q') }}"
                >
                <button type="submit" class="mobile-nav__search-btn" aria-label="Buscar">
                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.25" viewBox="0 0 24 24" aria-hidden="true">
                        <circle cx="11" cy="11" r="7"/>
                        <path d="M20 20l-3.5-3.5"/>
                    </svg>
                </button>
            </form>
        @endif

        <ul class="mobile-nav__list">
            @if (Route::has('home'))
                <li><a href="{{ route('home') }}" class="mobile-nav__link" @click="close()">Inicio</a></li>
            @endif
            @if (Route::has('shop.index'))
                <li><a href="{{ route('shop.index') }}" class="mobile-nav__link" @click="close()">Tienda</a></li>
            @endif
            @if (Route::has('wishlist.index'))
                <li><a href="{{ route('wishlist.index') }}" class="mobile-nav__link" @click="close()">Favoritos</a></li>
            @endif
            @auth
                @if (Route::has('account.index'))
                    <li><a href="{{ route('account.index') }}" class="mobile-nav__link" @click="close()">Mi cuenta</a></li>
                @endif
                @if (Route::has('logout'))
                    <li>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="mobile-nav__link mobile-nav__link--button">Salir</button>
                        </form>
                    </li>
                @endif
            @else
                @if (Route::has('login'))
                    <li><a href="{{ route('login') }}" class="mobile-nav__link" @click="close()">Entrar</a></li>
                @endif
            @endauth
        </ul>
    </nav>

    <div class="header__overlay" :class="{ 'header__overlay--visible': open }" x-show="open" x-transition.opacity @click="close()" x-cloak aria-hidden="true"></div>

    <style>[x-cloak] { display: none !important; }</style>
</header>
