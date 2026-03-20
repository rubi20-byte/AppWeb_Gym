<?php 
include 'config.php';
include 'validar_admin.php'; 
include 'header.php'; 

$hoy = date('Y-m-d');

// Consulta para desglosar el dinero de HOY
$res_corte = $conexion->query("SELECT 
    SUM(CASE WHEN metodo_pago = 'Efectivo' THEN monto ELSE 0 END) as efectivo,
    SUM(CASE WHEN metodo_pago = 'Tarjeta' THEN monto ELSE 0 END) as tarjeta,
    SUM(CASE WHEN metodo_pago = 'Transferencia' THEN monto ELSE 0 END) as transferencia,
    SUM(monto) as total_dia,
    COUNT(id_pago) as num_operaciones
    FROM pagos 
    WHERE DATE(fecha_pago) = '$hoy'");

$c = $res_corte->fetch_assoc();
?>

<div class="page-wrapper">
    <div class="container-xl mt-4">
        <div class="page-header mb-4 text-center">
            <h2 class="page-title text-primary">Corte de Caja Diario</h2>
            <p class="text-muted">Resumen de ingresos correspondientes al: <strong><?php echo date('d/m/Y'); ?></strong></p>
        </div>

        <div class="row row-cards justify-content-center">
            <div class="col-md-8">
                <div class="card card-lg shadow-sm border-0 bg-primary-lt">
                    <div class="card-body text-center">
                        <div class="text-uppercase font-weight-bold text-primary mb-2">Total Recaudado Hoy</div>
                        <div class="display-3 fw-bold text-dark">$<?php echo number_format($c['total_dia'] ?? 0, 2); ?></div>
                        <div class="text-muted"><?php echo $c['num_operaciones'] ?? 0; ?> operaciones registradas</div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card shadow-sm h-100">
                    <div class="card-header bg-light">
                        <h3 class="card-title text-dark font-weight-bold">Desglose de Efectivo y Otros</h3>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <div class="d-flex align-items-center mb-1">
                                <div class="font-weight-bold"><i class="ti ti-cash me-1 text-success"></i> Efectivo</div>
                                <div class="ms-auto fw-bold text-success">$<?php echo number_format($c['efectivo'] ?? 0, 2); ?></div>
                            </div>
                        </div>
                        <hr>
                        <div class="mb-3">
                            <div class="d-flex align-items-center mb-1">
                                <div class="font-weight-bold"><i class="ti ti-credit-card me-1 text-blue"></i> Tarjeta</div>
                                <div class="ms-auto fw-bold text-blue">$<?php echo number_format($c['tarjeta'] ?? 0, 2); ?></div>
                            </div>
                        </div>
                        <hr>
                        <div class="mb-3">
                            <div class="d-flex align-items-center mb-1">
                                <div class="font-weight-bold"><i class="ti ti-building-bank me-1 text-purple"></i> Transferencia</div>
                                <div class="ms-auto fw-bold text-purple">$<?php echo number_format($c['transferencia'] ?? 0, 2); ?></div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer bg-yellow-lt text-center py-2">
                        <small class="text-dark font-weight-bold">Verificar saldo contra cuenta bancaria y caja física</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-12 text-center">
                <button onclick="window.print();" class="btn btn-outline-dark">
                    <i class="ti ti-printer me-2"></i> Imprimir Corte del Día
                </button>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>