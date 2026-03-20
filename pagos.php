<?php 
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
include 'validar_admin.php'; 
include 'config.php';
include 'header.php'; 

// Consultamos los socios activos para el buscador
$sql_socios = "SELECT id_socio, nombre, fecha_vencimiento FROM socios WHERE estado = 'activo' ORDER BY nombre ASC";
$res_socios = $conexion->query($sql_socios);
?>

<div class="page-wrapper">
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <h2 class="page-title text-pink">
                        Pagos y Caja
                    </h2>
                </div>
            </div>
        </div>
    </div>

    <div class="page-body">
        <div class="container-xl">
            <div class="row row-cards">
                
                <div class="col-md-7">
                    <form action="procesar_pago.php" method="POST" class="card shadow-sm">
                        <div class="card-header bg-pink-lt">
                            <h3 class="card-title">Registrar Nuevo Movimiento</h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <label class="form-label required">Seleccionar Socio</label>
                                    <select name="id_socio" class="form-select" required>
                                        <option value="">-- Seleccione un socio --</option>
                                        <?php while($s = $res_socios->fetch_assoc()): ?>
                                            <option value="<?php echo $s['id_socio']; ?>">
                                                <?php echo $s['nombre']; ?> (Vence: <?php echo date('d/m/Y', strtotime($s['fecha_vencimiento'])); ?>)
                                            </option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label required">Monto a Recibir ($)</label>
                                    <div class="input-group">
                                        <span class="input-group-text">$</span>
                                        <input type="number" name="monto" class="form-control" placeholder="0.00" step="0.01" required>
                                    </div>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label required">Concepto</label>
                                    <select name="concepto" class="form-select" required>
                                        <option value="Mensualidad">Mensualidad (30 días)</option>
                                        <option value="Trimestral">Trimestre (90 días)</option>
                                        <option value="Anual">Anual (365 días)</option>
                                        <option value="Familiar">Familiar (5 personas, 30 días)</option>
                                        <option value="Inscripción">Inscripción</option>
                                        <option value="Venta de Producto">Venta de Producto / Suplemento</option>
                                        <option value="Otro">Otro concepto</option>
                                    </select>
                                </div>

                                <div class="col-md-12 mb-3">
                                    <label class="form-label">Método de Pago</label>
                                    <div class="form-selectgroup form-selectgroup-boxes d-flex">
                                        <label class="form-selectgroup-item flex-fill">
                                            <input type="radio" name="metodo_pago" value="Efectivo" class="form-selectgroup-input" checked>
                                            <span class="form-selectgroup-label d-flex align-items-center p-3">
                                                <span class="me-3"><i class="ti ti-cash fs-1"></i></span>
                                                <span class="form-selectgroup-label-content text-start">
                                                    <span class="form-selectgroup-title">Efectivo</span>
                                                </span>
                                            </span>
                                        </label>
                                        <label class="form-selectgroup-item flex-fill mx-2">
                                            <input type="radio" name="metodo_pago" value="Tarjeta" class="form-selectgroup-input">
                                            <span class="form-selectgroup-label d-flex align-items-center p-3">
                                                <span class="me-3"><i class="ti ti-credit-card fs-1"></i></span>
                                                <span class="form-selectgroup-label-content text-start">
                                                    <span class="form-selectgroup-title">Tarjeta</span>
                                                </span>
                                            </span>
                                        </label>
                                        <label class="form-selectgroup-item flex-fill">
                                            <input type="radio" name="metodo_pago" value="Transferencia" class="form-selectgroup-input">
                                            <span class="form-selectgroup-label d-flex align-items-center p-3">
                                                <span class="me-3"><i class="ti ti-building-bank fs-1"></i></span>
                                                <span class="form-selectgroup-label-content text-start">
                                                    <span class="form-selectgroup-title">Transferencia</span>
                                                </span>
                                            </span>
                                        </label>
                                    </div>
                                </div>

                                <div class="col-md-12 mb-3">
                                    <label class="form-label">Referencia de Pago</label>
                                    <input type="text" name="referencia" class="form-control" placeholder="Número de transacción, referencia bancaria, etc.">
                                </div>
                            </div>
                        </div>
                        <div class="card-footer text-end">
                            <button type="submit" class="btn btn-pink btn w-100">
                                <i class="ti ti-check me-2"></i> Confirmar y Registrar Pago
                            </button>
                        </div>
                    </form>
                </div>

                <div class="col-md-5">
                    <div class="card shadow-sm">
                        <div class="card-header"><h3 class="card-title">Resumen de Hoy</h3></div>
                        <div class="card-body p-4 text-center">
                            <?php 
                                $hoy = date('Y-m-d');
                                $q = $conexion->query("SELECT SUM(monto) as total FROM pagos WHERE DATE(fecha_pago) = '$hoy'");
                                $total = $q->fetch_assoc()['total'] ?? 0;
                            ?>
                            <div class="text-muted mb-2">Total en Caja</div>
                            <div class="h1 fw-bold text-success" style="font-size: 3rem;">$<?php echo number_format($total, 2); ?></div>
                        </div>
                        <div class="card-footer">
                            <div class="row align-items-center">
                                <div class="col">Ingresos registrados hoy</div>
                                <div class="col-auto">
                                    <a href="corte_caja.php" class="btn btn-ghost-dark">Ver historial <i class="ti ti-arrow-right ms-1"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-3">
                        <a href="adeudos.php" class="btn btn-outline-danger w-100 p-3 shadow-sm">
                            <i class="ti ti-alert-triangle me-2"></i> Ver Control de Adeudos
                        </a>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>