<?php
include 'config.php';

if (isset($_GET['id'])) {
    $id = mysqli_real_escape_string($conexion, $_GET['id']);
    $f_hoy = date('Y-m-d');
    $multa_monto = 50.00; // Pago extra por pago tardío

    // 1. OBTENER DATOS ACTUALES (Usando fecha_vencimiento)
    $sql_socio = "SELECT s.id_membresia, s.fecha_vencimiento, m.duracion_meses, m.precio, m.nombre as nombre_mem
                  FROM socios s 
                  JOIN membresias m ON s.id_membresia = m.id_membresia 
                  WHERE s.id_socio = $id";
    $res_socio = $conexion->query($sql_socio);

    if ($res_socio->num_rows > 0) {
        $datos = $res_socio->fetch_assoc();
        $id_mem = $datos['id_membresia'];
        $meses = $datos['duracion_meses'];
        $vencimiento_actual = $datos['fecha_vencimiento'];
        $precio_base = $datos['precio'];
        $nombre_membresia = $datos['nombre_mem'];

        $total_pago = $precio_base;
        $referencia = "Renovación estándar";

        // 2. LÓGICA DE MULTA (Si ya venció)
        if ($f_hoy > $vencimiento_actual) {
            $total_pago = $precio_base + $multa_monto;
            $referencia = "Renovación con MULTA ($$multa_monto) por atraso";
        }

        // 3. CALCULAR NUEVA FECHA (A partir de hoy)
        $nueva_fecha = date('Y-m-d', strtotime("+ $meses month"));

        // 4. ACTUALIZAR TABLA SOCIOS
        $update_socio = "UPDATE socios SET 
                         fecha_vencimiento = '$nueva_fecha', 
                         estado = 'activo' 
                         WHERE id_socio = $id";
        
        if ($conexion->query($update_socio)) {
            
            // 5. REGISTRAR EN PAGOS
            $concepto_pago = "Membresía: " . $nombre_membresia;
            $sql_pago = "INSERT INTO pagos (id_socio, monto, fecha_pago, metodo_pago, referencia, estado, concepto) 
                         VALUES ('$id', '$total_pago', NOW(), 'Efectivo', '$referencia', 'pagado', '$concepto_pago')";
            $conexion->query($sql_pago);

            // 6. HISTORIAL DE MEMBRESÍAS
            $sql_hist = "INSERT INTO socios_membresias (id_socio, id_membresia, fecha_inicio, fecha_fin, estado) 
                         VALUES ($id, $id_mem, '$f_hoy', '$nueva_fecha', 'activa')";
            $conexion->query($sql_hist);

            header("Location: socios.php?res=renovado&pago=$total_pago");
        } else {
            echo "Error al actualizar socio: " . $conexion->error;
        }
    }
}
?>