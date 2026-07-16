<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin') — {{ config('app.name', 'Le Cameleon') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,500;0,9..144,600;0,9..144,700;1,9..144,500;1,9..144,600&family=Source+Sans+3:ital,wght@0,400;0,500;0,600;0,700;1,400&display=swap" rel="stylesheet">
    <script>
        (function () {
            var k = 'lecameleon-theme';
            var t = localStorage.getItem(k);
            document.documentElement.setAttribute('data-theme', t === 'dark' || t === 'light' ? t : 'light');
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
