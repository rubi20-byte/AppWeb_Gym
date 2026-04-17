<?php 
include 'config.php';
session_start();

// Verificamos que los datos vengan por el método POST (desde el formulario)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    // 1. Recolección y limpieza de datos básicos
    $tipo_pago = $_POST['tipo_pago'] ?? 'membresia';
    $monto      = mysqli_real_escape_string($conexion, $_POST['monto']);
    $metodo     = mysqli_real_escape_string($conexion, $_POST['metodo_pago']);
    // Si no hay socio (venta al público), guardamos NULL en la base de datos
    $id_socio   = !empty($_POST['id_socio']) ? "'".mysqli_real_escape_string($conexion, $_POST['id_socio'])."'" : "NULL";
    $fecha_pago = date('Y-m-d H:i:s');
    $concepto   = mysqli_real_escape_string($conexion, $_POST['concepto']);

    // --- BLOQUE PARA VENTA DE PRODUCTOS ---
    if ($tipo_pago == 'producto') {
        $id_producto = mysqli_real_escape_string($conexion, $_POST['id_producto']);
        $cantidad    = intval($_POST['cantidad']);

        // Insertamos el registro de la venta en la tabla pagos
        $sql = "INSERT INTO pagos (id_socio, monto, fecha_pago, metodo_pago, estado, concepto) 
                VALUES ($id_socio, '$monto', '$fecha_pago', '$metodo', 'pagado', '$concepto')";
        
        if ($conexion->query($sql)) {
            // CAPTURA CRÍTICA: Guardamos el ID del pago antes de hacer cualquier otra operación
            $id_generado = $conexion->insert_id; 
            
            // Actualizamos el inventario restando la cantidad vendida
            $conexion->query("UPDATE productos SET stock = stock - $cantidad WHERE id_producto = '$id_producto'");
            
            // Redirigimos a la caja enviando el ID para que salte el ticket
            header("Location: pagos.php?pago_exitoso=" . $id_generado);
            exit();
        } else {
            die("Error en producto: " . $conexion->error);
        }

    } 
    // --- BLOQUE PARA PAGO DE MEMBRESÍAS ---
    else {
        // Insertamos el registro del pago de la membresía
        $sql = "INSERT INTO pagos (id_socio, monto, fecha_pago, metodo_pago, estado, concepto) 
                VALUES ($id_socio, '$monto', '$fecha_pago', '$metodo', 'pagado', '$concepto')";

        if ($conexion->query($sql)) {
            // CAPTURA CRÍTICA: Guardamos el ID del pago para el ticket
            $id_generado = $conexion->insert_id;

            // Si el pago está amarrado a un socio registrado, actualizamos su vigencia
            if ($_POST['id_socio'] != "") {
                $id_s_clean = mysqli_real_escape_string($conexion, $_POST['id_socio']);
                
                // Consultamos la tabla de membresías para saber cuántos meses sumarle al socio
                $res_m = $conexion->query("SELECT duracion_meses FROM membresias WHERE nombre = '$concepto'");
                
                if ($res_m && $m = $res_m->fetch_assoc()) {
                    $meses = $m['duracion_meses'];
                    // Calculamos la nueva fecha (Fecha actual + meses de la membresía)
                    $nueva_fecha = date('Y-m-d', strtotime("+$meses month"));
                    
                    // Actualizamos al socio: nueva fecha y lo ponemos como 'activo'
                    $conexion->query("UPDATE socios SET fecha_vencimiento = '$nueva_fecha', estado = 'activo' WHERE id_socio = '$id_s_clean'");
                }
            }
            // Redirigimos a la caja enviando el ID para que salte el ticket
            header("Location: pagos.php?pago_exitoso=" . $id_generado);
            exit();
        } else {
            die("Error en membresía: " . $conexion->error);
        }
    }
} else {
    // Si intentan entrar al archivo directamente sin el formulario, los regresa a pagos
    header("Location: pagos.php");
    exit();
}
?>