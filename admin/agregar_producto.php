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
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Panel Admin - Productos</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Space+Grotesk:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../stilos/base.css" />
    <link rel="stylesheet" href="../stilos/dashboard.css" />
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
                            <td style="color: var(--color-primary-light); font-weight: 600;">BS <?= number_format($prod['precio'], 2) ?></td>
                            <td>
                                <?php if (filter_var($prod['imagen'], FILTER_VALIDATE_URL)): ?>
                                    <img src="<?= htmlspecialchars($prod['imagen']) ?>" alt="Img">
                                <?php else: ?>
                                    <img src="/proyecto_videojuegos/assets/<?= htmlspecialchars($prod['imagen']) ?>" alt="Img">
                                <?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars($prod['categoria_nombre']) ?></td>
                            <td class="acciones">
                                <a href="editar_producto.php?id=<?= $prod['id'] ?>" class="edit">Editar</a>
                                <a href="#" class="delete" onclick="openDeleteModal(<?= $prod['id'] ?>, '<?= htmlspecialchars($prod['nombre'], ENT_QUOTES) ?>'); return false;">Borrar</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal de confirmación de borrado -->
    <div class="modal-overlay" id="deleteModal">
        <div class="modal">
            <div class="modal-icon">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
            </div>
            <h3>¿Eliminar producto?</h3>
            <p id="deleteMessage">¿Estás seguro de que deseas eliminar este producto? Esta acción no se puede deshacer.</p>
            <div class="modal-actions">
                <button type="button" class="modal-btn-cancel" onclick="closeDeleteModal()">Cancelar</button>
                <button type="button" class="modal-btn-delete" id="confirmDeleteBtn">Eliminar</button>
            </div>
        </div>
    </div>

    <script>
        let productIdToDelete = null;

        function mostrarSeccion(id) {
            document.getElementById("formulario").style.display = (id === 'formulario') ? 'block' : 'none';
            document.getElementById("tabla").style.display = (id === 'tabla') ? 'block' : 'none';
        }

        function openDeleteModal(productId, productName) {
            productIdToDelete = productId;
            document.getElementById('deleteMessage').textContent = 
                `¿Estás seguro de que deseas eliminar "${productName}"? Esta acción no se puede deshacer.`;
            document.getElementById('deleteModal').classList.add('active');
        }

        function closeDeleteModal() {
            document.getElementById('deleteModal').classList.remove('active');
            productIdToDelete = null;
        }

        document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
            if (productIdToDelete) {
                window.location.href = `agregar_producto.php?eliminar_id=${productIdToDelete}`;
            }
        });

        // Cerrar modal al hacer clic fuera de él
        document.getElementById('deleteModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeDeleteModal();
            }
        });

        // Cerrar modal con tecla Escape
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeDeleteModal();
            }
        });
    </script>
</body>
</html>