<nav class="account-nav" aria-label="Cuenta">
    @if (Route::has('account.index'))
        <a href="{{ route('account.index') }}" class="account-nav__link">Perfil</a>
    @endif
    @if (Route::has('account.orders.index'))
        <a href="{{ route('account.orders.index') }}" class="account-nav__link">Mis pedidos</a>
    @endif
    @if (Route::has('account.addresses.index'))
        <a href="{{ route('account.addresses.index') }}" class="account-nav__link account-nav__link--active">Direcciones</a>
    @endif
    @if (Route::has('account.offers.index'))
        <a href="{{ route('account.offers.index') }}" class="account-nav__link">Mis ofertas</a>
    @endif
    @if (Route::has('account.saved-searches.index'))
        <a href="{{ route('account.saved-searches.index') }}" class="account-nav__link">Búsquedas guardadas</a>
    @endif
    @if (Route::has('wishlist.index'))
        <a href="{{ route('wishlist.index') }}" class="account-nav__link">Favoritos</a>
    @endif
    @if (Route::has('logout'))
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="account-nav__logout">Cerrar sesión</button>
        </form>
    @endif
</nav>
