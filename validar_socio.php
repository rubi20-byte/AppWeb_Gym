<?php
include 'config.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (isset($_SESSION['id_socio'])) {
    $id_s = $_SESSION['id_socio'];

    // Buscamos al socio y verificamos que NO esté eliminado
    $check = $conexion->query("SELECT id_socio, eliminado, estado FROM socios WHERE id_socio = '$id_s'");
    $socio_actual = $check->fetch_assoc();

    // Si no existe el registro OR el campo eliminado es 1 OR está bloqueado
    if (!$socio_actual || $socio_actual['eliminado'] == 1 || $socio_actual['estado'] == 'bloqueado') {
        
        // Destruimos la sesión para que no pueda volver a entrar
        session_destroy();
        
        // Lo mandamos al login con un mensaje de error
        echo "<script>
                alert('Tu cuenta ya no está disponible o ha sido inhabilitada.');
                window.location='login.php';
              </script>";
        exit();
    }
} else {
    // Si ni siquiera hay sesión, al login de una vez
    header("Location: login.php");
    exit();
}
?>