<?php
session_start();
require_once 'db.php';

// Cargar categorías para el filtro
$stmtCat = $conn->query("SELECT * FROM categorias ORDER BY nombre");
$categorias = $stmtCat->fetchAll(PDO::FETCH_ASSOC);

// Preparar consulta para productos con filtros
$condiciones = [];
$params = [];

if (!empty($_GET['busqueda'])) {
    $condiciones[] = "nombre LIKE :busqueda";
    $params[':busqueda'] = "%" . $_GET['busqueda'] . "%";
}

if (!empty($_GET['categoria'])) {
    $condiciones[] = "categoria_id = :categoria";
    $params[':categoria'] = $_GET['categoria'];
}

$where = $condiciones ? "WHERE " . implode(" AND ", $condiciones) : "";

$sql = "SELECT * FROM productos $where ORDER BY nombre";
$stmt = $conn->prepare($sql);
$stmt->execute($params);
$productos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Productos</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/proyecto_videojuegos/stilos/base.css" />
    <link rel="stylesheet" href="/proyecto_videojuegos/stilos/style.css" />
    <link rel="stylesheet" href="/proyecto_videojuegos/stilos/productos.css" />
</head>

<body class="fondo-claro">

    <?php include __DIR__ . '/includes/navbar.php'; ?>

    <div class="container mt-4">
        <!-- Enhanced Catalog Header -->
        <div class="catalog-header">
            <h1>Catálogo de Videojuegos</h1>
            <p class="subtitle">Descubre los mejores títulos para todas las plataformas</p>
            <span class="product-count">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                </svg>
                <?= count($productos) ?> producto<?= count($productos) !== 1 ? 's' : '' ?> disponible<?= count($productos) !== 1 ? 's' : '' ?>
            </span>
        </div>

        <!-- Enhanced Search Filters -->
        <form method="GET" class="search-filters">
            <input type="text" name="busqueda" class="form-control" placeholder="Buscar juego..." value="<?= htmlspecialchars($_GET['busqueda'] ?? '') ?>" />
            <select name="categoria" class="form-select">
                <option value="">Todas las categorías</option>
                <?php foreach ($categorias as $cat):
                    $selected = (isset($_GET['categoria']) && $_GET['categoria'] == $cat['id']) ? 'selected' : '';
                ?>
                    <option value="<?= $cat['id'] ?>" <?= $selected ?>><?= htmlspecialchars($cat['nombre']) ?></option>
                <?php endforeach; ?>
            </select>
            <button type="submit" class="btn-search">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                Buscar
            </button>
            <?php if (!empty($_GET['busqueda']) || !empty($_GET['categoria'])): ?>
                <a href="productos.php" class="btn-clear">Limpiar filtros</a>
            <?php endif; ?>
        </form>

        <?php if (empty($productos)): ?>
            <!-- Empty State -->
            <div class="empty-state">
                <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="color: #475569; margin-bottom: 20px;">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <h3>No se encontraron productos</h3>
                <p>Intenta con otros términos de búsqueda o categorías</p>
                <a href="productos.php" class="btn btn-dark" style="margin-top: 16px;">Ver todos los productos</a>
            </div>
        <?php else: ?>
            <div class="productos-container">
                <?php foreach ($productos as $producto): ?>
                    <div class="card">
                        <img
                            src="<?= htmlspecialchars($producto['imagen'] ?? '') ?>"
                            class="card-img-top"
                            alt="<?= htmlspecialchars($producto['nombre']) ?>"
                            onerror="this.onerror=null;this.src='/proyecto_videojuegos/assets/default.jpg';" />
                        <div class="card-body">
                            <h5 class="card-title"><?= htmlspecialchars($producto['nombre']) ?></h5>
                            <p class="card-text">BS <?= number_format($producto['precio'], 2) ?></p>
                            <div class="card-actions">
                                <button class="btn btn-dark agregar-carrito" data-producto-id="<?= $producto['id'] ?>">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                                    </svg>
                                    Añadir
                                </button>
                                <a href="info.php?id=<?= $producto['id'] ?>" class="btn btn-outline-dark">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    Info
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <script>
    document.addEventListener("DOMContentLoaded", () => {
        const botones = document.querySelectorAll(".agregar-carrito");

        botones.forEach(boton => {
            boton.addEventListener("click", () => {
                const productoId = boton.getAttribute("data-producto-id");

                fetch("/proyecto_videojuegos/carrito/agregar_ajax.php", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/x-www-form-urlencoded"
                    },
                    body: "producto_id=" + encodeURIComponent(productoId)
                })
                .then(response => response.json())
                .then(data => {
                    if (data.exito) {
                        boton.textContent = "✅ Agregado";
                        boton.disabled = true;

                        // Actualizar contador del carrito
                        actualizarContadorCarrito();

                        setTimeout(() => {
                            boton.textContent = "añadir a carrito";
                            boton.disabled = false;
                        }, 2000);
                    } else {
                        alert("Error: " + data.mensaje);
                    }
                })
                .catch(error => {
                    alert("Hubo un error al añadir al carrito");
                    console.error(error);
                });
            });
        });

        function actualizarContadorCarrito() {
            fetch("/proyecto_videojuegos/carrito/obtener_total.php")
                .then(response => response.json())
                .then(data => {
                    const contador = document.getElementById("carrito-contador");
                    if (contador) {
                        contador.textContent = data.total;
                    }
                });
        }
    });
</script>

</body>

</html>