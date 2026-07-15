<header class="header" x-data="mobileNav()">
    <div class="header__inner">
        <a href="{{ Route::has('home') ? route('home') : '/' }}" class="header__logo">
            Le <span>Cameleon</span>
        </a>

        <nav class="header__nav" aria-label="Principal">
            @if (Route::has('shop.index'))
                <a href="{{ route('shop.index') }}" class="header__nav-link {{ request()->routeIs('shop.*') ? 'header__nav-link--active' : '' }}">Tienda</a>
            @endif
            @if (Route::has('shop.index'))
                <a href="{{ route('shop.index', ['sort' => 'new']) }}" class="header__nav-link">Novedades</a>
            @endif
            @if (Route::has('wishlist.index'))
                <a href="{{ route('wishlist.index') }}" class="header__nav-link {{ request()->routeIs('wishlist.*') ? 'header__nav-link--active' : '' }}">Favoritos</a>
            @endif
        </nav>

        <div class="header__actions">
            @include('components.theme-toggle')

            @auth
                @if (Route::has('account.index'))
                    <a href="{{ route('account.index') }}" class="header__icon-link" aria-label="Mi cuenta">
                        <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                    </a>
                @endif
            @else
                @if (Route::has('login'))
                    <a href="{{ route('login') }}" class="btn btn--ghost btn--sm">Entrar</a>
                @endif
            @endauth

            @if (Route::has('cart.index'))
                <a href="{{ route('cart.index') }}" class="header__icon-link header__cart-wrap" aria-label="Carrito">
                    <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
                    @if (($cartCount ?? 0) > 0)
                        <span class="header__cart-count">{{ $cartCount }}</span>
                    @endif
                </a>
            @endif

            <button type="button" class="header__menu-toggle" @click="toggle()" aria-label="Abrir menú">
                <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
            </button>
        </div>
    </div>

    <nav class="mobile-nav" :class="{ 'mobile-nav--open': open }" x-show="open" x-transition @click.outside="close()" x-cloak aria-label="Menú móvil">
        <button type="button" class="btn btn--ghost btn--sm mobile-nav__close" @click="close()">Cerrar</button>
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
                            <button type="submit" class="mobile-nav__link" style="background:none;border:none;cursor:pointer;">Salir</button>
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
    <style>[x-cloak] { display: none !important; }</style>
</header>
