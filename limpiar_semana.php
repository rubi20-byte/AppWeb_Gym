<?php
session_start();
include 'config.php';
$id_socio = $_SESSION['id_socio'];

// Borra todos los registros del socio en la tabla semanal
$conexion->query("DELETE FROM planes_socio_semanal WHERE id_socio = '$id_socio'");

header("Location: socio_nutricion.php");
?>