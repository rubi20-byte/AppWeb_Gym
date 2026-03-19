<?php
include 'config.php';

if ($_POST) {
    // Recibimos los datos del formulario
    $id_socio = $_POST['id_socio'];
    $peso = (float)$_POST['peso'];
    $talla_cm = (float)$_POST['talla'];
    $cintura = $_POST['cintura'];
    $cadera = $_POST['cadera'];
    $grasa = $_POST['porcentaje_grasa'];
    $musculo = $_POST['porcentaje_musculo'];
    $comentarios = mysqli_real_escape_string($conexion, $_POST['comentarios']);
    $fecha = date('Y-m-d');

    // Calculo del IMC
    $imc = 0;
    if ($peso > 0 && $talla_cm > 0) {
        $talla_m = $talla_cm / 100;
        $imc = $peso / ($talla_m * $talla_m);
        $imc = round($imc, 2);
    }

    // Insertamos en la tabla 'evaluaciones' incluyendo la columna 'talla'
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
        echo "<script>
                alert('¡Evaluación guardada!');
                window.location='ver_progreso.php?id=$id_socio';
              </script>";
    } else {
        echo "Error al guardar: " . $conexion->error;
    }
}
?>