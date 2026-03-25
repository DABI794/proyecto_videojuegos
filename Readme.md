# 🎮 GameStore Bolivia — Tienda de Videojuegos

Plataforma de e-commerce especializada en videojuegos, consolas y accesorios gaming desarrollada para el mercado boliviano. El proyecto fue migrado y refactorizado desde una arquitectura PHP tradicional hacia **Laravel 12** con el objetivo de mejorar la seguridad, mantenibilidad y escalabilidad del sistema.

---

## 📋 Tabla de contenidos

- [Descripción general](#descripción-general)
- [Stack tecnológico](#stack-tecnológico)
- [Proceso de migración y refactorización](#proceso-de-migración-y-refactorización)
- [Mejoras implementadas](#mejoras-implementadas)
- [Estructura del proyecto](#estructura-del-proyecto)
- [Base de datos](#base-de-datos)
- [Instalación y configuración](#instalación-y-configuración)
- [Credenciales de prueba](#credenciales-de-prueba)
- [Funcionalidades](#funcionalidades)
- [Manejo de imágenes](#manejo-de-imágenes)
- [Seguridad](#seguridad)

---

## Descripción general

GameStore Bolivia es una tienda en línea que permite a los usuarios explorar un catálogo de videojuegos con filtros por categoría, gestionar un carrito de compras, realizar pagos mediante PayPal sandbox y consultar el historial de pedidos. Cuenta además con un panel de administración para la gestión completa de productos.

El sistema fue originalmente desarrollado en PHP 8.1 con consultas PDO directas, sesiones manuales y sin estructura de proyecto definida. Se tomó la decisión de migrar la totalidad del código a **Laravel 12** para resolver los problemas críticos de seguridad existentes y adoptar las convenciones modernas de desarrollo web.

---

## Stack tecnológico

| Capa | Tecnología |
|------|-----------|
| Backend | PHP 8.2 + Laravel 12 |
| Autenticación | Laravel Breeze |
| Base de datos | MySQL 8.0 + Eloquent ORM |
| Frontend | Blade Templates |
| CSS | Tailwind CSS + Bootstrap 5.3 |
| Build tool | Vite |
| Pagos | PayPal SDK (sandbox) |
| Servidor local | XAMPP / PHP Dev Server |

---

## Proceso de migración y refactorización

### Origen del proyecto

El proyecto original fue desarrollado con PHP 8.1 puro, sin framework, siguiendo un patrón MVC informal. La estructura consistía en archivos PHP individuales por funcionalidad (`index.php`, `productos.php`, `info.php`, `carrito/agregar_ajax.php`, etc.), consultas SQL escritas directamente en cada archivo y estilos CSS distribuidos en 9 archivos separados por página.

Se identificaron los siguientes problemas críticos en el sistema original:

- Las contraseñas del panel de administración se almacenaban en **texto plano** en la base de datos.
- Los formularios no contaban con **protección CSRF**, exponiéndolos a ataques de falsificación de solicitudes.
- Las credenciales de la base de datos se encontraban **hardcodeadas** directamente en el archivo de conexión.
- Las consultas SQL se construían mediante **concatenación de strings**, generando vulnerabilidades de inyección SQL.

### Decisiones de arquitectura

Se adoptó **Laravel 12** como framework base por las siguientes razones:

1. Resuelve automáticamente los problemas de seguridad identificados mediante sus mecanismos integrados (hashing de contraseñas, tokens CSRF, variables de entorno, Eloquent ORM).
2. Provee una estructura MVC clara y convencional que facilita el mantenimiento y la incorporación de nuevos desarrolladores al proyecto.
3. Incluye un sistema de migraciones que permite versionar la base de datos junto con el código fuente.
4. Ofrece Laravel Breeze como solución de autenticación completa lista para producción.

Se decidió mantener **Bootstrap 5.3** junto a **Tailwind CSS** para preservar la compatibilidad con los componentes JavaScript existentes (modales, dropdowns) mientras se adoptaba Tailwind como sistema principal de utilidades CSS.

---

## Mejoras implementadas

### 1. Base de datos

Se reescribió la totalidad del esquema de base de datos mediante **migrations de Laravel**, lo que permite versionar los cambios y revertirlos si fuera necesario.

Las tablas fueron renombradas y extendidas respecto al esquema original:

| Tabla original | Tabla nueva | Cambios realizados |
|---------------|-------------|-------------------|
| `usuarios` | `users` | Se agregó campo `role` y se integró con el sistema de autenticación de Breeze |
| `categorias` | `categories` | Se agregaron campos `slug` e `is_active` |
| `productos` | `products` | Se agregaron `slug`, `stock`, `is_featured` y soporte para **soft deletes** |
| `carrito` | `cart_items` | Se mantuvo la misma lógica con constraint `unique(user_id, product_id)` |
| `facturas` | `orders` | Se agregaron campos de estado, integración PayPal y soporte de notas |
| `factura_detalles` | `order_items` | Se incorporó un **snapshot** del nombre y precio del producto al momento de la compra |

La incorporación de **soft deletes** en la tabla `products` resolvió un problema crítico del sistema original: al eliminar un producto, las facturas históricas quedaban con referencias rotas. Con soft deletes, el producto se marca como eliminado pero permanece en la base de datos, preservando la integridad de los pedidos anteriores.

El **snapshot de precio y nombre** en `order_items` garantiza que los registros históricos de compras no se vean afectados por modificaciones futuras en los productos.

### 2. Seguridad

Los problemas críticos de seguridad fueron resueltos de la siguiente manera:

**Contraseñas en texto plano:** Se eliminó por completo esta vulnerabilidad. Laravel aplica automáticamente bcrypt a todas las contraseñas mediante el cast `hashed` definido en el modelo `User`. No es necesario llamar manualmente a ninguna función de hashing.

**Ausencia de protección CSRF:** Se resolvió al adoptar las vistas Blade de Laravel, donde la directiva `@csrf` genera y valida automáticamente tokens de seguridad en todos los formularios POST.

**Credenciales hardcodeadas:** Todas las credenciales de base de datos, claves de API y configuraciones sensibles fueron movidas al archivo `.env`, el cual está excluido del repositorio Git.

**Inyección SQL:** Se eliminó completamente al reemplazar las consultas SQL manuales por Eloquent ORM, que utiliza prepared statements internamente en todas las operaciones de base de datos.

Adicionalmente, se implementó el middleware `EnsureUserIsAdmin` que protege todas las rutas del panel de administración, verificando que el usuario autenticado tenga el rol `admin` antes de procesar cualquier solicitud.

### 3. Carga de datos y consultas

Se identificó un problema de rendimiento conocido como **N+1 queries** en el proyecto original. Cuando se listaban productos con su categoría, el sistema ejecutaba una consulta para obtener los productos y luego una consulta adicional por cada producto para obtener su categoría. Con 20 productos en pantalla, esto generaba 21 consultas a la base de datos.

Se resolvió mediante **eager loading** de Eloquent:

```php
// Antes: 1 + N consultas (N = cantidad de productos)
$productos = $conn->query("SELECT * FROM productos");
// Luego en el loop: SELECT * FROM categorias WHERE id = ?

// Después: siempre 2 consultas sin importar la cantidad
$products = Product::with('category')->active()->paginate(12);
```

Se definieron **scopes reutilizables** en los modelos para encapsular la lógica de filtrado:

```php
Product::active()       // solo productos activos
Product::featured()     // solo productos destacados
Product::inStock()      // solo productos con stock > 0
Product::byCategory()   // filtro por categoría (acepta id o slug)
```

Se implementó **paginación automática** en el catálogo de productos, reemplazando la carga completa de registros que realizaba el sistema original.

### 4. Estilos y diseño

El sistema de estilos fue refactorizado de 9 archivos CSS independientes a un único archivo `resources/css/app.css`, el cual centraliza las variables de diseño y las integra con Tailwind CSS.

Se preservaron exactamente los valores de la paleta de colores del diseño original:

```css
--primary-color:  #6366f1;   /* Indigo — color principal */
--bg-darkest:     #0f172a;   /* Fondo de página */
--bg-dark:        #1e293b;   /* Fondo de tarjetas */
--bg-medium:      #334155;   /* Superficies elevadas */
--text-primary:   #f1f5f9;   /* Texto principal */
--text-secondary: #94a3b8;   /* Texto secundario */
```

Bootstrap se mantiene para la grilla responsiva y los componentes JavaScript. Tailwind se utiliza para las clases utilitarias en los componentes Blade, eliminando la necesidad de archivos CSS por página.

Se creó el componente parcial `partials/product-card.blade.php` para encapsular la tarjeta de producto, reutilizada en la homepage, el catálogo y la página de detalle. Previamente, el HTML de la tarjeta se duplicaba en cada archivo PHP.

### 5. Autenticación

El sistema de autenticación fue completamente reescrito utilizando **Laravel Breeze**, reemplazando la implementación manual de sesiones PHP.

El sistema original requería copiar y pegar la verificación de sesión en cada archivo:

```php
// Antes: en cada archivo que requería autenticación
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header('Location: /proyecto_videojuegos/auth/login.php');
    exit();
}
```

El nuevo sistema utiliza middleware declarativo en las rutas:

```php
// Después: definido una sola vez en routes/web.php
Route::middleware('auth')->group(function () {
    Route::get('/carrito', [CartController::class, 'index']);
    // ...
});
```

Las funcionalidades de autenticación incorporadas incluyen login, registro, cierre de sesión, recuperación de contraseña y verificación de email.

### 6. Manejo de imágenes

El sistema de imágenes fue completamente rediseñado respecto al proyecto original. En el sistema anterior, el campo `imagen` de la tabla `productos` almacenaba URLs externas de servicios como Unsplash directamente en la base de datos, lo que generaba dependencia de terceros y no permitía subida real de archivos.

En el nuevo sistema, las imágenes **nunca se almacenan en la base de datos**. Lo que se guarda es únicamente la ruta relativa del archivo dentro del servidor:

```
Base de datos (campo image_path)
→ "products/mi-imagen-1234.jpg"       ← solo el texto de la ruta

Archivo físico en el servidor
→ storage/app/public/products/mi-imagen-1234.jpg

URL pública accesible en el navegador
→ http://localhost:8000/storage/products/mi-imagen-1234.jpg
```

El flujo completo al subir una imagen desde el panel de administración es el siguiente:

1. El administrador selecciona el archivo en el formulario de producto.
2. Laravel lo guarda físicamente en `storage/app/public/products/`.
3. En la base de datos se almacena únicamente la ruta relativa `products/nombre-archivo.jpg`.
4. Al mostrar el producto, el accessor `getImageUrlAttribute()` del modelo `Product` construye la URL pública completa.

Se implementó un **accessor con fallback** en el modelo `Product` que maneja automáticamente dos casos:

```php
public function getImageUrlAttribute(): string
{
    if ($this->image_path && Storage::disk('public')->exists($this->image_path)) {
        return Storage::disk('public')->url($this->image_path);
    }

    return asset('images/default.jpg'); // imagen por defecto
}
```

El comando `php artisan storage:link` es obligatorio en la instalación del proyecto, ya que crea el enlace simbólico `public/storage → storage/app/public` que permite al navegador acceder a los archivos físicos almacenados fuera de la carpeta pública.

### 7. Carrito de compras y AJAX

El archivo `carrito/agregar_ajax.php` del proyecto original fue reemplazado por el método `CartController@store`, el cual detecta automáticamente si la solicitud proviene de JavaScript o de un formulario tradicional y responde de forma apropiada en cada caso:

```php
// Un único método maneja ambos casos
if ($request->expectsJson()) {
    return response()->json(['exito' => true, 'cantidad' => $cartCount]);
}
return back()->with('success', 'Producto agregado al carrito.');
```

Se incorporó validación de stock antes de agregar productos al carrito y feedback visual en el botón durante el proceso de agregado.

---

## Estructura del proyecto

```
tienda-videojuegos/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── HomeController.php
│   │   │   ├── ProductController.php
│   │   │   ├── CartController.php
│   │   │   ├── OrderController.php
│   │   │   └── Admin/
│   │   │       ├── DashboardController.php
│   │   │       └── ProductController.php
│   │   ├── Middleware/
│   │   │   └── EnsureUserIsAdmin.php
│   │   └── Requests/
│   │       └── Admin/
│   │           ├── StoreProductRequest.php
│   │           └── UpdateProductRequest.php
│   ├── Models/
│   │   ├── User.php
│   │   ├── Category.php
│   │   ├── Product.php
│   │   ├── CartItem.php
│   │   ├── Order.php
│   │   └── OrderItem.php
│   └── View/
│       └── Components/
│           └── GuestLayout.php
├── database/
│   ├── migrations/          # 6 migrations del proyecto
│   ├── seeders/             # Datos de prueba
│   └── factories/           # Factories para testing
├── resources/
│   ├── css/
│   │   └── app.css          # Tailwind + variables del sistema de diseño
│   └── views/
│       ├── layouts/
│       │   ├── app.blade.php
│       │   └── guest.blade.php
│       ├── partials/
│       │   ├── navbar.blade.php
│       │   ├── footer.blade.php
│       │   ├── product-card.blade.php
│       │   └── pagination.blade.php
│       ├── home.blade.php
│       ├── products/
│       ├── cart/
│       ├── orders/
│       ├── admin/
│       └── auth/
└── routes/
    ├── web.php
    └── api.php
```

---

## Base de datos

### Diagrama de relaciones

```
categories          products
    id ──────────── category_id
    name            id
    slug            name
    is_active       slug
                    price
                    stock
                    image_path
                    is_featured
                    deleted_at (soft delete)

users               cart_items
    id ──────────── user_id
    name            id
    email           product_id ──── products.id
    password        quantity
    role
    phone           orders
    │               id
    └────────────── user_id
                    subtotal
                    total
                    status
                    paypal_order_id
                    paid_at

                    order_items
                    id
                    order_id ────── orders.id
                    product_id ──── products.id (nullable)
                    product_name   (snapshot)
                    unit_price     (snapshot)
                    quantity
                    subtotal
```

### Estados de una orden

```
pending → paid → processing → shipped → delivered
    └──────────────────────────────────→ cancelled
```

---

## Instalación y configuración

Para una guía detallada de instalación en Ubuntu Linux, consulta el archivo [INSTALLATION.md](file:///home/cesar/Documentos/proyecto_videojuegos/INSTALLATION.md).


- PHP 8.2+
- Composer 2.x
- MySQL 8.0+
- Node.js 20+

### Pasos

```bash
# 1. Clonar el repositorio
git clone https://github.com/tu-usuario/tienda-videojuegos.git
cd tienda-videojuegos

# 2. Instalar dependencias PHP
composer install

# 3. Instalar dependencias Node
npm install

# 4. Copiar el archivo de entorno
cp .env.example .env

# 5. Generar la clave de la aplicación
php artisan key:generate

# 6. Configurar la base de datos en .env
# DB_DATABASE=proyecto_videojuegos
# DB_USERNAME=root
# DB_PASSWORD=

# 7. Crear el symlink de storage
php artisan storage:link

# 8. Ejecutar las migrations
php artisan migrate

# 9. Poblar con datos de prueba
php artisan db:seed

# 10. Compilar assets
npm run dev

# 11. Levantar el servidor
php artisan serve
```

La aplicación estará disponible en `http://localhost:8000`.

---

## Credenciales de prueba

| Rol | Email | Contraseña |
|-----|-------|-----------|
| Administrador | admin@videojuegos.bo | Admin@12345! |
| Cliente | juan@example.com | password |
| Cliente | maria@example.com | password |
| Cliente | carlos@example.com | password |

> **Nota:** Estas credenciales son únicamente para entornos de desarrollo. Deben eliminarse antes de un despliegue en producción.

---

## Funcionalidades

### Área pública
- Catálogo de productos con filtros por categoría, búsqueda por nombre y ordenamiento
- Página de detalle de producto con productos relacionados
- Registro e inicio de sesión
- Recuperación de contraseña por email

### Área de cliente (requiere autenticación)
- Carrito de compras con actualización en tiempo real mediante AJAX
- Proceso de checkout con notas opcionales
- Integración con PayPal SDK (modo sandbox)
- Historial de pedidos con detalle de cada orden
- Cancelación de pedidos pendientes

### Panel de administración (requiere rol admin)
- Dashboard con estadísticas: productos activos, clientes, pedidos e ingresos
- Alertas de stock bajo y pedidos pendientes
- CRUD completo de productos con subida de imágenes
- Control de estado de productos (activo/inactivo, destacado)

---

## Seguridad

- Contraseñas hasheadas con bcrypt mediante el cast `hashed` de Eloquent
- Protección CSRF automática en todos los formularios mediante la directiva `@csrf` de Blade
- Variables de entorno en `.env` excluidas del repositorio Git
- Prevención de inyección SQL mediante Eloquent ORM y prepared statements
- Middleware de autorización por rol (`EnsureUserIsAdmin`) en todas las rutas del panel de administración
- Validación de datos mediante Form Requests en todas las operaciones de escritura
- Soft deletes en productos para preservar integridad referencial en el historial de órdenes

---

## Variables de entorno relevantes

```env
APP_NAME="Tienda Videojuegos"
APP_LOCALE=es
APP_TIMEZONE=America/La_Paz

DB_CONNECTION=mysql
DB_DATABASE=proyecto_videojuegos

PAYPAL_CLIENT_ID=          # Client ID de PayPal sandbox
PAYPAL_CLIENT_SECRET=      # Secret de PayPal sandbox
PAYPAL_MODE=sandbox        # sandbox | live
```
---
## Cambio sugerido
Agrega una sección de "Requisitos de Instalación" donde pongas lo que aprendimos hoy (activar extensión zip, ejecutar composer install, php artisan key:generate).