<?php
// Calcular el total del carrito antes de imprimirlo
$carrito_total = 0;
if (isset($_SESSION['usuario_id'])) {
  $stmt = $conn->prepare("SELECT SUM(cantidad) AS total FROM carrito WHERE usuario_id = ?");
  $stmt->execute([$_SESSION['usuario_id']]);
  $carrito_total = $stmt->fetchColumn() ?? 0;
}
?>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

<style>
  /* Override Bootstrap para mantener tema oscuro */
  .navbar-custom {
    background: var(--bg-secondary, #1e293b) !important;
    border-bottom: 1px solid var(--border-color, #334155);
    padding: 12px 0;
  }
  
  .navbar-custom .navbar-brand {
    color: var(--text-primary, #f1f5f9) !important;
    font-weight: 700;
    font-size: 1.25rem;
    display: flex;
    align-items: center;
    gap: 8px;
  }
  
  .navbar-custom .navbar-brand:hover {
    color: var(--color-primary, #6366f1) !important;
  }
  
  .navbar-custom .navbar-brand i {
    color: var(--color-primary, #6366f1);
  }
  
  .navbar-custom .nav-link {
    color: var(--text-secondary, #94a3b8) !important;
    font-weight: 500;
    padding: 8px 16px !important;
    border-radius: 8px;
    transition: all 0.2s ease;
  }
  
  .navbar-custom .nav-link:hover {
    color: var(--text-primary, #f1f5f9) !important;
    background: var(--bg-tertiary, #334155);
  }
  
  .navbar-custom .navbar-toggler {
    border-color: var(--border-color, #334155);
    padding: 4px 8px;
  }
  
  .navbar-custom .navbar-toggler-icon {
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='rgba(148, 163, 184, 1)' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e");
  }
  
  .navbar-custom .badge {
    background: var(--color-primary, #6366f1) !important;
    color: white !important;
    font-weight: 600;
    padding: 4px 8px;
    border-radius: 20px;
  }
  
  .navbar-custom .user-greeting {
    color: var(--text-secondary, #94a3b8);
    font-size: 14px;
  }
  
  .navbar-custom .user-greeting strong {
    color: var(--color-primary, #6366f1);
  }
  
  .navbar-custom .btn-admin {
    background: var(--color-primary, #6366f1);
    color: white !important;
    padding: 6px 14px !important;
    border-radius: 8px;
    font-size: 13px;
    font-weight: 600;
  }
  
  .navbar-custom .btn-admin:hover {
    background: var(--color-primary-hover, #4f46e5);
  }
  
  .navbar-custom .btn-logout {
    color: var(--error, #ef4444) !important;
  }
  
  .navbar-custom .btn-logout:hover {
    background: rgba(239, 68, 68, 0.1);
    color: var(--error-light, #f87171) !important;
  }
  
  /* Dropdown en mobile */
  .navbar-custom .navbar-collapse {
    background: var(--bg-secondary, #1e293b);
  }
  
  @media (max-width: 991px) {
    .navbar-custom .navbar-collapse {
      padding: 16px 0;
      border-top: 1px solid var(--border-color, #334155);
      margin-top: 12px;
    }
    
    .navbar-custom .navbar-nav {
      gap: 4px;
    }
    
    .navbar-custom .d-flex {
      flex-direction: column;
      gap: 8px;
      padding-top: 12px;
      border-top: 1px solid var(--border-color, #334155);
      margin-top: 12px;
    }
  }
</style>

<nav class="navbar navbar-expand-lg navbar-custom">
  <div class="container-fluid px-4">
    <a class="navbar-brand" href="/proyecto_videojuegos/index.php">
      <i class="bi bi-controller"></i> Pulsa Start
    </a>

    <!-- Botón hamburguesa -->
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#menuNav" aria-controls="menuNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="menuNav">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item">
          <a class="nav-link" href="/proyecto_videojuegos/index.php">
            <i class="bi bi-house-door me-1"></i> Inicio
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="/proyecto_videojuegos/productos.php">
            <i class="bi bi-grid me-1"></i> Productos
          </a>
        </li>
      </ul>

      <div class="d-flex align-items-center gap-2">
        <a class="nav-link" href="/proyecto_videojuegos/carrito/ver_carrito.php">
          <i class="bi bi-cart3"></i> Carrito 
          <span id="carrito-contador" class="badge"><?= $carrito_total ?></span>
        </a>

        <?php if (isset($_SESSION['usuario_nombre'])): ?>
          <span class="user-greeting d-none d-lg-inline">
            Hola, <strong><?= htmlspecialchars($_SESSION['usuario_nombre']) ?></strong>
          </span>

          <?php if (isset($_SESSION['usuario_rol']) && $_SESSION['usuario_rol'] === 'admin'): ?>
            <a class="nav-link btn-admin" href="/proyecto_videojuegos/admin/agregar_producto.php">
              <i class="bi bi-gear me-1"></i> Admin
            </a>
          <?php endif; ?>

          <a class="nav-link btn-logout" href="/proyecto_videojuegos/logout.php">
            <i class="bi bi-box-arrow-right me-1"></i> Salir
          </a>
        <?php else: ?>
          <a class="nav-link" href="/proyecto_videojuegos/auth/login.php">
            <i class="bi bi-person-circle me-1"></i> Login
          </a>
        <?php endif; ?>
      </div>
    </div>
  </div>
</nav>

<script>
  function actualizarContadorCarrito() {
    fetch('/proyecto_videojuegos/carrito/contar_carrito.php')
      .then(response => response.json())
      .then(data => {
        document.getElementById('carrito-contador').textContent = data.total;
      })
      .catch(err => console.log('Error actualizando carrito:', err));
  }

  document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.agregar-carrito').forEach(function(btn) {
      btn.addEventListener('click', function() {
        const productoId = this.getAttribute('data-producto-id');

        fetch('/proyecto_videojuegos/carrito/agregar_carrito.php', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
          },
          body: 'producto_id=' + encodeURIComponent(productoId)
        })
        .then(response => response.json())
        .then(data => {
          if (data.exito) {
            actualizarContadorCarrito();
          } else {
            alert(data.mensaje || 'Hubo un error al agregar al carrito.');
          }
        });
      });
    });

    // Actualizar contador al cargar la página
    actualizarContadorCarrito();
  });
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
