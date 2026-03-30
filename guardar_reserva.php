<?php
session_start();
include 'config.php';

$id_socio = $_SESSION['id_socio'] ?? 0;
$id_clase = $_GET['id'] ?? 0;
$tipo_solicitud = $_GET['tipo'] ?? 'confirmar'; // 'confirmar' o 'espera'
$fecha_hoy = date('Y-m-d');

if ($id_socio == 0 || $id_clase == 0) {
    die("Error: Datos insuficientes para procesar la reserva.");
}

// 1. Verificar si el socio ya tiene una reserva activa para esta clase hoy
$check_usuario = $conexion->query("SELECT id_reserva FROM reservas_clases 
                                   WHERE id_socio = '$id_socio' 
                                   AND id_clase = '$id_clase' 
                                   AND fecha_clase = '$fecha_hoy' 
                                   AND estado_reserva != 'Cancelada'");

if ($check_usuario->num_rows > 0) {
    echo "<script>alert('Ya tienes una reserva para esta clase.'); window.location.href='socio_clases.php';</script>";
    exit;
}

// 2. Verificar cupo actual de la clase
$sql_clase = $conexion->query("SELECT capacidad FROM clases WHERE id_clase = '$id_clase'");
$clase = $sql_clase->fetch_assoc();
$capacidad_max = $clase['capacidad'];

$sql_inscritos = $conexion->query("SELECT COUNT(*) as total FROM reservas_clases 
                                   WHERE id_clase = '$id_clase' 
                                   AND fecha_clase = '$fecha_hoy' 
                                   AND estado_reserva = 'Confirmada'");
$inscritos = $sql_inscritos->fetch_assoc()['total'];

// 3. Determinar estado de la reserva
$estado_final = 'Confirmada';
if ($inscritos >= $capacidad_max) {
    $estado_final = 'Lista de Espera';
}

// 4. Insertar la reserva
$sql_insert = "INSERT INTO reservas_clases (id_clase, id_socio, fecha_clase, estado_reserva) 
               VALUES ('$id_clase', '$id_socio', '$fecha_hoy', '$estado_final')";

if ($conexion->query($sql_insert)) {
    $mensaje = ($estado_final == 'Confirmada') ? "¡Reserva confirmada!" : "Cupo lleno. Has quedado en lista de espera.";
    echo "<script>alert('$mensaje'); window.location.href='socio_clases.php';</script>";
} else {
    echo "Error al reservar: " . $conexion->error;
}
?>