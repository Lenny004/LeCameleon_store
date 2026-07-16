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
        <header class="guest-shell__header">
            @include('components.brand-logo', [
                'variant' => 'horizontal',
                'size' => 'md',
                'href' => Route::has('home') ? route('home') : '/',
            ])
            <div class="guest-shell__theme">
                @include('components.theme-toggle')
            </div>
        </header>

        <main class="guest-shell__main">
            @yield('content')
        </main>
    </div>
</body>
</html>
