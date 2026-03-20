<?php
session_start();
require '../db.php';  

// Verifica que el usuario esté logueado
if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

$usuario_id = $_SESSION['usuario_id'];

// Verifica conexión
if (!isset($pdo) && !isset($conn)) {
    die("Error: Conexión a base de datos no definida.");
}
$db = isset($pdo) ? $pdo : $conn;

// Agregar producto al carrito
if (isset($_POST['agregar'])) {
    $producto_id = $_POST['producto_id'];
    $stmt = $db->prepare("SELECT * FROM carrito WHERE usuario_id = ? AND producto_id = ?");
    $stmt->execute([$usuario_id, $producto_id]);
    $existe = $stmt->fetch();

    if ($existe) {
        $stmt = $db->prepare("UPDATE carrito SET cantidad = cantidad + 1 WHERE id = ?");
        $stmt->execute([$existe['id']]);
    } else {
        $stmt = $db->prepare("INSERT INTO carrito (usuario_id, producto_id, cantidad) VALUES (?, ?, 1)");
        $stmt->execute([$usuario_id, $producto_id]);
    }
    header("Location: ../carrito/ver_carrito.php");
    exit();
}

// Eliminar producto del carrito
if (isset($_POST['eliminar'])) {
    $carrito_id = $_POST['carrito_id'];
    $stmt = $db->prepare("DELETE FROM carrito WHERE id = ? AND usuario_id = ?");
    $stmt->execute([$carrito_id, $usuario_id]);
    header("Location: ../carrito/ver_carrito.php");
    exit();
}

// Obtener productos del carrito
$stmt = $db->prepare("SELECT carrito.*, productos.nombre, productos.precio FROM carrito 
    JOIN productos ON carrito.producto_id = productos.id WHERE usuario_id = ?");
$stmt->execute([$usuario_id]);
$items = $stmt->fetchAll();

// Calcular total
$total = 0;
foreach ($items as $item) {
    $total += $item['precio'] * $item['cantidad'];
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Carrito de Compras</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/proyecto_videojuegos/stilos/base.css">
    <link rel="stylesheet" href="/proyecto_videojuegos/stilos/carrito.css">
</head>
<body>

<h2>Tu Carrito de Compras</h2>

<div class="carrito-container">
<?php if (count($items) === 0): ?>
    <p class="vacío">Tu carrito está vacío.</p>
<?php else: ?>
    <?php foreach ($items as $item): ?>
        <div class="carrito-item">
            <div>
                <?= htmlspecialchars($item['nombre']) ?> 
                <span class="cantidad">x <?= intval($item['cantidad']) ?></span> 
                <br><small style="color:#94a3b8;">BS <?= number_format($item['precio'], 2) ?> c/u</small>
            </div>
            <form method="POST" action="">
                <input type="hidden" name="carrito_id" value="<?= $item['id'] ?>" />
                <button type="submit" name="eliminar" class="eliminar-btn" title="Eliminar producto">&times;</button>
            </form>
        </div>
    <?php endforeach; ?>

    <h3 class="total">Total a pagar: $<?= number_format($total, 2) ?></h3>

    <div id="paypal-button-container"></div>

    <!-- Formulario clásico como opción adicional -->
    <form action="https://www.sandbox.paypal.com/cgi-bin/webscr" method="post" style="text-align:center;">
        <input type="hidden" name="cmd" value="_xclick">
        <input type="hidden" name="business" value="sb-4zjsi40675578@business.example.com"> <!-- Tu correo sandbox -->
        <input type="hidden" name="item_name" value="Compra en Tienda de Videojuegos">
        <input type="hidden" name="amount" value="<?= number_format($total, 2, '.', '') ?>">
        <input type="hidden" name="currency_code" value="BS">
        <input type="hidden" name="return" value="http://localhost/factura.php?paypal=ok">
        <input type="hidden" name="cancel_return" value="http://localhost/carrito/ver_carrito.php">

        <input type="submit" value="Pagar con PayPal" class="paypal-submit-btn">
    </form>
<?php endif; ?>
</div>

<!-- PayPal SDK con enable-funding=card para pagos con tarjeta -->
<script src="https://www.paypal.com/sdk/js?client-id=ARQjmV8o6de60bty5PMaPvqFGJ8Pxc8Gzwnlu9CNqE3MUB2TZ5r9OSDTr60ipkoE_cEtXXSKpuZkhS7P&currency=USD&enable-funding=card"></script>
<script>
paypal.Buttons({
    createOrder: function(data, actions) {
        return actions.order.create({
            purchase_units: [{
                amount: {
                    value: '<?= number_format($total, 2, '.', '') ?>'
                },
                description: 'Compra en tienda de videojuegos'
            }]
        });
    },
    onApprove: function(data, actions) {
        return actions.order.capture().then(function(details) {
            alert('Pago aprobado por ' + details.payer.name.given_name);
            window.location.href = "../factura.php?paypal=ok";
        });
    },
    onError: function(err) {
        console.error(err);
        alert("Hubo un error procesando el pago.");
    }
}).render('#paypal-button-container');
</script>

</body>
</html>
