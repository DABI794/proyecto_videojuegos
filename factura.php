<?php
session_start();
require 'db.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: auth/login.php");
    exit();
}

// Verificar parámetro de confirmación de PayPal
if (!isset($_GET['paypal']) || $_GET['paypal'] !== 'ok') {
    echo "Error: Pago no verificado.";
    exit();
}

$usuario_id = $_SESSION['usuario_id'];
$db = isset($pdo) ? $pdo : $conn;

// Obtener productos del carrito
$stmt = $db->prepare("SELECT carrito.*, productos.nombre, productos.precio 
                      FROM carrito 
                      JOIN productos ON carrito.producto_id = productos.id 
                      WHERE usuario_id = ?");
$stmt->execute([$usuario_id]);
$items = $stmt->fetchAll();

if (count($items) === 0) {
    echo "El carrito está vacío.";
    exit();
}

// Calcular total
$total = 0;
foreach ($items as $item) {
    $total += $item['precio'] * $item['cantidad'];
}

// Insertar en tabla `facturas`
$stmt = $db->prepare("INSERT INTO facturas (usuario_id, total, fecha) VALUES (?, ?, NOW())");
$stmt->execute([$usuario_id, $total]);
$factura_id = $db->lastInsertId();

// Insertar en `factura_detalles`
foreach ($items as $item) {
    $stmt = $db->prepare("INSERT INTO factura_detalles (factura_id, producto_id, cantidad, precio_unitario) 
                          VALUES (?, ?, ?, ?)");
    $stmt->execute([$factura_id, $item['producto_id'], $item['cantidad'], $item['precio']]);
}

// Vaciar carrito
$stmt = $db->prepare("DELETE FROM carrito WHERE usuario_id = ?");
$stmt->execute([$usuario_id]);

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Compra Exitosa</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="stilos/base.css" />
    <link rel="stylesheet" href="stilos/factura.css" />
</head>
<body>
    <div class="mensaje">
        <h1>¡Compra realizada con éxito!</h1>
        <p>Tu pago ha sido procesado correctamente a través de PayPal.</p>
        <p>Total pagado: <strong>Bs <?= number_format($total, 2) ?></strong></p>
        <p><a href="index.php">Volver a la tienda</a></p>
    </div>
</body>
</html>
