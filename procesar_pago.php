<?php
include 'config.php';
session_start();

if ($_POST) {
    // 1. Limpiamos los datos para evitar errores de SQL
    $id_s       = mysqli_real_escape_string($conexion, $_POST['id_socio']);
    $monto      = mysqli_real_escape_string($conexion, $_POST['monto']);
    $metodo     = mysqli_real_escape_string($conexion, $_POST['metodo_pago']);
    $concepto   = mysqli_real_escape_string($conexion, $_POST['concepto']);
    $referencia = mysqli_real_escape_string($conexion, $_POST['referencia']);
    $fecha_pago = date('Y-m-d H:i:s');

    // 2. Insertar el registro del pago (con todas las columnas de tu BD)
    $sql_pago = "INSERT INTO pagos (id_socio, monto, fecha_pago, metodo_pago, referencia, estado, concepto) 
                 VALUES ('$id_s', '$monto', '$fecha_pago', '$metodo', '$referencia', 'pagado', '$concepto')";
    
    if ($conexion->query($sql_pago)) {
        
        $dias_extra = 0;
        $actualizar_fecha = false;

        // 3. Lógica para identificar cuánto tiempo sumar según el concepto seleccionado
        // Usamos strpos para que detecte la palabra aunque el texto sea "Mensualidad (30 días)"
        if (strpos($concepto, 'Mensualidad') !== false) {
            $dias_extra = 30;
            $actualizar_fecha = true;
        } elseif (strpos($concepto, 'Trimestre') !== false) {
            $dias_extra = 90;
            $actualizar_fecha = true;
        } elseif (strpos($concepto, 'Anual') !== false) {
            $dias_extra = 365;
            $actualizar_fecha = true;
        } elseif (strpos($concepto, 'Familiar') !== false) {
            $dias_extra = 30;
            $actualizar_fecha = true;
        }

        if ($actualizar_fecha) {
            // Calculamos la nueva fecha sumando los días a la fecha de HOY
            $nueva_fecha = date('Y-m-d', strtotime("+$dias_extra days"));
            
            // IMPORTANTE: Aquí se usa 'fecha_vencimiento' para que la tabla de socios se actualice
            $sql_update = "UPDATE socios SET fecha_vencimiento = '$nueva_fecha', estado = 'activo' WHERE id_socio = '$id_s'";
            
            if($conexion->query($sql_update)) {
                $aviso = "¡Pago de $concepto registrado! Nueva fecha: " . date('d/m/Y', strtotime($nueva_fecha));
            } else {
                $aviso = "Pago registrado, pero hubo un error al actualizar la fecha del socio: " . $conexion->error;
            }
        } else {
            // Para otros conceptos (Inscripción, Suplementos, etc.) solo activamos al socio si estaba vencido
            $conexion->query("UPDATE socios SET estado = 'activo' WHERE id_socio = '$id_s'");
            $aviso = "Pago por $concepto registrado con éxito.";
        }
        
        echo "<script>alert('$aviso'); window.location='pagos.php';</script>";
    } else {
        echo "Error al registrar pago en la tabla pagos: " . $conexion->error;
    }
}
?>