<?php
include 'config.php';
include 'validar_admin.php';

if (isset($_GET['id'])) {
    $id = mysqli_real_escape_string($conexion, $_GET['id']);

    // 1. Antes de borrar, ponemos en NULL el id_membresia de los socios que la usaban
    // Esto evita que la base de datos bloquee el borrado
    $conexion->query("UPDATE socios SET id_membresia = NULL WHERE id_membresia = $id");

    // 2. Ahora sí, borramos la membresía
    $sql = "DELETE FROM membresias WHERE id_membresia = $id";

    if ($conexion->query($sql)) {
        header("Location: membresias.php?res=eliminado");
    } else {
        echo "Error al eliminar: " . $conexion->error;
    }
} else {
    header("Location: membresias.php");
}
?>