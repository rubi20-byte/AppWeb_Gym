<?php 
include 'config.php';
include 'validar_admin.php'; 
include 'header.php'; 

// Consulta de socios
$socios = $conexion->query("SELECT id_socio, nombre, apellido, fecha_vencimiento FROM socios ORDER BY nombre ASC");

// Consulta de membresías para sacar nombres y precios
$membresias = $conexion->query("SELECT id_membresia, nombre, precio FROM membresias WHERE estado = 'activo'");
?>

<div class="page-wrapper">
    <div class="container-xl mt-4">
        <div class="page-header mb-3">
            <h2 class="page-title text-yellow">Gestión de Pagos y Caja</h2>
        </div>

        <div class="row">
            <div class="col-md-8">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-dark text-white">
                        <ul class="nav nav-tabs card-header-tabs" data-bs-toggle="tabs">
                            <li class="nav-item">
                                <a href="#tab-membresias" class="nav-link active" data-bs-toggle="tab">
                                    <i class="ti ti-user-check me-1"></i> Membresías y Pases
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="#tab-productos" class="nav-link" data-bs-toggle="tab">
                                    <i class="ti ti-package me-1"></i> Venta de Productos
                                </a>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body">
                        <div class="tab-content">
                            <div class="tab-pane active show" id="tab-membresias">
                                <form action="procesar_pago.php" method="POST">
                                    <div class="mb-3">
                                        <label class="form-label font-weight-bold">Seleccionar Socio</label>
                                        <select name="id_socio" class="form-select border-yellow">
                                            <option value="">-- Cliente sin registro / Pase Diario --</option>
                                            <?php while($s = $socios->fetch_assoc()): ?>
                                                <option value="<?php echo $s['id_socio']; ?>">
                                                    <?php echo $s['nombre']." ".$s['apellido']; ?> 
                                                    (Vence: <?php echo date('d/m/Y', strtotime($s['fecha_vencimiento'])); ?>)
                                                </option>
                                            <?php endwhile; ?>
                                        </select>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label font-weight-bold">Concepto (Membresía)</label>
                                            <select name="concepto" id="select_membresia" class="form-select border-yellow" required>
                                                <option value="" data-precio="0">-- Seleccione plan --</option>
                                                <?php while($m = $membresias->fetch_assoc()): ?>
                                                    <option value="<?php echo $m['nombre']; ?>" data-precio="<?php echo $m['precio']; ?>">
                                                        <?php echo $m['nombre']; ?>
                                                    </option>
                                                <?php endwhile; ?>
                                            </select>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label font-weight-bold">Monto a Cobrar</label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-yellow-lt">$</span>
                                                <input type="number" step="0.01" name="monto" id="input_monto" class="form-control" placeholder="0.00" required>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label font-weight-bold">Método de Pago</label>
                                            <select name="metodo_pago" class="form-select border-yellow">
                                                <option value="Efectivo">Efectivo</option>
                                                <option value="Tarjeta">Tarjeta</option>
                                                <option value="Transferencia">Transferencia</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label font-weight-bold">Referencia / Nombre Visitante</label>
                                            <input type="text" name="referencia" class="form-control border-yellow" placeholder="Nombre o # ticket">
                                        </div>
                                    </div>
                                    <button type="submit" class="btn btn-yellow w-100 font-weight-bold">Confirmar y Registrar Pago</button>
                                </form>
                            </div>

                            <div class="tab-pane" id="tab-productos">
                                <p class="text-center text-muted py-4">Módulo de productos próximamente.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card shadow-sm border-0 mb-3">
                    <div class="card-body text-center">
                        <div class="subheader text-muted mb-2">Total en Caja (Hoy)</div>
                        <?php 
                        $hoy = date('Y-m-d');
                        $corte = $conexion->query("SELECT SUM(monto) as total FROM pagos WHERE DATE(fecha_pago) = '$hoy'")->fetch_assoc();
                        ?>
                        <div class="display-5 fw-bold text-green mb-3">
                            $<?php echo number_format($corte['total'] ?? 0, 2); ?>
                        </div>
                        <div class="d-grid gap-2">
                            <a href="corte_caja.php" class="btn btn-outline-primary">
                                <i class="ti ti-list-details me-2"></i> Detalle del Corte
                            </a>
                            <a href="historial_pagos.php" class="btn btn-azure">
                                <i class="ti ti-history me-2"></i> Consultar Historial de Pagos
                            </a>
                        </div>
                    </div>
                </div>
                <div class="alert alert-important alert-info show">
                    <div class="d-flex">
                        <div><i class="ti ti-info-circle icon me-2"></i></div>
                        <div>
                            Usa el botón de <strong>historial</strong> para buscar ingresos de meses o años pasados.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const selectMembresia = document.getElementById('select_membresia');
    const inputMonto = document.getElementById('input_monto');

    selectMembresia.addEventListener('change', function() {
        // Obtenemos el precio del atributo data-precio de la opción seleccionada
        const precioSeleccionado = this.options[this.selectedIndex].getAttribute('data-precio');
        
        // Lo ponemos en el input del monto
        inputMonto.value = precioSeleccionado;
    });
});
</script>

<?php include 'footer.php'; ?>