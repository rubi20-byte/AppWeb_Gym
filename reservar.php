<?php
include 'config.php';
session_start();

// Buscamos el ID del socio en la sesión (ahora con el nombre correcto)
$id_socio = $_SESSION['id_socio'] ?? $_SESSION['id_usuario'] ?? null;

if (!$id_socio) {
    die("Error: No se encontró tu ID de socio. Asegúrate de estar logueado.");
}

$id_clase = $_GET['id'] ?? null;
$fecha_hoy = date('Y-m-d');

if (!$id_clase) {
    die("Error: Clase no especificada.");
}

// Insertar la reserva usando tus columnas confirmadas
$sql = "INSERT INTO reservas_clases (id_clase, id_socio, fecha_clase, asistencia, estado_reserva) 
        VALUES ('$id_clase', '$id_socio', '$fecha_hoy', 0, 'confirmada')";

if ($conexion->query($sql)) {
    echo "<script>alert('¡Te has inscrito correctamente!'); window.location.href='clases_agenda.php';</script>";
} else {
    echo "Error al inscribirse: " . $conexion->error;
}
?>