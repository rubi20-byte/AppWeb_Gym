<?php
session_start();
// Validar que el rol sea exactamente 'Administrador' (con la A mayúscula)
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'Administrador') {
    header("Location: login.php");
    exit();
}
?>