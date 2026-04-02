<?php
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_socio = $_POST['id_socio'];
    $id_alim  = $_POST['id_alimento'];
    $dia      = $_POST['dia'];
    $momento  = $_POST['momento'];
    $gramos   = $_POST['gramos'] ?? 100;

    $res = $conexion->query("SELECT calorias_por_100g FROM alimentos WHERE id_alimento = '$id_alim'");
    if ($res && $res->num_rows > 0) {
        $alim = $res->fetch_assoc();
        $cal_final = ($alim['calorias_por_100g'] / 100) * $gramos;

        // Limpiar duplicado en ese horario
        $conexion->query("DELETE FROM planes_socio_semanal WHERE id_socio = '$id_socio' AND dia_semana = '$dia' AND momento = '$momento'");

        $sql = "INSERT INTO planes_socio_semanal (id_socio, dia_semana, momento, id_alimento, cantidad_gramos, calorias_calculadas, estado) 
                VALUES ('$id_socio', '$dia', '$momento', '$id_alim', '$gramos', '$cal_final', 'activo')";

        if ($conexion->query($sql)) {
            header("Location: asignar_plan.php?id=$id_socio&success=1");
            exit();
        }
    }
    die("Error al procesar el alimento.");
}