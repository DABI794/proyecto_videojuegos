<?php
session_start();
require_once '../db.php';
include 'proteger.php';



// Obtener categorías
$stmtCat = $conn->query("SELECT * FROM categorias ORDER BY nombre");
$categorias = $stmtCat->fetchAll(PDO::FETCH_ASSOC);



// Obtener productos
$sql = "SELECT p.*, c.nombre AS categoria_nombre 
        FROM productos p 
        LEFT JOIN categorias c ON p.categoria_id = c.id 
        ORDER BY p.id DESC";
$stmt = $conn->query($sql);
$productos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <title>Panel Admin - Productos</title>
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700&family=Roboto:wght@300;400&display=swap" rel="stylesheet">
    <style>
        :root {
            --neon-cyan: #0ff;
            --dark-bg: #121212;
            --sidebar-color: #0a0a0a;
            --text-gray: #b3b3b3;
        }

        body {
            margin: 0;
            font-family: 'Roboto', sans-serif;
            display: flex;
            height: 100vh;
            background-color: var(--dark-bg);
            color: white;
        }

        /* Sidebar */
        .sidebar {
            width: 240px;
            background-color: var(--sidebar-color);
            border-right: 1px solid #333;
            padding: 25px;
            display: flex;
            flex-direction: column;
        }

        .sidebar h2 {
            font-family: 'Orbitron', sans-serif;
            font-size: 18px;
            color: var(--neon-cyan);
            margin-bottom: 30px;
            text-transform: uppercase;
            text-shadow: 0 0 5px var(--neon-cyan);
        }

        .sidebar a {
            display: block;
            padding: 12px 15px;
            color: var(--text-gray);
            text-decoration: none;
            margin-bottom: 10px;
            border-radius: 8px;
            transition: 0.3s;
            font-size: 14px;
        }

        .sidebar a:hover {
            background: rgba(0, 255, 255, 0.1);
            color: white;
            box-shadow: inset 0 0 5px var(--neon-cyan);
        }

        .btn-logout {
            margin-top: auto;
            background-color: #ff4444 !important;
            color: white !important;
            text-align: center;
            font-weight: bold;
        }

        .btn-logout:hover {
            background-color: #cc0000 !important;
            box-shadow: none !important;
        }

        /* Contenido */
        .content {
            flex: 1;
            padding: 40px;
            overflow-y: auto;
        }

        .header-panel {
            border-bottom: 1px solid #333;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }

        .header-panel h2 {
            font-family: 'Orbitron', sans-serif;
            margin: 0;
            font-size: 24px;
        }

        .mensaje {
            padding: 10px;
            background: rgba(0, 255, 0, 0.1);
            border: 1px solid #00ff00;
            color: #00ff00;
            border-radius: 5px;
            margin-bottom: 20px;
        }

        /* Formulario */
        .formulario {
            background: #1e1e1e;
            padding: 25px;
            border-radius: 12px;
            margin-bottom: 30px;
            border: 1px solid #333;
        }

        .formulario label { display: block; margin-bottom: 5px; font-size: 13px; color: var(--text-gray); }
        
        .formulario input, .formulario select {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            background: #0a0a0a;
            border: 1px solid #444;
            color: white;
            border-radius: 5px;
            box-sizing: border-box;
        }

        .formulario button {
            padding: 12px 20px;
            background-color: var(--neon-cyan);
            color: black;
            border: none;
            border-radius: 6px;
            font-weight: bold;
            cursor: pointer;
            text-transform: uppercase;
        }

        /* Tabla */
        table {
            width: 100%;
            border-collapse: collapse;
            background: #1e1e1e;
            border-radius: 12px;
            overflow: hidden;
        }

        th {
            background: #2a2a2a;
            color: var(--neon-cyan);
            text-align: left;
            padding: 15px;
            font-size: 13px;
            text-transform: uppercase;
        }

        td {
            padding: 15px;
            border-bottom: 1px solid #333;
            font-size: 14px;
        }

        tr:hover { background: #252525; }

        img { width: 50px; height: 50px; object-fit: cover; border-radius: 4px; border: 1px solid #444; }

        .acciones a { text-decoration: none; font-size: 12px; font-weight: bold; margin-right: 10px; }
        .edit { color: var(--neon-cyan); }
        .delete { color: #ff4444; }
    </style>
    <script>
        function mostrarSeccion(id) {
            document.getElementById("formulario").style.display = (id === 'formulario') ? 'block' : 'none';
            document.getElementById("tabla").style.display = (id === 'tabla') ? 'block' : 'none';
        }
    </script>
</head>

<body>
    <div class="sidebar">
        <h2>Admin Panel</h2>
        <a href="#" onclick="mostrarSeccion('tabla')">📦 Ver Inventario</a>
        <a href="#" onclick="mostrarSeccion('formulario')">➕ Agregar Nuevo</a>
        
        <a href="../logout.php" class="btn-logout">SISTEMA OFF</a>
    </div>

    <div class="content">
        <div class="header-panel">
            <h2>Gestión de Productos</h2>
        </div>

        <?php if (isset($mensaje)) echo "<div class='mensaje'>$mensaje</div>"; ?>

        <div id="formulario" class="formulario" style="display:none;">
            <h3>Subir nuevo registro</h3>
            <form method="POST" action="" enctype="multipart/form-data">
                <label>Nombre del Producto</label>
                <input type="text" name="nombre" required>

                <label>Precio (BS)</label>
                <input type="number" name="precio" step="0.01" required>

                <label>Archivo de Imagen</label>
                <input type="file" name="imagen" accept="image/*" required>

                <label>Categoría</label>
                <select name="categoria_id" required>
                    <option value="">Seleccionar...</option>
                    <?php foreach ($categorias as $cat): ?>
                        <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['nombre']) ?></option>
                    <?php endforeach; ?>
                </select>

                <button type="submit">Guardar en Base de Datos</button>
            </form>
        </div>

        <div id="tabla">
            <table>
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th>Precio</th>
                        <th>Imagen</th>
                        <th>Categoría</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($productos as $prod): ?>
                        <tr>
                            <td><?= htmlspecialchars($prod['nombre']) ?></td>
                            <td style="color: var(--neon-cyan);">BS <?= number_format($prod['precio'], 2) ?></td>
                            <td>
                                <?php if (filter_var($prod['imagen'], FILTER_VALIDATE_URL)): ?>
                                    <img src="<?= htmlspecialchars($prod['imagen']) ?>" alt="Img">
                                <?php else: ?>
                                    <img src="/proyecto_videojuegos/assets/<?= htmlspecialchars($prod['imagen']) ?>" alt="Img">
                                <?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars($prod['categoria_nombre']) ?></td>
                            <td class="acciones">
                                <a href="editar_producto.php?id=<?= $prod['id'] ?>" class="edit">EDITAR</a>
                                <a href="agregar_producto.php?eliminar_id=<?= $prod['id'] ?>" class="delete" onclick="return confirm('¿Eliminar?')">BORRAR</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>