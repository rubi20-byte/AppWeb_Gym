<?php
include 'config.php';
include 'validar_admin.php';

if (isset($_GET['id'])) {
    $id_membresia = $_GET['id'];

    // 1. Buscamos el nombre de la membresía para la consulta
    $info = $conexion->query("SELECT nombre FROM membresias WHERE id_membresia = $id_membresia")->fetch_assoc();
    $nombre_plan = $info['nombre'];

    // 2. CONTAR SOCIOS: Verificamos si hay socios activos con este plan
    // Nota: Ajusta 'plan_estudio' o como se llame tu columna de membresía en la tabla socios
    $check_socios = $conexion->query("SELECT COUNT(*) as total FROM socios WHERE membresia = '$nombre_plan' AND eliminado = 0");
    $resultado = $check_socios->fetch_assoc();

    if ($resultado['total'] > 0) {
        // SI HAY SOCIOS: No permitimos el borrado y mandamos alerta
        header("Location: membresias.php?error=plan_en_uso&cantidad=" . $resultado['total']);
        exit();
    } else {
        // NO HAY SOCIOS: Procedemos al borrado lógico
        $conexion->query("UPDATE membresias SET estado = 'inactivo' WHERE id_membresia = $id_membresia");
        header("Location: membresias.php?msj=eliminado");
        exit();
    }
}
?>