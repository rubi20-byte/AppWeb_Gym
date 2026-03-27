<?php
session_start();
include 'config.php';
include 'validar_admin.php'; // Para que solo el admin/autorizado pueda guardar

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Recibimos los datos del modal
    $id_socio = $_POST['id_socio'];
    $id_rutina = $_POST['id_rutina'];
    $id_entrenador = $_POST['id_entrenador']; // Lo usamos para saber a qué entrenador regresar

    // Limpiamos los datos
    $id_socio = mysqli_real_escape_string($conexion, $id_socio);
    $id_rutina = !empty($id_rutina) ? "'" . mysqli_real_escape_string($conexion, $id_rutina) . "'" : "NULL";

    // Actualizamos la tabla socios
    $sql = "UPDATE socios SET id_rutina = $id_rutina WHERE id_socio = '$id_socio'";

    if ($conexion->query($sql)) {
        // Si todo sale bien, regresamos a la lista de ese entrenador con éxito
        header("Location: seguimiento_entrenador.php?id=$id_entrenador&status=success");
    } else {
        // Si hay error, regresamos con aviso de error
        header("Location: seguimiento_entrenador.php?id=$id_entrenador&status=error");
    }
} else {
    // Si alguien intenta entrar al archivo sin enviar el formulario, lo mandamos fuera
    header("Location: entrenadores.php");
}
exit();
?>