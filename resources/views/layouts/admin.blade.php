<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin') — {{ config('app.name', 'Le Cameleon') }}</title>
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
<body class="admin" x-data="adminSidebar()">
    <div class="admin-overlay" x-show="open" x-transition @click="close()" x-cloak></div>

    @include('components.admin-sidebar')

    <div class="admin-main">
        @include('components.admin-topbar')
        <div class="admin-content">
            @include('components.flash')
            @yield('content')
        </div>
    </div>

    @stack('scripts')
    <style>[x-cloak] { display: none !important; }</style>
</body>
</html>
