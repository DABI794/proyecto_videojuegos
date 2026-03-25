<!DOCTYPE html>
<html>
<head>
    <title>Bienvenida</title>
</head>
<body style="font-family: sans-serif; background-color: #f8fafc; padding: 20px;">
    <div style="max-width: 600px; margin: 0 auto; background-color: #ffffff; padding: 40px; border-radius: 8px;">
        <h1 style="color: #6366f1;">¡Hola, {{ $user->name }}!</h1>
        <p>Gracias por unirte a <strong>GameStore</strong>. Estamos muy emocionados de tenerte con nosotros.</p>
        <p>Ahora puedes explorar nuestra colección de videojuegos, añadir tus favoritos al carrito y realizar pedidos de forma rápida y segura.</p>
        <div style="margin-top: 30px;">
            <a href="{{ url('/') }}" style="background-color: #6366f1; color: white; padding: 12px 24px; text-decoration: none; border-radius: 6px; font-weight: bold;">Explorar Tienda</a>
        </div>
        <hr style="margin-top: 40px; border: 0; border-top: 1px solid #e2e8f0;">
        <p style="font-size: 12px; color: #64748b;">© {{ date('Y') }} GameStore Bolivia. Todos los derechos reservados.</p>
    </div>
</body>
</html>
