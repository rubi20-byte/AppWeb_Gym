<?php
include 'config.php';
include 'validar_entrenador.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_socio = $_POST['id_socio'];
    $fecha = date('Y-m-d');
    $peso = $_POST['peso'];
    $talla = $_POST['talla'];
    $imc = $_POST['imc'];
    $cintura = $_POST['cintura'];
    $cadera = $_POST['cadera'];
    $p_grasa = $_POST['porcentaje_grasa'];
    $p_musculo = $_POST['porcentaje_musculo'];
    $comentarios = mysqli_real_escape_string($conexion, $_POST['comentarios']);

    $sql = "INSERT INTO evaluaciones (id_socio, fecha_evaluacion, peso, talla, imc, cintura, cadera, porcentaje_grasa, porcentaje_musculo, comentarios) 
            VALUES ('$id_socio', '$fecha', '$peso', '$talla', '$imc', '$cintura', '$cadera', '$p_grasa', '$p_musculo', '$comentarios')";

    if ($conexion->query($sql)) {
        header("Location: mis_socios.php?status=success");
    } else {
        echo "Error al guardar: " . $conexion->error;
    }
}
?>