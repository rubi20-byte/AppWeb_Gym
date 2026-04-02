<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Si no es entrenador, lo mandamos al login
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'entrenador') {
    header("Location: login.php");
    exit();
}

// Guardamos el ID del profe en una variable fácil de usar
$id_profe_sesion = $_SESSION['id_entrenador'];
?>