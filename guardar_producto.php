<?php
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Escapar datos para evitar errores de SQL
    $nombre   = mysqli_real_escape_string($conexion, $_POST['nombre']);
    $cat      = mysqli_real_escape_string($conexion, $_POST['categoria']);
    $stock    = intval($_POST['stock']);
    $p_compra = floatval($_POST['p_compra']);
    $p_venta  = floatval($_POST['p_venta']);

    $sql = "INSERT INTO productos (nombre, categoria, stock, precio_compra, precio_venta) 
            VALUES ('$nombre', '$cat', $stock, $p_compra, $p_venta)";

    if ($conexion->query($sql)) {
        // Redirigir al inventario con un parámetro de éxito
        header("Location: inventario.php?success=1");
    } else {
        echo "Error al guardar el producto: " . $conexion->error;
    }
} else {
    header("Location: inventario.php");
}
?>