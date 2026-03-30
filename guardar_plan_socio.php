<?php
include 'config.php';
session_start();

// Habilitar errores para diagnóstico
ini_set('display_errors', 1);
error_reporting(E_ALL);

if ($_POST) {
    $id_socio = $_SESSION['id_socio'];
    $dia      = $_POST['dia'];
    $mom      = $_POST['momento'];
    $id_recibido = $_POST['id_alimento']; // Aquí llega el id_sugerencia
    $gramos   = $_POST['gramos'] ?? 100;

    // 1. Buscamos en 'sugerencias_comidas' usando tu columna 'id_sugerencia'
    $sql_sug = "SELECT * FROM sugerencias_comidas WHERE id_sugerencia = '$id_recibido'";
    $res_sug = $conexion->query($sql_sug);

    if ($res_sug && $res_sug->num_rows > 0) {
        // CASO: VIENE DEL CARRUSEL (Sugerencia)
        $sug = $res_sug->fetch_assoc();
        $cal_final = $sug['calorias']; // Usamos las calorías directas de la tabla
        
        /* IMPORTANTE: Como tu tabla 'sugerencias_comidas' no tiene id_alimento, 
           usaremos un valor 0 o nulo para id_alimento en el plan, o puedes 
           ajustar tu tabla 'planes_socio_semanal' para que acepte el nombre.
        */
        $id_alm_final = 0; 
    } else {
        // CASO: VIENE DEL BUSCADOR MANUAL (Alimento real)
        $res_alm = $conexion->query("SELECT calorias_por_100g FROM alimentos WHERE id_alimento = '$id_recibido'");
        $alm = $res_alm->fetch_assoc();
        
        if ($alm) {
            $id_alm_final = $id_recibido;
            $cal_final = ($alm['calorias_por_100g'] / 100) * $gramos;
        } else {
            die("Error: No se encontró el alimento o sugerencia con ID: " . $id_recibido);
        }
    }

    // 2. Limpiar entrada previa para evitar duplicados
    $conexion->query("DELETE FROM planes_socio_semanal WHERE id_socio = '$id_socio' AND dia_semana = '$dia' AND momento = '$mom'");

    // 3. Insertar el nuevo registro
    // Asegúrate de que 'id_alimento' en tu tabla permita ceros o valores nulos si es sugerencia
    $sql = "INSERT INTO planes_socio_semanal (id_socio, dia_semana, momento, id_alimento, cantidad_gramos, calorias_calculadas) 
            VALUES ('$id_socio', '$dia', '$mom', '$id_alm_final', '$gramos', '$cal_final')";

    if ($conexion->query($sql)) {
        header("Location: socio_nutricion.php?success=1");
    } else {
        echo "Error en la base de datos: " . $conexion->error;
    }
}
?>