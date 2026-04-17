<?php
/**
 * SISTEMA DE GESTIÓN GYM RUBÍ
 * Archivo: dashboard_caja.php
 */

include 'config.php';
include 'validar_caja.php'; 

$hoy = date('Y-m-d');

// Ventas del día
$query_p = "SELECT SUM(monto) as total FROM pagos WHERE DATE(fecha_pago) = '$hoy'";
$res_p = $conexion->query($query_p);
$total_hoy = ($res_p) ? ($res_p->fetch_assoc()['total'] ?? 0) : 0;

// Cantidad de pagos realizados hoy
$total_transacciones = $conexion->query("SELECT COUNT(*) as total FROM pagos WHERE DATE(fecha_pago) = '$hoy'")->fetch_assoc()['total'];

// Últimos 5 movimientos para la tabla
$ultimos_movimientos = $conexion->query("SELECT p.*, s.nombre, s.apellido FROM pagos p LEFT JOIN socios s ON p.id_socio = s.id_socio ORDER BY p.id_pago DESC LIMIT 5");

include 'header_caja.php'; 
?>

<div class="page-wrapper">
    <div class="container-xl mt-4">
        
        <div class="page-header mb-4">
            <div class="row align-items-center">
                <div class="col">
                    <div class="page-pretitle text-uppercase">Bienvenid@</div>
                    <h2 class="page-title fw-bold" style="color: #206bc4;">
                        ¡Hola, <?php echo explode(' ', $_SESSION['nombre'])[0]; ?>!
                    </h2>
                </div>
                <div class="col-auto ms-auto">
                    <div class="btn-list">
                        <span class="d-none d-sm-inline">
                            <div class="text-muted fw-bold"><?php echo date('l, d F Y'); ?></div>
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <div class="row row-cards mb-4">
            <div class="col-md-6 col-lg-4">
                <div class="card card-sm border-0 shadow-sm" style="border-left: 4px solid #4ade80 !important;">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <span class="bg-green-lt avatar"><i class="ti ti-currency-dollar fs-2"></i></span>
                            </div>
                            <div class="col">
                                <div class="font-weight-medium">Caja del Día</div>
                                <div class="h1 mb-0 fw-bold">$<?php echo number_format($total_hoy, 2); ?></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-4">
                <div class="card card-sm border-0 shadow-sm" style="border-left: 4px solid #206bc4 !important;">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <span class="bg-blue-lt avatar"><i class="ti ti-receipt fs-2"></i></span>
                            </div>
                            <div class="col">
                                <div class="font-weight-medium">Transacciones</div>
                                <div class="h1 mb-0 fw-bold"><?php echo $total_transacciones; ?> cobros</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <a href="cajero_pagos.php" class="card card-link py-3 border-0 shadow-sm bg-azure-lt text-center">
                    <div class="card-body">
                        <div class="mb-3"><i class="ti ti-shopping-cart" style="font-size: 2.5rem;"></i></div>
                        <div class="h2 fw-bold mb-1">PUNTO DE VENTA</div>
                        <div class="text-muted small text-uppercase fw-bold">Venta de productos / suplementos</div>
                    </div>
                </a>
            </div>
            <div class="col-md-6">
                <a href="cajero_pagos.php" class="card card-link py-3 border-0 shadow-sm bg-teal-lt text-center">
                    <div class="card-body">
                        <div class="mb-3"><i class="ti ti-user-check" style="font-size: 2.5rem;"></i></div>
                        <div class="h2 fw-bold mb-1">COBRAR MEMBRESÍA</div>
                        <div class="text-muted small text-uppercase fw-bold">Renovación de planes y socios</div>
                    </div>
                </a>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">
                <h3 class="card-title fw-bold">Últimos Movimientos de Caja</h3>
            </div>
            <div class="table-responsive">
                <table class="table table-vcenter card-table table-hover">
                    <thead>
                        <tr>
                            <th>Concepto / Socio</th>
                            <th>Monto</th>
                            <th>Fecha/Hora</th>
                            <th class="w-1"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($m = $ultimos_movimientos->fetch_assoc()): ?>
                        <tr>
                            <td>
                                <div class="d-flex py-1 align-items-center">
                                    <div class="flex-fill">
                                        <div class="font-weight-medium">
                                            <?php echo ($m['nombre']) ? $m['nombre']." ".$m['apellido'] : "Venta General"; ?>
                                        </div>                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="text-green fw-bold">+$<?php echo number_format($m['monto'], 2); ?></span>
                            </td>
                            <td class="text-muted">
                                <?php echo date('d/m/Y H:i', strtotime($m['fecha_pago'])); ?>
                            </td>
                            <td>
                                <a href="ticket.php?id=<?php echo $m['id_pago']; ?>" class="btn btn-sm btn-ghost-secondary">Ticket</a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>