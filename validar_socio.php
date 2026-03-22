<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Si no hay sesión de SOCIO, lo mandamos al login.
// IMPORTANTE: No usamos id_usuario aquí, usamos el ROL 'socio'.
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'socio') {
    header("Location: login.php");
    exit();
}
?>