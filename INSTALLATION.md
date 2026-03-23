# Instalación del Proyecto (Ubuntu Linux)

Este documento detalla los pasos necesarios para configurar el entorno de desarrollo en Ubuntu Linux para este proyecto Laravel 12.

## Requisitos Previos

Asegúrate de tener instalados los siguientes componentes:

### 1. PHP 8.2+ y Extensiones
Instala PHP y las extensiones necesarias:
```bash
sudo apt update
sudo apt install php8.2 php8.2-cli php8.2-fpm php8.2-mysql php8.2-curl php8.2-xml php8.2-mbstring php8.2-zip php8.2-bcmath php8.2-intl php8.2-readline php8.2-sqlite3 php8.2-gd php8.2-redis
```

### 2. Composer
Si no tienes Composer, instálalo globalmente:
```bash
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
```

### 3. Node.js y npm
Se recomienda la versión LTS:
```bash
curl -fsSL https://deb.nodesource.com/setup_lts.x | sudo -E bash -
sudo apt install -y nodejs
```

### 4. MySQL Server
```bash
sudo apt install mysql-server
```

## Pasos de Instalación

1. **Clonar el repositorio** (ya realizado).
2. **Instalar dependencias de PHP**:
   ```bash
   composer install
   ```
3. **Instalar dependencias de Node**:
   ```bash
   npm install
   ```
4. **Configurar el entorno**:
   - El archivo `.env` ya ha sido organizado. Asegúrate de configurar las credenciales de tu base de datos local en `.env`.
5. **Generar la clave de la aplicación**:
   ```bash
   php artisan key:generate
   ```
6. **Ejecutar migraciones**:
   ```bash
   php artisan migrate
   ```
7. **Compilar assets**:
   ```bash
   npm run dev
   ```
8. **Iniciar el servidor**:
   ```bash
   php artisan serve
   ```
