<?php
include 'config.php';

if (isset($_GET['id'])) {
    $id = mysqli_real_escape_string($conexion, $_GET['id']);
    $f_hoy = date('Y-m-d');
    $multa_monto = 50.00; // Aquí tú defines cuánto cobrar de multa

    // 1. Consultar datos del socio y su plan
    $sql_socio = "SELECT s.id_membresia, s.fecha_vencimiento, m.duracion_meses, m.precio 
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

        // 2. LÓGICA DE PENALIZACIÓN
        $nota_historial = "Renovación estándar";
        $total_pago = $precio_base;

        if ($f_hoy > $vencimiento_actual) {
            // El socio está atrasado
            $total_pago = $precio_base + $multa_monto;
            $nota_historial = "Renovación con PENALIZACIÓN por atraso ($$multa_monto)";
        }

        // 3. Calcular nueva fecha
        $nueva_fecha = date('Y-m-d', strtotime("+ $meses month"));

        // 4. Actualizar al socio
        $update_socio = "UPDATE socios SET 
                         fecha_vencimiento = '$nueva_fecha', 
                         estado = 'activo' 
                         WHERE id_socio = $id";
        
        if ($conexion->query($update_socio)) {
            // 5. REGISTRAR EN HISTORIAL (Aquí el profe verá la multa en la nota)
            // Asegúrate de tener la columna 'nota' en tu tabla o usa un campo que tengas libre
            $sql_hist = "INSERT INTO socios_membresias (id_socio, id_membresia, fecha_inicio, fecha_fin, estado) 
                         VALUES ($id, $id_mem, '$f_hoy', '$nueva_fecha', 'activa')";
            $conexion->query($sql_hist);
            
            // También lo registramos en pagos para que cuadre la caja
            $sql_pago = "INSERT INTO pagos (id_socio, monto, fecha_pago, nota) 
                         VALUES ($id, '$total_pago', NOW(), '$nota_historial')";
            $conexion->query($sql_pago);

            header("Location: socios.php?res=renovado&pago=$total_pago");
        }
    }
}
?>