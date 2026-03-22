<?php
include 'config.php';
session_start();

if ($_POST) {
    $id_s       = mysqli_real_escape_string($conexion, $_POST['id_socio']);
    $monto      = mysqli_real_escape_string($conexion, $_POST['monto']);
    $metodo     = mysqli_real_escape_string($conexion, $_POST['metodo_pago']);
    $concepto   = mysqli_real_escape_string($conexion, $_POST['concepto']); // Viene el nombre de la membresía
    $referencia = mysqli_real_escape_string($conexion, $_POST['referencia']);
    $fecha_pago = date('Y-m-d H:i:s');

    // 1. Registrar el pago en la tabla pagos
    // Si no hay id_socio (Pase Diario), guardamos NULL para que la base de datos lo acepte
    $id_socio_db = !empty($id_s) ? "'$id_s'" : "NULL";
    
    $sql_pago = "INSERT INTO pagos (id_socio, monto, fecha_pago, metodo_pago, referencia, estado, concepto) 
                 VALUES ($id_socio_db, '$monto', '$fecha_pago', '$metodo', '$referencia', 'pagado', '$concepto')";
    
    if ($conexion->query($sql_pago)) {
        
        // 2. Si hay un socio seleccionado, actualizamos su vigencia
        if (!empty($id_s)) {
            // Buscamos cuánto dura la membresía seleccionada en la tabla membresias
            $res_m = $conexion->query("SELECT duracion_meses FROM membresias WHERE nombre = '$concepto'");
            
            if ($res_m->num_rows > 0) {
                $m = $res_m->fetch_assoc();
                $meses = $m['duracion_meses'];

                if ($meses > 0) {
                    // Calculamos la nueva fecha sumando los meses
                    $nueva_fecha = date('Y-m-d', strtotime("+$meses month"));
                    
                    // Actualizamos la columna correcta: fecha_vencimiento
                    $conexion->query("UPDATE socios SET fecha_vencimiento = '$nueva_fecha', estado = 'activo' WHERE id_socio = '$id_s'");
                    $aviso = "Pago de $concepto registrado. Nueva fecha: " . date('d/m/Y', strtotime($nueva_fecha));
                } else {
                    // Si es "Visita Diaria" (0 meses), solo aseguramos que el socio esté activo hoy
                    $conexion->query("UPDATE socios SET estado = 'activo' WHERE id_socio = '$id_s'");
                    $aviso = "Pago de Pase Diario registrado para socio.";
                }
            } else {
                // Si el concepto no es una membresía (ej: Inscripción o Producto)
                $aviso = "Pago por $concepto registrado con éxito.";
            }
        } else {
            $aviso = "Pase Diario registrado correctamente (Cliente externo).";
        }
        
        echo "<script>alert('$aviso'); window.location='pagos.php';</script>";
    } else {
        echo "Error al registrar pago: " . $conexion->error;
    }
}
?>