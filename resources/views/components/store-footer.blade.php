<footer class="footer">
    <div class="footer__inner">
        <div class="footer__grid">
            <div>
                <p class="footer__brand">Le Cameleon</p>
                <p class="footer__tagline">Piezas vintage seleccionadas con criterio. Moda y objetos con historia, listos para una nueva vida.</p>
                @if (Route::has('newsletter.store'))
                    <form method="POST" action="{{ route('newsletter.store') }}" class="footer__newsletter">
                        @csrf
                        <p class="footer__newsletter-label">Boletín</p>
                        <div class="footer__newsletter-row">
                            <input
                                type="email"
                                name="email"
                                class="footer__newsletter-input @error('email') form-input--error @enderror"
                                placeholder="Tu correo"
                                value="{{ old('email') }}"
                                required
                                autocomplete="email"
                                aria-label="Correo para el boletín"
                            >
                            <button type="submit" class="btn btn--primary btn--sm">Suscribirme</button>
                        </div>
                    </form>
                @endif
            </div>
            <div>
                <p class="footer__heading">Tienda</p>
                <ul class="footer__links">
                    @if (Route::has('shop.index'))
                        <li><a href="{{ route('shop.index') }}" class="footer__link">Catálogo</a></li>
                    @endif
                    @if (Route::has('shop.index'))
                        <li><a href="{{ route('shop.index', ['sort' => 'new']) }}" class="footer__link">Novedades</a></li>
                    @endif
                    @if (Route::has('wishlist.index'))
                        <li><a href="{{ route('wishlist.index') }}" class="footer__link">Favoritos</a></li>
                    @endif
                </ul>
            </div>
            <div>
                <p class="footer__heading">Ayuda</p>
                <ul class="footer__links">
                    @if (Route::has('about'))
                        <li><a href="{{ route('about') }}" class="footer__link">Nuestra historia</a></li>
                    @endif
                    @if (Route::has('contact.show'))
                        <li><a href="{{ route('contact.show') }}" class="footer__link">Contacto</a></li>
                    @endif
                    @if (Route::has('faq'))
                        <li><a href="{{ route('faq') }}" class="footer__link">Preguntas frecuentes</a></li>
                    @endif
                    @if (Route::has('returns'))
                        <li><a href="{{ route('returns') }}" class="footer__link">Devoluciones</a></li>
                    @endif
                </ul>
            </div>
            <div>
                <p class="footer__heading">Cuenta</p>
                <ul class="footer__links">
                    @auth
                        @if (Route::has('account.index'))
                            <li><a href="{{ route('account.index') }}" class="footer__link">Mi perfil</a></li>
                        @endif
                        @if (Route::has('account.orders.index'))
                            <li><a href="{{ route('account.orders.index') }}" class="footer__link">Mis pedidos</a></li>
                        @endif
                    @else
                        @if (Route::has('login'))
                            <li><a href="{{ route('login') }}" class="footer__link">Iniciar sesión</a></li>
                        @endif
                        @if (Route::has('register'))
                            <li><a href="{{ route('register') }}" class="footer__link">Crear cuenta</a></li>
                        @endif
                    @endauth
                </ul>
            </div>
        </div>
        <div class="footer__bottom">
            <span>&copy; {{ date('Y') }} Le Cameleon. Todos los derechos reservados.</span>
            @include('components.theme-toggle')
        </div>
    </div>
</footer>
