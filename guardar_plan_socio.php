<?php
include 'config.php';
session_start();

if ($_POST) {
    $id_socio = $_SESSION['id_socio'];
    $dia = $_POST['dia'];
    $mom = $_POST['momento'];
    $id_alm = $_POST['id_alimento'];
    $gramos = $_POST['gramos'];

    // CORRECCIÓN: usamos calorias_por_100g
    $res = $conexion->query("SELECT calorias_por_100g FROM alimentos WHERE id_alimento = '$id_alm'");
    $alm = $res->fetch_assoc();
    
    // Cálculo: (calorias / 100) * gramos
    $cal_final = ($alm['calorias_por_100g'] / 100) * $gramos;

    // Limpiar anterior e insertar
    $conexion->query("DELETE FROM planes_socio_semanal WHERE id_socio = '$id_socio' AND dia_semana = '$dia' AND momento = '$mom'");

    $sql = "INSERT INTO planes_socio_semanal (id_socio, dia_semana, momento, id_alimento, cantidad_gramos, calorias_calculadas) 
            VALUES ('$id_socio', '$dia', '$mom', '$id_alm', '$gramos', '$cal_final')";

    if ($conexion->query($sql)) {
        header("Location: socio_nutricion.php");
    } else {
        echo "Error: " . $conexion->error;
    }
}