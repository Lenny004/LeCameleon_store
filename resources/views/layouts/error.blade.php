<!DOCTYPE html>
<html lang="es" data-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Le Cameleon')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="page page--guest">
    <div class="guest-shell">
        <main id="main-content" class="guest-shell__main">
            @yield('content')
        </main>
    </div>
</body>
</html>
