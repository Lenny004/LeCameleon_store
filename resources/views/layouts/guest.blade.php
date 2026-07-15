<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name', 'Le Cameleon'))</title>
    <script>
        (function () {
            var k = 'lecameleon-theme';
            var t = localStorage.getItem(k);
            if (!t && window.matchMedia('(prefers-color-scheme: dark)').matches) t = 'dark';
            document.documentElement.setAttribute('data-theme', t || 'light');
        })();
    </script>
    @fonts
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    @yield('content')
</body>
</html>
