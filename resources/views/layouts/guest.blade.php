<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name', 'Le Cameleon'))</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,500;0,9..144,600;0,9..144,700;1,9..144,500;1,9..144,600&family=Source+Sans+3:ital,wght@0,400;0,500;0,600;0,700;1,400&display=swap" rel="stylesheet">
    <script>
        (function () {
            var k = 'lecameleon-theme-v2';
            var t = localStorage.getItem(k);
            document.documentElement.setAttribute('data-theme', t === 'dark' ? 'dark' : 'light');
        })();
    </script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="page page--guest">
    <div class="guest-shell">
        <div class="guest-shell__ambient" aria-hidden="true">
            <span class="guest-shell__orb guest-shell__orb--primary"></span>
            <span class="guest-shell__orb guest-shell__orb--accent"></span>
        </div>

        <header class="guest-shell__header" aria-label="Acceso a la tienda">
            @include('components.brand-logo', [
                'variant' => 'horizontal',
                'size' => 'md',
                'href' => Route::has('home') ? route('home') : '/',
            ])
            <div class="guest-shell__theme">
                @include('components.theme-toggle')
            </div>
        </header>

        <main id="main-content" class="guest-shell__main">
            @yield('content')
        </main>

        <footer class="guest-shell__footer">
            &copy; {{ date('Y') }} {{ config('app.name', 'Le Cameleon') }}
        </footer>
    </div>
</body>
</html>
