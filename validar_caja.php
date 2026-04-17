<?php
/**
 * SISTEMA DE GESTIÓN GYM RUBÍ
 * Archivo: validar_caja.php
 */

// Solo inicia sesión si no se ha iniciado antes
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Validamos acceso para Recepcionista o Administrador
if (!isset($_SESSION['rol']) || ($_SESSION['rol'] !== 'Recepcionista' && $_SESSION['rol'] !== 'Administrador')) {
    header("Location: login.php");
    exit();
}
?>