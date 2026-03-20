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
    <title>Carrito de Compras</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap');

        * {
            box-sizing: border-box;
        }
        body {
            background: linear-gradient(135deg, #0f2027, #203a43, #2c5364);
            color: #f0f0f0;
            font-family: 'Roboto', sans-serif;
            margin: 0;
            padding: 20px;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        h2 {
            margin-bottom: 30px;
            color: #00ffc3;
            text-shadow: 0 0 10px #00ffc3;
            font-weight: 700;
        }
        .carrito-container {
            width: 100%;
            max-width: 700px;
            background: rgba(30, 30, 30, 0.9);
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 0 20px #00ffc3aa;
        }
        .carrito-item {
            background: #121212;
            border-radius: 12px;
            padding: 15px 20px;
            margin-bottom: 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 0 10px #00ffc388;
            transition: transform 0.2s ease;
        }
        .carrito-item:hover {
            transform: scale(1.02);
            box-shadow: 0 0 15px #00ffccbb;
        }
        .carrito-item div {
            font-size: 1.15em;
            font-weight: 500;
            color: #a0f0e0;
        }
        .cantidad {
            font-weight: 700;
            color: #00ffc3;
            margin-left: 8px;
        }
        form {
            margin: 0;
        }
        button.eliminar-btn {
            background: #ff4d4d;
            border: none;
            color: white;
            padding: 10px 16px;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 700;
            font-size: 0.9em;
            transition: background 0.3s ease;
        }
        button.eliminar-btn:hover {
            background: #ff1a1a;
        }
        .vacío {
            text-align: center;
            font-size: 1.3em;
            color: #888;
            margin: 60px 0;
        }
        h3.total {
            text-align: center;
            color: #00ffb0;
            font-weight: 700;
            margin-top: 30px;
            font-size: 1.5em;
            text-shadow: 0 0 8px #00ffb0;
        }
        #paypal-button-container {
            margin-top: 25px;
            text-align: center;
        }
        input[type="submit"].paypal-submit-btn {
            background-color: #0070ba;
            border: none;
            color: white;
            padding: 12px 28px;
            font-size: 16px;
            font-weight: 700;
            border-radius: 10px;
            cursor: pointer;
            box-shadow: 0 0 15px #0070baaa;
            transition: background-color 0.3s ease;
            display: block;
            margin: 30px auto 0 auto;
            max-width: 250px;
        }
        input[type="submit"].paypal-submit-btn:hover {
            background-color: #004f8b;
            box-shadow: 0 0 20px #004f8baa;
        }
    </style>
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
                <br><small style="color:#44ffcc;">BS <?= number_format($item['precio'], 2) ?> c/u</small>
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
