<?php
include 'validar_admin.php';
include 'config.php';
include 'header.php';

$query = "SELECT p.*, s.nombre as socio_nombre, s.apellido as socio_apellido 
          FROM pagos p 
          INNER JOIN socios s ON p.id_socio = s.id_socio 
          WHERE p.concepto LIKE '%Producto%' 
          ORDER BY p.fecha_pago DESC";
$res_ventas = $conexion->query($query);

$total_ventas = $conexion->query("SELECT SUM(monto) as total FROM pagos WHERE concepto LIKE '%Producto%'")->fetch_assoc();
?>

<div class="page-wrapper">
    <div class="container-xl mt-4">
        <div class="page-header mb-4">
            <div class="row align-items-center">
                <div class="col">
                    <h2 class="page-title text-teal">
                        Historial de Ventas
                    </h2>
                    <p class="text-muted small">Registro contable de salidas de inventario y pagos recibidos.</p>
                </div>
                <div class="col-auto">
                    <div class="card bg-gray-100 border-0 shadow-sm">
                        <div class="card-body py-2 px-3">
                            <span class="text-uppercase fw-bold text-teal small d-block">Ingreso Total</span>
                            <span class="h2 mb-0 fw-bold text-dark">$<?php echo number_format($total_ventas['total'], 2); ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-status-top bg-secondary"></div>
            
            <div class="table-responsive">
                <table class="table table-vcenter card-table table-hover">
                    <thead class="bg-light">
                        <tr>
                            <th class="text-secondary">Fecha / Hora</th>
                            <th class="text-secondary">Socio</th>
                            <th class="text-secondary">Concepto</th>
                            <th class="text-secondary text-end">Importe</th>
                            <th class="text-secondary text-center">Estatus</th>
                            <th class="w-1"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($res_ventas && $res_ventas->num_rows > 0): ?>
                            <?php while($v = $res_ventas->fetch_assoc()): ?>
                            <tr>
                                <td class="text-muted small">
                                    <?php echo date('d/m/Y H:i', strtotime($v['fecha_pago'])); ?>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <span class="avatar avatar-xs me-2 rounded-circle">
                                            <?php echo strtoupper(substr($v['socio_nombre'], 0, 1)); ?>
                                        </span>
                                        <div class="fw-bold"><?php echo htmlspecialchars($v['socio_nombre'] . " " . $v['socio_apellido']); ?></div>
                                    </div>
                                </td>
                                <td>
                                    <span class="text-truncate d-inline-block" style="max-width: 200px;">
                                        <?php echo htmlspecialchars($v['concepto']); ?>
                                    </span>
                                </td>
                                <td class="text-end fw-bold text-dark">
                                    $<?php echo number_format($v['monto'], 2); ?>
                                </td>
                                <td class="text-center">
                                    <span class="badge badge-outline text-secondary text-uppercase fw-bold" style="font-size: 0.7rem;">
                                        <?php echo $v['estado']; ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="#" class="btn btn-ghost-secondary btn-icon" title="Imprimir Comprobante">
                                        <i class="ti ti-printer"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <div class="text-muted">No se encontraron registros de ventas.</div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>