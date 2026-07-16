<header
    class="header"
    x-data="mobileNav()"
    x-effect="document.body.classList.toggle('has-mobile-nav-open', open)"
>
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
            <div class="header__logo">
                @include('components.brand-logo', [
                    'variant' => 'horizontal',
                    'href' => Route::has('home') ? route('home') : '/',
                    'size' => 'md',
                ])
            </div>

            @if (Route::has('search') || Route::has('shop.index'))
                <form
                    method="GET"
                    action="{{ Route::has('search') ? route('search') : route('shop.index') }}"
                    class="header__search"
                    role="search"
                >
                    <label for="header-search" class="sr-only">Buscar en la tienda</label>
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
                    <a href="{{ route('cart.index') }}" class="header__cart">
                        <span class="header__cart-icon-wrap">
                            <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                                <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/>
                                <line x1="3" y1="6" x2="21" y2="6"/>
                                <path d="M16 10a4 4 0 0 1-8 0"/>
                            </svg>
                            @if (($cartCount ?? 0) > 0)
                                <span class="header__cart-count" aria-hidden="true">{{ $cartCount }}</span>
                            @endif
                        </span>
                        <span class="header__cart-label">
                            Carrito
                            @if (($cartCount ?? 0) > 0)
                                <span class="sr-only">, {{ $cartCount }} {{ $cartCount === 1 ? 'artículo' : 'artículos' }}</span>
                            @endif
                        </span>
                    </a>
                @endif

                <div class="header__theme">
                    @include('components.theme-toggle')
                </div>

                <button type="button" class="header__menu-toggle" @click="toggle()" :aria-label="open ? 'Cerrar menú' : 'Abrir menú'" :aria-expanded="open ? 'true' : 'false'" aria-controls="mobile-nav">
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

    <nav
        id="mobile-nav"
        class="mobile-nav"
        :class="{ 'mobile-nav--open': open }"
        @click.outside="close()"
        aria-label="Menú móvil"
        :aria-hidden="!open"
    >
        <div class="mobile-nav__header">
            <div class="mobile-nav__brand">
                @include('components.brand-logo', [
                    'variant' => 'compact',
                    'href' => Route::has('home') ? route('home') : '/',
                    'size' => 'sm',
                ])
            </div>
            <div class="mobile-nav__actions">
                @include('components.theme-toggle')
                <button type="button" class="mobile-nav__close" @click="close()" aria-label="Cerrar menú">
                    <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                        <line x1="18" y1="6" x2="6" y2="18"/>
                        <line x1="6" y1="6" x2="18" y2="18"/>
                    </svg>
                </button>
            </div>
        </div>

        @if (Route::has('search') || Route::has('shop.index'))
            <form
                method="GET"
                action="{{ Route::has('search') ? route('search') : route('shop.index') }}"
                class="mobile-nav__search"
                role="search"
            >
                <label for="mobile-nav-search" class="sr-only">Buscar en la tienda</label>
                <input
                    type="search"
                    id="mobile-nav-search"
                    name="q"
                    class="mobile-nav__search-input"
                    placeholder="Buscar piezas vintage…"
                    value="{{ request('q') }}"
                    autocomplete="off"
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

    <div
        class="header__overlay"
        :class="{ 'header__overlay--visible': open }"
        @click="close()"
        :aria-hidden="!open"
    ></div>
</header>
