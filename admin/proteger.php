<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verifica si la sesión existe. 
// IMPORTANTE: Usa el mismo nombre que definas en el login (ej. 'admin_id')
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}
?>