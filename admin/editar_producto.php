<?php
require_once '../db.php';
include 'proteger.php';

if (!isset($_GET['id'])) {
    header("Location: agregar_producto.php");
    exit;
}

$id = intval($_GET['id']);
$mensaje = '';
$tipo_mensaje = ''; // Para diferenciar éxito de error

// Obtener datos actuales del producto
$stmt = $conn->prepare("SELECT * FROM productos WHERE id = ?");
$stmt->execute([$id]);
$producto = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$producto) {
    header("Location: agregar_producto.php");
    exit;
}

// Obtener categorías para el select
$categoriaStmt = $conn->query("SELECT * FROM categorias ORDER BY nombre");
$categorias = $categoriaStmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre'];
    $precio = $_POST['precio'];
    $categoria_id = $_POST['categoria_id'];

    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] == 0) {
        $ext = strtolower(pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        
        if (in_array($ext, $allowed)) {
            $nombre_imagen = uniqid() . "." . $ext;
            $ruta = "../assets/" . $nombre_imagen;
            
            if (move_uploaded_file($_FILES['imagen']['tmp_name'], $ruta)) {
                // Borrar imagen antigua si existe
                $ruta_antigua = "../assets/" . $producto['imagen'];
                if (!empty($producto['imagen']) && file_exists($ruta_antigua)) {
                    unlink($ruta_antigua);
                }

                $sql = "UPDATE productos SET nombre = ?, precio = ?, imagen = ?, categoria_id = ? WHERE id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->execute([$nombre, $precio, $nombre_imagen, $categoria_id, $id]);
                $mensaje = "✅ Producto e imagen actualizados con éxito.";
                $tipo_mensaje = "success";
            }
        } else {
            $mensaje = "❌ Formato de imagen no permitido.";
            $tipo_mensaje = "error";
        }
    } else {
        $sql = "UPDATE productos SET nombre = ?, precio = ?, categoria_id = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$nombre, $precio, $categoria_id, $id]);
        $mensaje = "✅ Datos actualizados correctamente.";
        $tipo_mensaje = "success";
    }

    // Recargar datos actualizados
    $stmt = $conn->prepare("SELECT * FROM productos WHERE id = ?");
    $stmt->execute([$id]);
    $producto = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Editar Producto - Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Space+Grotesk:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../stilos/base.css" />
    <link rel="stylesheet" href="../stilos/editar.css" />
</head>
<body>

<div class="container">
    <h2>Editar Producto</h2>

    <?php if ($mensaje): ?>
        <div class="alert <?= $tipo_mensaje ?>"><?= $mensaje ?></div>
    <?php endif; ?>

    <form action="editar_producto.php?id=<?= $id ?>" method="POST" enctype="multipart/form-data">
        <label>Nombre del Producto:</label>
        <input type="text" name="nombre" value="<?= htmlspecialchars($producto['nombre']) ?>" required />

        <label>Precio (BS):</label>
        <input type="number" step="0.01" name="precio" value="<?= htmlspecialchars($producto['precio']) ?>" required />
        
        <label>Categoría:</label>
        <select name="categoria_id" required>
            <?php foreach ($categorias as $cat): ?>
                <option value="<?= $cat['id'] ?>" <?= $cat['id'] == $producto['categoria_id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($cat['nombre']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <div class="preview-box">
            <label>Imagen Actual:</label>
            <img src="../assets/<?= htmlspecialchars($producto['imagen']) ?>" alt="Producto">
            <p style="font-size: 12px; color: #7f8c8d; margin-top: 10px;">Subir nueva para cambiar</p>
            <input type="file" name="imagen" accept="image/*" />
        </div>

        <div class="actions">
            <button type="submit">Guardar Cambios</button>
            <a href="agregar_producto.php" class="btn-cancel">Volver</a>
        </div>
    </form>
</div>

</body>
</html>