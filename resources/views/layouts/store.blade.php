<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', $storeName)</title>
    <meta name="description" content="@yield('meta_description', 'Le Cameleon — moda vintage seleccionada y piezas únicas.')">
    @php
        $canonicalQuery = request()->only('page');
        $canonical = url()->current().($canonicalQuery !== [] ? '?'.http_build_query($canonicalQuery) : '');
        $defaultOgImage = asset(config('store.brand_logo_wide'));
        $shopFilterQuery = request()->except('page');
        $shouldNoIndex = request()->is('cart', 'checkout', 'login', 'register', 'forgot-password', 'reset-password/*', 'account', 'account/*', 'wishlist')
            || request()->is('search')
            || (request()->is('shop') && $shopFilterQuery !== [])
            || request()->is('checkout/success/*', 'pedido/*', 'two-factor-challenge', 'email/verify*');
    @endphp
    <link rel="canonical" href="{{ $canonical }}">
    <meta property="og:site_name" content="{{ $storeName }}">
    <meta property="og:type" content="@yield('og_type', 'website')">
    <meta property="og:title" content="@yield('title', $storeName)">
    <meta property="og:description" content="@yield('meta_description', 'Le Cameleon — moda vintage seleccionada y piezas únicas.')">
    <meta property="og:url" content="{{ $canonical }}">
    <meta property="og:image" content="@yield('og_image', $defaultOgImage)">
    <meta name="twitter:card" content="summary_large_image">
    @if ($shouldNoIndex)
        <meta name="robots" content="noindex,follow">
    @endif
    @stack('meta')
    <script nonce="{{ Vite::cspNonce() }}">
        (function () {
            var k = 'lecameleon-theme-v2';
            var t = localStorage.getItem(k);
            document.documentElement.setAttribute('data-theme', t === 'dark' ? 'dark' : 'light');
        })();
    </script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('head')
</head>
<body class="page page--store">
    @include('components.store-header')

    @if (! empty($whatsapp))
        <a class="whatsapp-float" href="{{ $whatsapp }}" target="_blank" rel="noopener" aria-label="Escríbenos por WhatsApp">
            <svg class="whatsapp-float__icon" viewBox="0 0 24 24" width="24" height="24" aria-hidden="true" fill="currentColor"><path d="M12.04 2C6.58 2 2.15 6.4 2.15 11.83c0 1.74.46 3.44 1.34 4.94L2 22l5.39-1.41a10 10 0 0 0 4.65 1.18h.01c5.46 0 9.89-4.4 9.89-9.83C21.94 6.4 17.5 2 12.04 2zm5.76 13.94c-.24.68-1.41 1.3-1.96 1.38-.5.07-1.14.1-1.84-.12-.42-.12-.97-.31-1.67-.61-2.94-1.27-4.86-4.23-5.01-4.43-.15-.2-1.2-1.6-1.2-3.05 0-1.45.76-2.16 1.03-2.46.27-.3.59-.37.79-.37h.57c.18 0 .43-.07.67.51.24.58.83 2.01.9 2.16.07.15.12.32.02.52-.1.2-.15.32-.29.49-.15.17-.31.38-.44.51-.15.15-.3.3-.13.59.17.29.76 1.25 1.63 2.03 1.12 1 2.06 1.31 2.35 1.46.29.15.46.12.63-.07.17-.2.73-.85.93-1.14.2-.29.39-.24.66-.15.27.1 1.71.81 2 .95.29.15.49.22.56.34.07.12.07.7-.17 1.38z"/></svg>
            <span class="whatsapp-float__label">WhatsApp</span>
        </a>
    @endif

    <main class="page__main">
        @include('components.flash')
        @yield('content')
    </main>

    @include('components.store-footer')
    @stack('scripts')
</body>
</html>
