<?php
session_start();

// Si no hay sesión iniciada O el rol no es 'Socio', lo mandamos al login
// Usamos strtolower y trim para que sea igual de flexible que el login
if (!isset($_SESSION['rol']) || strtolower(trim($_SESSION['rol'])) !== 'socio') {
    header("Location: login.php");
    exit();
}

// Si llega aquí, es porque es un socio real y tiene permiso de ver su QR
?>