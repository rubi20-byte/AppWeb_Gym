<?php
session_start();
include 'config.php';

if (isset($_GET['id']) && isset($_SESSION['id_socio'])) {
    $id_plan = $_GET['id'];
    $id_socio = $_SESSION['id_socio'];

    // Eliminamos el alimento asegurándonos que pertenezca al socio actual
    $sql = "DELETE FROM planes_socio_semanal WHERE id_plan = '$id_plan' AND id_socio = '$id_socio'";
    
    if ($conexion->query($sql)) {
        header("Location: socio_nutricion.php?status=deleted");
    } else {
        header("Location: socio_nutricion.php?status=error");
    }
} else {
    header("Location: socio_nutricion.php");
}
exit();