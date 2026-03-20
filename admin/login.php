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
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700&family=Roboto:wght@300;400&display=swap" rel="stylesheet">
    <style>
        :root {
            --neon-cyan: #0ff;
            --neon-purple: #bc13fe;
            --error-red: #ff4444;
            --dark-bg: #050505;
        }

        body {
            background: var(--dark-bg) url('../assets/bg_gaming.jpg') no-repeat center center fixed;
            background-size: cover;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            font-family: 'Roboto', sans-serif;
            overflow: hidden;
        }

        /* Capa de superposición para oscurecer el fondo */
        body::before {
            content: "";
            position: absolute;
            top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(0, 0, 0, 0.7);
            z-index: 1;
        }

        .form-container {
            position: relative;
            z-index: 2;
            background: rgba(15, 15, 15, 0.95);
            padding: 50px 40px;
            border: 2px solid var(--neon-cyan);
            box-shadow: 0 0 25px rgba(0, 255, 255, 0.3);
            border-radius: 20px;
            width: 100%;
            max-width: 360px;
            text-align: center;
            transition: transform 0.3s ease;
        }

        .form-container:hover {
            transform: scale(1.02);
            box-shadow: 0 0 35px rgba(0, 255, 255, 0.5);
        }

        h2 { 
            font-family: 'Orbitron', sans-serif;
            color: var(--neon-cyan); 
            margin-bottom: 30px; 
            letter-spacing: 3px;
            text-transform: uppercase;
            text-shadow: 0 0 10px var(--neon-cyan);
        }

        .input-group {
            margin-bottom: 20px;
            text-align: left;
        }

        .input-group label {
            display: block;
            color: #aaa;
            margin-bottom: 5px;
            font-size: 0.9rem;
            text-transform: uppercase;
        }

        input {
            width: 100%;
            padding: 14px;
            background: #111;
            border: 1px solid #333;
            color: #fff;
            border-radius: 8px;
            box-sizing: border-box;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        input:focus {
            border-color: var(--neon-cyan);
            background: #151515;
            outline: none;
            box-shadow: 0 0 8px rgba(0, 255, 255, 0.2);
        }

        button {
            width: 100%;
            padding: 15px;
            margin-top: 10px;
            background: var(--neon-cyan);
            color: #000;
            font-family: 'Orbitron', sans-serif;
            font-weight: bold;
            font-size: 1.1rem;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
            text-transform: uppercase;
        }

        button:hover { 
            background: #fff; 
            box-shadow: 0 0 20px #fff;
            transform: translateY(-2px);
        }

        button:active {
            transform: translateY(0);
        }

        .error-msg { 
            background: rgba(255, 68, 68, 0.1);
            color: var(--error-red); 
            font-size: 0.85rem; 
            margin-bottom: 20px;
            padding: 10px;
            border: 1px solid var(--error-red);
            border-radius: 5px;
        }

        .footer-text {
            margin-top: 25px;
            color: #555;
            font-size: 0.8rem;
            text-transform: uppercase;
        }
    </style>
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