<?php
include 'config.php';
if (session_status() === PHP_SESSION_NONE) { session_start(); }

if ($_POST) {
    $id_socio = $_SESSION['id_socio'] ?? 0;
    if ($id_socio == 0) { die("Error: Sesión no válida."); }

    $dia      = $_POST['dia'];
    $mom      = $_POST['momento'];
    $id_rec   = $_POST['id_alimento']; 
    $gramos   = $_POST['gramos'] ?? 100;

    $res_alm = $conexion->query("SELECT * FROM alimentos WHERE id_alimento = '$id_rec'");
    
    if ($res_alm && $res_alm->num_rows > 0) {
        $alm = $res_alm->fetch_assoc();
        $id_final = $id_rec;
        $gramos_final = $gramos; 
        $cal_final = ($alm['calorias_por_100g'] / 100) * $gramos;
    } else {
        $res_sug = $conexion->query("SELECT * FROM sugerencias_comidas WHERE id_sugerencia = '$id_rec'");
        $sug = $res_sug->fetch_assoc();
        $id_final = 0; 
        $cal_final = $sug['calorias'];
        $gramos_final = $id_rec; 
    }

    $conexion->query("DELETE FROM planes_socio_semanal WHERE id_socio = '$id_socio' AND dia_semana = '$dia' AND momento = '$mom'");
    $sql = "INSERT INTO planes_socio_semanal (id_socio, dia_semana, momento, id_alimento, cantidad_gramos, calorias_calculadas, estado) 
            VALUES ('$id_socio', '$dia', '$mom', '$id_final', '$gramos_final', '$cal_final', 'activo')";

    $conexion->query($sql);
    header("Location: socio_nutricion.php");
}