<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Bienvenido</title>
</head>
<body style="font-family: sans-serif; line-height: 1.5; color: #222;">
    <h1>¡Bienvenido, {{ $user->name }}!</h1>
    <p>Tu correo fue verificado y tu cuenta ya está lista para disfrutar la tienda.</p>
    @if (Route::has('account.profile'))
        <p><a href="{{ route('account.profile') }}">Ir a mi cuenta</a></p>
    @endif
</body>
</html>
