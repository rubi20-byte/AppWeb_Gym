<?php
include 'config.php';
session_start();

// 1. Verificación de seguridad: Solo el admin puede borrar
if ($_SESSION['rol'] !== 'admin') {
    header("Location: clases_agenda.php");
    exit();
}

// 2. Obtener el ID
$id_clase = $_GET['id'] ?? null;

if ($id_clase) {
    // 3. Validación: ¿Hay alumnos inscritos en esta clase?
    // Es mejor no borrar una clase que ya tiene gente anotada para no perder el historial.
    $check_reservas = $conexion->query("SELECT id_reserva FROM reservas_clases WHERE id_clase = '$id_clase'");

    if ($check_reservas->num_rows > 0) {
        echo "<script>
                alert('No se puede eliminar: Esta clase ya tiene socios inscritos. Te recomendamos cambiar su estado a Inactivo.');
                window.location.href='admin_clases.php';
              </script>";
    } else {
        // 4. Proceder a eliminar
        $sql = "DELETE FROM clases WHERE id_clase = '$id_clase'";
        
        if ($conexion->query($sql)) {
            echo "<script>
                    alert('Clase eliminada correctamente.');
                    window.location.href='admin_clases.php';
                  </script>";
        } else {
            echo "Error al eliminar: " . $conexion->error;
        }
    }
} else {
    header("Location: admin_clases.php");
}
?>