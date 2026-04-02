<?php
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = mysqli_real_escape_string($conexion, $_POST['id_socio']);
    $cal = mysqli_real_escape_string($conexion, $_POST['calorias']); 

    if ($id > 0 && $cal > 0) {
        $sql = "UPDATE socios SET calorias_objetivo = '$cal' WHERE id_socio = '$id'";
        if ($conexion->query($sql)) {
            header("Location: asignar_plan.php?id=$id&success=1");
            exit;
        }
    }
    die("Error: Datos inválidos.");
}