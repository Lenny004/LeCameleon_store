<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name', 'Le Cameleon'))</title>
    <meta name="description" content="@yield('meta_description', 'Le Cameleon — curated vintage fashion and unique pieces.')">
    @stack('meta')
    <script>
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

    <main class="page__main">
        @include('components.flash')
        @yield('content')
    </main>

    @include('components.store-footer')
    @stack('scripts')
</body>
</html>
