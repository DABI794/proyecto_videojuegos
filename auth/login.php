<?php
session_start();
require_once '../db.php';

// Inicializar variable para evitar warning
$mostrarRegistro = false;

// Función para limpiar datos y evitar XSS
function limpiar($dato) {
    return htmlspecialchars(trim($dato), ENT_QUOTES, 'UTF-8');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['registro'])) {
        // Limpiar datos de entrada
        $nombre = limpiar($_POST['nombre'] ?? '');
        $correo = filter_var($_POST['correo'] ?? '', FILTER_VALIDATE_EMAIL);
        $contraseña = $_POST['contraseña'] ?? '';
        $confirmar = $_POST['confirmar_contraseña'] ?? '';

        if (!$correo) {
            $error = "Correo inválido.";
            $mostrarRegistro = true;
        } elseif ($contraseña !== $confirmar) {
            $error = "Las contraseñas no coinciden.";
            $mostrarRegistro = true;
        } elseif (strlen($contraseña) < 6) {
            $error = "La contraseña debe tener al menos 6 caracteres.";
            $mostrarRegistro = true;
        } else {
            // Validar si el correo ya existe
            $stmt = $conn->prepare("SELECT id FROM usuarios WHERE correo = ?");
            $stmt->execute([$correo]);
            if ($stmt->rowCount() > 0) {
                $error = "El correo ya está registrado.";
                $mostrarRegistro = true;
            } else {
                $hash = password_hash($contraseña, PASSWORD_DEFAULT);
                $stmt = $conn->prepare("INSERT INTO usuarios (nombre, correo, contraseña, rol) VALUES (?, ?, ?, 'cliente')");
                if ($stmt->execute([$nombre, $correo, $hash])) {
                    $exito = "Registro exitoso. Inicia sesión.";
                    $mostrarRegistro = false;
                } else {
                    $error = "Error al registrar usuario.";
                    $mostrarRegistro = true;
                }
            }
        }
    } elseif (isset($_POST['login'])) {
        $correo = filter_var($_POST['correo'] ?? '', FILTER_VALIDATE_EMAIL);
        $contraseña = $_POST['contraseña'] ?? '';

        if (!$correo) {
            $error = "Correo inválido.";
        } else {
            $stmt = $conn->prepare("SELECT * FROM usuarios WHERE correo = ?");
            $stmt->execute([$correo]);
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($usuario && password_verify($contraseña, $usuario['contraseña'])) {
                // Regenerar sesión para evitar fijación de sesión
                session_regenerate_id(true);

                $_SESSION['usuario_id'] = $usuario['id'];
                $_SESSION['usuario_nombre'] = $usuario['nombre'];
                $_SESSION['usuario_rol'] = $usuario['rol'];
                header("Location: /proyecto_videojuegos/index.php");
                exit;
            } else {
                $error = "Correo o contraseña incorrectos.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Login / Registro</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/proyecto_videojuegos/stilos/base.css">
    <link rel="stylesheet" href="/proyecto_videojuegos/stilos/style.css">
    <link rel="stylesheet" href="/proyecto_videojuegos/stilos/login.css">
</head>
<body>
    <?php include '../includes/navbar.php'; ?>

    <div class="form-container" id="loginForm" style="<?= $mostrarRegistro ? 'display:none;' : '' ?>">
        <h2>Iniciar Sesión</h2>
        <?php if (isset($error) && !$mostrarRegistro) echo "<p class='error'>" . limpiar($error) . "</p>"; ?>
        <?php if (isset($exito)) echo "<p class='exito'>" . limpiar($exito) . "</p>"; ?>
        <form method="POST" autocomplete="off">
            <input type="email" name="correo" placeholder="Correo electrónico" required />
            <input type="password" name="contraseña" placeholder="Contraseña" required />
            <button type="submit" name="login">Entrar</button>
        </form>
        <div class="toggle-link">
            ¿No tienes cuenta? <a href="#" onclick="mostrarRegistro()">Regístrate aquí</a>
        </div>
    </div>

    <div class="form-container" id="registroForm" style="<?= $mostrarRegistro ? '' : 'display:none;' ?>">
        <h2>Registro</h2>
        <?php if (isset($error) && $mostrarRegistro) echo "<p class='error'>" . limpiar($error) . "</p>"; ?>
        <form method="POST" autocomplete="off">
            <input type="text" name="nombre" placeholder="Nombre" required />
            <input type="email" name="correo" placeholder="Correo electrónico" required />
            <input type="password" name="contraseña" placeholder="Contraseña" required />
            <input type="password" name="confirmar_contraseña" placeholder="Confirmar Contraseña" required />
            <button type="submit" name="registro">Registrarse</button>
        </form>
        <div class="toggle-link">
            ¿Ya tienes cuenta? <a href="#" onclick="mostrarLogin()">Inicia sesión aquí</a>
        </div>
    </div>

    <script>
        function mostrarRegistro() {
            document.getElementById('loginForm').style.display = 'none';
            document.getElementById('registroForm').style.display = 'block';
        }
        function mostrarLogin() {
            document.getElementById('loginForm').style.display = 'block';
            document.getElementById('registroForm').style.display = 'none';
        }
    </script>
</body>
</html>
