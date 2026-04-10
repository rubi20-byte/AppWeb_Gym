<?php
include 'config.php';
include 'validar_admin.php'; 

if ($_POST) {
    // 1. Recibimos y saneamos los datos
    $id_socio = mysqli_real_escape_string($conexion, $_POST['id_socio']);
    $peso = (float)$_POST['peso'];
    $talla_cm = (float)$_POST['talla'];
    $cintura = mysqli_real_escape_string($conexion, $_POST['cintura']);
    $cadera = mysqli_real_escape_string($conexion, $_POST['cadera']);
    $grasa = mysqli_real_escape_string($conexion, $_POST['porcentaje_grasa']);
    $musculo = mysqli_real_escape_string($conexion, $_POST['porcentaje_musculo']);
    $comentarios = mysqli_real_escape_string($conexion, strip_tags($_POST['comentarios']));
    
    // Usamos la fecha actual del servidor
    $fecha = date('Y-m-d');

    // 2. Cálculo del IMC
    $imc = 0;
    if ($peso > 0 && $talla_cm > 0) {
        $talla_m = $talla_cm / 100;
        $imc = round($peso / ($talla_m * $talla_m), 2);
    }

    $sql = "INSERT INTO evaluaciones (
                id_socio, 
                fecha_evaluacion, 
                peso, 
                talla, 
                imc, 
                cintura, 
                cadera, 
                porcentaje_grasa, 
                porcentaje_musculo, 
                comentarios
            ) VALUES (
                '$id_socio', 
                '$fecha', 
                '$peso', 
                '$talla_cm', 
                '$imc', 
                '$cintura', 
                '$cadera', 
                '$grasa', 
                '$musculo', 
                '$comentarios'
            )";

    if ($conexion->query($sql)) {
        // Redirección limpia para evitar re-envío de formulario al refrescar
        header("Location: ver_progreso.php?id=$id_socio&res=success");
        exit();
    } else {
        echo "<div class='alert alert-danger'>Error al guardar: " . $conexion->error . "</div>";
    }
}
?>