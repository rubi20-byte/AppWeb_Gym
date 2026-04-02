<?php
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id_producto'];
    $nombre = mysqli_real_escape_string($conexion, $_POST['nombre']);
    $cat = $_POST['categoria'];
    $p_venta = $_POST['p_venta'];
    $stock = $_POST['stock'];

    $sql = "UPDATE productos SET nombre='$nombre', categoria='$cat', precio_venta=$p_venta, stock=$stock WHERE id_producto=$id";

    if ($conexion->query($sql)) {
        header("Location: inventario.php?status=updated");
    } else {
        echo "Error: " . $conexion->error;
    }
}
?>