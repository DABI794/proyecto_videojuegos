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
    <title>Editar Producto - Admin</title>
    <link rel="stylesheet" href="/proyecto_videojuegos/stilos/style.css" />
    <style>
        body {
            background-color: #f4f7f6;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .container {
            background: #ffffff;
            width: 100%;
            max-width: 500px;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }

        h2 {
            color: #2c3e50;
            text-align: center;
            margin-bottom: 25px;
            font-size: 24px;
            border-bottom: 2px solid #3498db;
            padding-bottom: 10px;
        }

        .alert {
            padding: 12px;
            border-radius: 6px;
            margin-bottom: 20px;
            text-align: center;
            font-weight: bold;
        }
        .success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }

        label {
            display: block;
            margin-bottom: 8px;
            color: #34495e;
            font-weight: 600;
        }

        input[type="text"],
        input[type="number"],
        select {
            width: 100%;
            padding: 12px;
            margin-bottom: 20px;
            border: 1px solid #dcdde1;
            border-radius: 8px;
            box-sizing: border-box; /* Asegura que el padding no saca el input del contenedor */
            font-size: 15px;
        }

        .preview-box {
            text-align: center;
            background: #f9f9f9;
            padding: 15px;
            border-radius: 8px;
            border: 1px dashed #ccc;
            margin-bottom: 20px;
        }

        .preview-box img {
            max-width: 120px;
            border-radius: 5px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }

        .actions {
            display: flex;
            gap: 10px;
        }

        button {
            flex: 2;
            background-color: #3498db;
            color: white;
            border: none;
            padding: 14px;
            border-radius: 8px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: background 0.3s;
        }

        button:hover { background-color: #2980b9; }

        .btn-cancel {
            flex: 1;
            background-color: #95a5a6;
            color: white;
            text-decoration: none;
            text-align: center;
            padding: 14px;
            border-radius: 8px;
            font-size: 16px;
            font-weight: bold;
        }

        .btn-cancel:hover { background-color: #7f8c8d; }
    </style>
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