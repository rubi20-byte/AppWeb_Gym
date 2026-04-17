<?php
include 'config.php';

if (isset($_GET['id'])) {
    $id = mysqli_real_escape_string($conexion, $_GET['id']);
    
    // Consulta para traer los datos del pago y el nombre del socio si existe
    $sql = "SELECT p.*, s.nombre, s.apellido 
            FROM pagos p 
            LEFT JOIN socios s ON p.id_socio = s.id_socio 
            WHERE p.id_pago = '$id'";
            
    $res = $conexion->query($sql);
    
    if ($res && $res->num_rows > 0) {
        echo json_encode($res->fetch_assoc());
    }
}
?>