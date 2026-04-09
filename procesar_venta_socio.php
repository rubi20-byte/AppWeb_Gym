<?php
include 'config.php';
session_start();

// Verificamos que el socio esté logueado y que venga un ID de producto
if (!isset($_SESSION['id_socio']) || !isset($_GET['id'])) {
    header("Location: login.php");
    exit();
}

$id_producto = intval($_GET['id']);
$id_socio = $_SESSION['id_socio'];

// validacion de stock
$stmt = $conexion->prepare("SELECT nombre, precio_venta, stock FROM productos WHERE id_producto = ? AND estado = 'activo'");
$stmt->bind_param("i", $id_producto);
$stmt->execute();
$resultado = $stmt->get_result();
$producto = $resultado->fetch_assoc();

if ($producto && $producto['stock'] > 0) {
    $nombre_prod = $producto['nombre'];
    $monto = $producto['precio_venta'];
    $concepto = "Compra de Producto: " . $nombre_prod;

    // Iniciamos una transacción para asegurar que ambos pasos se cumplan o ninguno
    $conexion->begin_transaction();

    try {
        // escontar 1 unidad del stock
        $update_stock = $conexion->prepare("UPDATE productos SET stock = stock - 1 WHERE id_producto = ?");
        $update_stock->bind_param("i", $id_producto);
        $update_stock->execute();

        // registrar el movimiento en pagos
        $insert_pago = $conexion->prepare("INSERT INTO pagos (id_socio, concepto, monto, fecha_pago, estado) VALUES (?, ?, ?, NOW(), 'Pagado')");
        $insert_pago->bind_param("isd", $id_socio, $concepto, $monto);
        $insert_pago->execute();

        // guarda cambios
        $conexion->commit();
        
        // regresar a la tienda con mensaje de exito
        header("Location: tienda_socio.php?status=success&item=" . urlencode($nombre_prod));
    } catch (Exception $e) {
        // Si algo falla, se revierte el descuento de stock
        $conexion->rollback();
        echo "Error en la transacción: " . $e->getMessage();
    }
} else {
    header("Location: tienda_socio.php?status=no_stock");
}
?>