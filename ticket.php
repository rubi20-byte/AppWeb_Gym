<?php
include 'config.php';
session_start();

$id_pago = $_GET['id'] ?? 0;

// Consulta de la venta con el nombre del socio y el proveedor del producto (si aplica)
$query = "SELECT p.*, s.nombre as socio_n, s.apellido as socio_a, u.nombre_completo as cajero 
          FROM pagos p 
          LEFT JOIN socios s ON p.id_socio = s.id_socio 
          LEFT JOIN usuarios u ON u.id_usuario = p.id_usuario 
          WHERE p.id_pago = '$id_pago'";
$res = $conexion->query($query);
$datos = $res->fetch_assoc();

if (!$datos) { die("Venta no encontrada."); }
?>
<!DOCTYPE html>
<html>
<head>
    <title>Ticket #<?php echo $id_pago; ?></title>
    <style>
        body { font-family: 'Courier New', Courier, monospace; width: 250px; font-size: 12px; }
        .text-center { text-align: center; }
        .divider { border-top: 1px dashed #000; margin: 10px 0; }
        .total { font-size: 16px; font-weight: bold; }
        @media print { .no-print { display: none; } }
    </style>
</head>
<body onload="window.print()">
    <div class="text-center">
        <h2 style="margin-bottom:0;">GYM RUBÍ</h2>
        <p style="margin-top:0;">¡Fuerza y Disciplina!</p>
    </div>

    <div class="divider"></div>
    <p><strong>Ticket:</strong> #<?php echo $id_pago; ?><br>
    <strong>Fecha:</strong> <?php echo date('d/m/Y H:i', strtotime($datos['fecha_pago'])); ?><br>
    <strong>Atendió:</strong> <?php echo $datos['cajero']; ?></p>

    <div class="divider"></div>
    <p><strong>Socio:</strong> <?php echo $datos['socio_n'] ? $datos['socio_n'] : 'Venta General'; ?></p>
    <p><strong>Concepto:</strong> <?php echo $datos['concepto'] ?? 'Venta de Productos'; ?></p>
    
    <div class="divider"></div>
    <div class="text-center">
        <span class="total">TOTAL: $<?php echo number_format($datos['monto'], 2); ?></span>
    </div>
    <div class="divider"></div>
    
    <p class="text-center">¡Gracias por tu compra!<br>gymrubi.com</p>

    <button class="no-print" onclick="window.print()" style="width:100%; margin-top:10px;">Imprimir</button>
</body>
</html>