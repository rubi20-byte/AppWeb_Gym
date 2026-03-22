<?php 
include 'config.php';
include 'validar_admin.php'; 
include 'header.php'; 

// Definimos que solo queremos ver lo de HOY
$hoy = date('Y-m-d');

// 1. Consulta para el resumen de dinero por método de pago
$res_metodos = $conexion->query("SELECT 
    SUM(CASE WHEN metodo_pago = 'Efectivo' THEN monto ELSE 0 END) as efectivo,
    SUM(CASE WHEN metodo_pago = 'Tarjeta' THEN monto ELSE 0 END) as tarjeta,
    SUM(CASE WHEN metodo_pago = 'Transferencia' THEN monto ELSE 0 END) as transferencia,
    SUM(monto) as total_dia,
    COUNT(id_pago) as total_operaciones
    FROM pagos 
    WHERE DATE(fecha_pago) = '$hoy'");

$c = $res_metodos->fetch_assoc();

// 2. Consulta para la lista detallada de movimientos de HOY
$movimientos = $conexion->query("SELECT p.*, s.nombre, s.apellido 
    FROM pagos p 
    LEFT JOIN socios s ON p.id_socio = s.id_socio 
    WHERE DATE(p.fecha_pago) = '$hoy' 
    ORDER BY p.fecha_pago DESC");
?>

<div class="page-wrapper">
    <div class="container-xl mt-4">
        <div class="page-header mb-4">
            <div class="row align-items-center">
                <div class="col">
                    <h2 class="page-title text-primary">Corte de Caja Diario</h2>
                    <div class="text-muted small mt-1">Fecha de corte: <strong><?php echo date('d/m/Y'); ?></strong></div>
                </div>
                <div class="col-auto">
                    <button onclick="window.print();" class="btn btn-outline-secondary">
                        <i class="ti ti-printer me-2"></i> Imprimir Corte
                    </button>
                </div>
            </div>
        </div>

        <div class="row row-cards">
            <div class="col-md-4">
                <div class="card card-sm bg-primary-lt shadow-sm border-0">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="subheader text-primary">Total Recaudado</div>
                        </div>
                        <div class="h1 mb-0 fw-bold">$<?php echo number_format($c['total_dia'] ?? 0, 2); ?></div>
                        <div class="text-muted small"><?php echo $c['total_operaciones']; ?> movimientos hoy</div>
                    </div>
                </div>
            </div>

            <div class="col-md-8">
                <div class="card shadow-sm border-0">
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-4 border-end">
                                <div class="text-muted mb-1 small">Efectivo</div>
                                <div class="h3 mb-0 text-success">$<?php echo number_format($c['efectivo'] ?? 0, 2); ?></div>
                            </div>
                            <div class="col-4 border-end">
                                <div class="text-muted mb-1 small">Tarjeta</div>
                                <div class="h3 mb-0 text-blue">$<?php echo number_format($c['tarjeta'] ?? 0, 2); ?></div>
                            </div>
                            <div class="col-4">
                                <div class="text-muted mb-1 small">Transferencia</div>
                                <div class="h3 mb-0 text-purple">$<?php echo number_format($c['transferencia'] ?? 0, 2); ?></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-light">
                        <h3 class="card-title">Detalle de Operaciones (Hoy)</h3>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-vcenter card-table">
                            <thead>
                                <tr>
                                    <th>Hora</th>
                                    <th>Socio / Cliente</th>
                                    <th>Concepto</th>
                                    <th>Método</th>
                                    <th>Monto</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if($movimientos->num_rows > 0): ?>
                                    <?php while($m = $movimientos->fetch_assoc()): ?>
                                    <tr>
                                        <td class="text-muted"><?php echo date('H:i', strtotime($m['fecha_pago'])); ?></td>
                                        <td>
                                            <?php 
                                            // Si id_socio es NULL, mostramos la referencia (Pase Diario)
                                            echo $m['nombre'] ? $m['nombre']." ".$m['apellido'] : "<span class='text-orange'>Cliente: ".$m['referencia']."</span>"; 
                                            ?>
                                        </td>
                                        <td><?php echo $m['concepto']; ?></td>
                                        <td><span class="badge bg-gray-lt"><?php echo $m['metodo_pago'] ?? 'N/A'; ?></span></td>
                                        <td class="fw-bold text-dark">$<?php echo number_format($m['monto'], 2); ?></td>
                                    </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr><td colspan="5" class="text-center py-4">No se han registrado pagos el día de hoy.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>