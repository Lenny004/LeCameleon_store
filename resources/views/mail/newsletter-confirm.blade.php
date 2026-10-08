<!DOCTYPE html>
<html lang="es">
<head><meta charset="utf-8"><title>Confirma tu suscripción</title></head>
<body style="font-family: sans-serif; line-height: 1.5; color: #222;">
    <h1>Confirma tu suscripción</h1>
    <p>Recibimos una solicitud para enviarte novedades de Le Cameleon.</p>
    <p><a href="{{ route('newsletter.confirm', $subscriber->token) }}">Confirmar suscripción</a></p>
    <p>Si no lo solicitaste, puedes ignorar este mensaje.</p>
</body>
</html>
