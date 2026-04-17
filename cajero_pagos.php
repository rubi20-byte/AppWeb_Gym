<?php 
include 'config.php';
include 'validar_caja.php'; // Validación de acceso para Recepción y Admin

// El session_start() ya está en validar_caja.php, no es necesario repetirlo aquí
include 'header_caja.php'; 

// 1. Consultas
$socios = $conexion->query("SELECT id_socio, nombre, apellido, fecha_vencimiento FROM socios WHERE eliminado = 0 ORDER BY nombre ASC");
$membresias = $conexion->query("SELECT id_membresia, nombre, precio FROM membresias WHERE estado = 'activo'");

// 2. Consulta de productos (Agregamos el JOIN para traer el nombre del proveedor)
$productos = $conexion->query("SELECT p.id_producto, p.nombre, p.precio_venta, p.stock, prov.nombre as nom_proveedor 
                               FROM productos p 
                               LEFT JOIN proveedores prov ON p.id_proveedor = prov.id_proveedor
                               WHERE p.stock > 0 ORDER BY p.nombre ASC");
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
                                    <input type="hidden" name="tipo_pago" value="membresia">
                                    <div class="mb-3">
                                        <label class="form-label font-weight-bold">Seleccionar Socio</label>
                                        <select name="id_socio" class="form-select border-yellow">
                                            <option value="">-- Cliente sin registro / Pase Diario --</option>
                                            <?php 
                                            $socios->data_seek(0);
                                            while($s = $socios->fetch_assoc()): ?>
                                                <option value="<?php echo $s['id_socio']; ?>">
                                                    <?php echo $s['nombre']." ".$s['apellido']; ?> 
                                                    (Vence: <?php echo date('d/m/Y', strtotime($s['fecha_vencimiento'])); ?>)
                                                </option>
                                            <?php endwhile; ?>
                                        </select>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label font-weight-bold">Membresía</label>
                                            <select name="concepto" id="select_membresia" class="form-select border-yellow" required>
                                                <option value="" data-precio="0">-- Seleccione plan --</option>
                                                <?php while($m = $membresias->fetch_assoc()): ?>
                                                    <option value="<?php echo $m['nombre']; ?>" data-precio="<?php echo $m['precio']; ?>">
                                                        <?php echo $m['nombre']; ?>
                                                    </option>
                                                <?php endwhile; ?>
                                            </select>
                                        </div>
                                        
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label font-weight-bold">Descuento (%)</label>
                                            <input type="number" id="input_descuento" name="descuento" class="form-control border-yellow" value="0" min="0" max="100">
                                        </div>

                                        <div class="col-md-4 mb-3">
                                            <label class="form-label font-weight-bold">Monto Final</label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-yellow-lt">$</span>
                                                <input type="number" step="0.01" name="monto" id="input_monto" class="form-control fw-bold text-dark" readonly value="0.00">
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
                                <form action="procesar_pago.php" method="POST" id="form-productos">
                                    <input type="hidden" name="tipo_pago" value="producto">
                                    
                                    <div class="mb-3">
                                        <label class="form-label font-weight-bold">Asignar a Socio (Opcional)</label>
                                        <select name="id_socio" class="form-select border-blue">
                                            <option value="">-- Venta General al Público --</option>
                                            <?php 
                                            $socios->data_seek(0);
                                            while($s = $socios->fetch_assoc()): ?>
                                                <option value="<?php echo $s['id_socio']; ?>">
                                                    <?php echo $s['nombre']." ".$s['apellido']; ?>
                                                </option>
                                            <?php endwhile; ?>
                                        </select>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label font-weight-bold">Seleccionar Producto</label>
                                        <select name="id_producto" id="select_prod" class="form-select border-blue" required>
                                            <option value="" data-precio="0" data-stock="0" data-prov="Sin proveedor">-- Buscar producto --</option>
                                            <?php 
                                            $productos->data_seek(0);
                                            while($p = $productos->fetch_assoc()): ?>
                                                <option value="<?php echo $p['id_producto']; ?>" 
                                                        data-precio="<?php echo $p['precio_venta']; ?>" 
                                                        data-stock="<?php echo $p['stock']; ?>"
                                                        data-prov="<?php echo htmlspecialchars($p['nom_proveedor'] ?? 'Sin proveedor'); ?>">
                                                    <?php echo $p['nombre']; ?> (Stock: <?php echo $p['stock']; ?>) - $<?php echo number_format($p['precio_venta'], 2); ?>
                                                </option>
                                            <?php endwhile; ?>
                                        </select>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label font-weight-bold text-muted">Proveedor</label>
                                            <input type="text" name="proveedor" id="prov_display" class="form-control bg-light" readonly placeholder="Automático">
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label font-weight-bold">Cantidad</label>
                                            <input type="number" name="cantidad" id="cant_prod" class="form-control border-blue" value="1" min="1">
                                            <small id="stock_help" class="form-hint text-danger" style="display:none;">Excede el stock</small>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label font-weight-bold">Total a Pagar</label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-blue-lt">$</span>
                                                <input type="number" step="0.01" name="monto" id="total_prod" class="form-control fw-bold" readonly value="0.00">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label font-weight-bold">Método de Pago</label>
                                        <select name="metodo_pago" class="form-select border-blue">
                                            <option value="Efectivo">Efectivo</option>
                                            <option value="Tarjeta">Tarjeta</option>
                                            <option value="Transferencia">Transferencia</option>
                                        </select>
                                    </div>
                                    <button type="submit" id="btn_vender" class="btn btn-primary w-100 font-weight-bold">Vender Producto</button>
                                </form>
                            </div>

                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card shadow-sm border-0 mb-3">
                    <div class="card-body text-center">
                        <div class="subheader text-muted mb-2">Mis ventas (Hoy)</div>
                        <?php 
                        $hoy = date('Y-m-d');
                        $corte = $conexion->query("SELECT SUM(monto) as total FROM pagos WHERE DATE(fecha_pago) = '$hoy'")->fetch_assoc();
                        ?>
                        <div class="display-5 fw-bold text-green mb-3">
                            $<?php echo number_format($corte['total'] ?? 0, 2); ?>
                        </div>
                        <div class="d-grid gap-2">
                            <a href="cajero_historial.php" class="btn btn-azure">Ver Mi Historial</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    // --- Lógica Membresías ---
    const selectMembresia = document.getElementById('select_membresia');
    const inputMonto = document.getElementById('input_monto');
    const inputDescuento = document.getElementById('input_descuento');

    function calcularTotal() {
        let precioBase = parseFloat(selectMembresia.options[selectMembresia.selectedIndex].getAttribute('data-precio')) || 0;
        let porcentajeDescuento = parseFloat(inputDescuento.value) || 0;
        if(porcentajeDescuento < 0) porcentajeDescuento = 0;
        if(porcentajeDescuento > 100) porcentajeDescuento = 100;
        let total = precioBase - (precioBase * (porcentajeDescuento / 100));
        inputMonto.value = total.toFixed(2);
    }
    selectMembresia.addEventListener('change', calcularTotal);
    inputDescuento.addEventListener('input', calcularTotal);

    // --- Lógica Productos (Con Proveedor y Stock) ---
    const sPro = document.getElementById('select_prod');
    const cPro = document.getElementById('cant_prod');
    const tPro = document.getElementById('total_prod');
    const bVen = document.getElementById('btn_vender');
    const hSto = document.getElementById('stock_help');
    const pDis = document.getElementById('prov_display');

    function calcProd() {
        let opt = sPro.options[sPro.selectedIndex];
        let precio = parseFloat(opt.getAttribute('data-precio')) || 0;
        let stockMax = parseInt(opt.getAttribute('data-stock')) || 0;
        let prov = opt.getAttribute('data-prov');
        let cant = parseInt(cPro.value) || 0;

        // Mostrar proveedor
        pDis.value = prov;

        if (cant > stockMax) {
            cPro.classList.add('is-invalid');
            hSto.style.display = 'block';
            bVen.disabled = true;
            tPro.value = "0.00";
        } else {
            cPro.classList.remove('is-invalid');
            hSto.style.display = 'none';
            bVen.disabled = (cant <= 0 || isNaN(precio));
            tPro.value = (precio * cant).toFixed(2);
        }
    }

    sPro.addEventListener('change', function() {
        let stockMax = parseInt(this.options[this.selectedIndex].getAttribute('data-stock')) || 0;
        cPro.max = stockMax; 
        calcProd();
    });

    cPro.addEventListener('input', calcProd);
});
</script>

<?php include 'footer.php'; ?>