<?php
session_start();

require_once '../db.php'; 

$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $correo_input = $_POST['correo'] ?? '';
    $clave_input  = $_POST['contraseña'] ?? '';

    if (!empty($correo_input) && !empty($clave_input)) {
        try {
            $stmt = $conn->prepare("SELECT * FROM usuarios WHERE correo = ? AND rol = 'admin'");
            $stmt->execute([$correo_input]);
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($usuario && $usuario['contraseña'] === $clave_input) {
                session_regenerate_id(true);
                $_SESSION['admin'] = true;
                $_SESSION['admin_id'] = $usuario['id'];
                $_SESSION['usuario_nombre'] = $usuario['nombre'];

                header("Location: agregar_producto.php");
                exit;
            } else {
                $error = "Acceso denegado. Verifica tus datos.";
            }
        } catch (PDOException $e) {
            $error = "Error en la consulta: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Administrador | Game Panel</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/proyecto_videojuegos/stilos/base.css">
    <link rel="stylesheet" href="/proyecto_videojuegos/stilos/admin-login.css">
</head>
<body>

    <div class="form-container">
        <h2>Admin Core</h2>

        <?php if ($error): ?>
            <div class="error-msg"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="input-group">
                <label>Terminal de acceso</label>
                <input type="email" name="correo" placeholder="ID de Correo" required autofocus>
            </div>
            
            <div class="input-group">
                <label>Clave de encriptación</label>
                <input type="password" name="contraseña" placeholder="********" required>
            </div>

            <button type="submit">Autenticar</button>
        </form>

        <div class="footer-text">
            Sistemas de Seguridad Activos
        </div>
    </div>

</body>
</html>