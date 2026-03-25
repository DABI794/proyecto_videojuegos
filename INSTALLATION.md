# Instalación del Proyecto

Este documento detalla los pasos necesarios para configurar el entorno de desarrollo para este proyecto Laravel 12.

## Requisitos Previos

### Windows (XAMPP)
- **PHP 8.2+** (incluido en XAMPP)
- **MySQL 8.0+** (incluido en XAMPP)
- **Composer** - [Descargar aquí](https://getcomposer.org/download/)
- **Node.js 18+** - [Descargar aquí](https://nodejs.org/)

### Linux (Ubuntu)
Asegúrate de tener instalados los siguientes componentes:

#### 1. PHP 8.2+ y Extensiones
Instala PHP y las extensiones necesarias:
```bash
sudo apt update
sudo apt install php8.2 php8.2-cli php8.2-fpm php8.2-mysql php8.2-curl php8.2-xml php8.2-mbstring php8.2-zip php8.2-bcmath php8.2-intl php8.2-readline php8.2-sqlite3 php8.2-gd php8.2-redis
```

#### 2. Composer
Si no tienes Composer, instálalo globalmente:
```bash
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
```

#### 3. Node.js y npm
Se recomienda la versión LTS:
```bash
curl -fsSL https://deb.nodesource.com/setup_lts.x | sudo -E bash -
sudo apt install -y nodejs
```

#### 4. MySQL Server
```bash
sudo apt install mysql-server
```

## Pasos de Instalación

### 1. Clonar el repositorio
```bash
git clone <url-del-repositorio>
cd tienda-videojuegos
```

### 2. Instalar dependencias
```bash
composer install
npm install
```

### 3. Configurar el entorno
```bash
# Copiar el archivo de ejemplo
cp .env.example .env

# Generar la clave de la aplicación
php artisan key:generate
```

### 4. Configurar la base de datos
Edita el archivo `.env` con tus credenciales de MySQL:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=proyecto_videojuegos
DB_USERNAME=root
DB_PASSWORD=tu_password
```

Luego crea la base de datos:
```bash
# MySQL
mysql -u root -p
CREATE DATABASE proyecto_videojuegos;
exit;
```

### 5. Ejecutar migraciones y seeders
```bash
php artisan migrate --seed
```

### 6. Crear enlace simbólico para storage
```bash
php artisan storage:link
```

### 7. Configurar PayPal (Opcional)
Si vas a trabajar con pagos, obtén tus credenciales de PayPal Sandbox en [PayPal Developer](https://developer.paypal.com/) y actualiza en `.env`:
```env
PAYPAL_CLIENT_ID=tu_client_id
PAYPAL_CLIENT_SECRET=tu_secret
PAYPAL_MODE=sandbox
```

### 8. Iniciar el servidor de desarrollo
```bash
# Opción 1: Servidor Laravel (Recomendado)
php artisan serve

# Opción 2: Usar el script de desarrollo que inicia todo
composer dev
```

El proyecto estará disponible en: `http://localhost:8000`

## Credenciales de Prueba

Después de ejecutar `php artisan migrate --seed`, puedes usar:

- **Admin:**
  - Email: `admin@gamestore.com`
  - Password: `password`

- **Usuario:**
  - Email: `user@gamestore.com`
  - Password: `password`

## Solución de Problemas

### Las imágenes no se muestran
```bash
# Asegúrate de que el enlace simbólico existe
php artisan storage:link

# Verifica que APP_URL en .env sea correcto
# Para desarrollo local: APP_URL=http://localhost:8000
```

### Error: "Class not found"
```bash
composer dump-autoload
php artisan optimize:clear
```

### Error de permisos en storage/
```bash
# Linux/Mac
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache

# Windows (ejecutar como administrador)
icacls storage /grant "IIS_IUSRS:(OI)(CI)F" /T
```

## Comandos Útiles

```bash
# Limpiar cachés
php artisan optimize:clear

# Formatear código PHP
./vendor/bin/pint

# Ejecutar tests
php artisan test

# Compilar assets para producción
npm run build
```
