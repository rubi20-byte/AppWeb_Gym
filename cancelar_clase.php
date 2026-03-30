<?php
include 'config.php';
session_start();

$id_usuario = $_SESSION['id_socio'] ?? $_SESSION['id_usuario'];
$id_reserva = $_GET['id'] ?? null;

if ($id_reserva && $id_usuario) {
    // Validamos que la reserva pertenezca realmente al socio logueado
    $sql = "DELETE FROM reservas_clases WHERE id_reserva = '$id_reserva' AND id_socio = '$id_usuario'";
    
    if ($conexion->query($sql)) {
        echo "<script>
                alert('Te has dado de baja de la clase exitosamente.');
                window.location.href='mis_clases.php';
              </script>";
    } else {
        echo "Error al procesar la baja: " . $conexion->error;
    }
} else {
    header("Location: mis_reservas.php");
}
?>