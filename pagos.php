<?php 
include 'config.php';
include 'validar_admin.php'; 
include 'header.php'; 

$socios = $conexion->query("SELECT id_socio, nombre, apellido, fecha_vencimiento FROM socios WHERE eliminado = 0 ORDER BY nombre ASC");
$membresias = $conexion->query("SELECT id_membresia, nombre, precio FROM membresias WHERE estado = 'activo'");
$productos = $conexion->query("SELECT p.id_producto, p.nombre, p.precio_venta, p.stock 
                               FROM productos p 
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
                                        <label class="form-label fw-bold">Seleccionar Socio</label>
                                        <select name="id_socio" class="form-select border-yellow">
                                            <option value="">-- Cliente sin registro / Pase Diario --</option>
                                            <?php $socios->data_seek(0); while($s = $socios->fetch_assoc()): ?>
                                                <option value="<?php echo $s['id_socio']; ?>">
                                                    <?php echo $s['nombre']." ".$s['apellido']; ?> 
                                                </option>
                                            <?php endwhile; ?>
                                        </select>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label fw-bold">Membresía</label>
                                            <select name="concepto" id="select_membresia" class="form-select border-yellow" required>
                                                <option value="" data-precio="0">-- Seleccione --</option>
                                                <?php while($m = $membresias->fetch_assoc()): ?>
                                                    <option value="<?php echo $m['nombre']; ?>" data-precio="<?php echo $m['precio']; ?>">
                                                        <?php echo $m['nombre']; ?>
                                                    </option>
                                                <?php endwhile; ?>
                                            </select>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label fw-bold">Descuento (%)</label>
                                            <input type="number" id="input_descuento" name="descuento" class="form-control border-yellow" value="0" min="0" max="100">
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label fw-bold">Monto Final</label>
                                            <input type="number" step="0.01" name="monto" id="input_monto" class="form-control fw-bold" readonly value="0.00">
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Método de Pago</label>
                                        <select name="metodo_pago" class="form-select border-yellow">
                                            <option value="Efectivo">Efectivo</option>
                                            <option value="Tarjeta">Tarjeta</option>
                                            <option value="Transferencia">Transferencia</option>
                                        </select>
                                    </div>
                                    <button type="submit" class="btn btn-yellow w-100 fw-bold">Confirmar Membresía</button>
                                </form>
                            </div>

                            <div class="tab-pane" id="tab-productos">
                                <form action="procesar_pago.php" method="POST">
                                    <input type="hidden" name="tipo_pago" value="producto">
                                    <input type="hidden" name="concepto" id="concepto_prod_input">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Asignar a Socio (Opcional)</label>
                                        <select name="id_socio" class="form-select border-blue">
                                            <option value="">-- Venta General --</option>
                                            <?php $socios->data_seek(0); while($s = $socios->fetch_assoc()): ?>
                                                <option value="<?php echo $s['id_socio']; ?>"><?php echo $s['nombre']." ".$s['apellido']; ?></option>
                                            <?php endwhile; ?>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Producto</label>
                                        <select name="id_producto" id="select_prod" class="form-select border-blue" required>
                                            <option value="" data-precio="0" data-nombre="">-- Buscar producto --</option>
                                            <?php $productos->data_seek(0); while($p = $productos->fetch_assoc()): ?>
                                                <option value="<?php echo $p['id_producto']; ?>" data-nombre="<?php echo $p['nombre']; ?>" data-precio="<?php echo $p['precio_venta']; ?>">
                                                    <?php echo $p['nombre']; ?> ($<?php echo $p['precio_venta']; ?>)
                                                </option>
                                            <?php endwhile; ?>
                                        </select>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label fw-bold">Cantidad</label>
                                            <input type="number" name="cantidad" id="cant_prod" class="form-control border-blue" value="1" min="1">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label fw-bold">Total</label>
                                            <input type="number" step="0.01" name="monto" id="total_prod" class="form-control fw-bold border-blue" readonly value="0.00">
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Método de Pago</label>
                                        <select name="metodo_pago" class="form-select border-blue">
                                            <option value="Efectivo">Efectivo</option>
                                            <option value="Tarjeta">Tarjeta</option>
                                            <option value="Transferencia">Transferencia</option>
                                        </select>
                                    </div>
                                    <button type="submit" class="btn btn-primary w-100 fw-bold">Vender Producto</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card shadow-sm border-0 mb-3 text-center">
                    <div class="card-body">
                        <div class="subheader text-muted mb-2">Total en Caja (Hoy)</div>
                        <?php 
                        $hoy = date('Y-m-d');
                        $corte = $conexion->query("SELECT SUM(monto) as total FROM pagos WHERE DATE(fecha_pago) = '$hoy'")->fetch_assoc();
                        ?>
                        <div class="display-5 fw-bold text-green mb-3">$<?php echo number_format($corte['total'] ?? 0, 2); ?></div>
                        <a href="historial_pagos.php" class="btn btn-azure w-100">Ver Historial</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal modal-blur fade" id="modal-ticket" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-body text-center" id="ticket-print">
                <div class="mb-3">
                    <h3 class="mb-1">TICKET DE VENTA</h3>
                    <div class="text-muted small">GYM SISTEMA</div>
                </div>
                <hr class="my-2" style="border-top: 1px dashed #000;">
                <div id="ticket-detalle">
                    </div>
                <hr class="my-2" style="border-top: 1px dashed #000;">
                <div class="fw-bold">¡Gracias por su compra!</div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-link link-secondary me-auto" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" onclick="imprimirTicket()">Imprimir</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    // --- Lógica de Cálculos (Membresías) ---
    const selectMembresia = document.getElementById('select_membresia');
    const inputMonto = document.getElementById('input_monto');
    const inputDescuento = document.getElementById('input_descuento');

    function calcularTotalMembresia() {
        let precioBase = parseFloat(selectMembresia.options[selectMembresia.selectedIndex].getAttribute('data-precio')) || 0;
        let porcentajeDescuento = parseFloat(inputDescuento.value) || 0;
        let total = precioBase - (precioBase * (porcentajeDescuento / 100));
        inputMonto.value = total.toFixed(2);
    }
    if(selectMembresia) selectMembresia.addEventListener('change', calcularTotalMembresia);
    if(inputDescuento) inputDescuento.addEventListener('input', calcularTotalMembresia);

    // --- Lógica de Cálculos (Productos) ---
    const sPro = document.getElementById('select_prod');
    const cPro = document.getElementById('cant_prod');
    const tPro = document.getElementById('total_prod');
    const cInp = document.getElementById('concepto_prod_input');

    function calcProd() {
        let opt = sPro.options[sPro.selectedIndex];
        let precio = parseFloat(opt.getAttribute('data-precio')) || 0;
        let nombre = opt.getAttribute('data-nombre') || '';
        let cant = parseInt(cPro.value) || 0;
        tPro.value = (precio * cant).toFixed(2);
        cInp.value = "Producto: " + nombre + " (x" + cant + ")";
    }
    if(sPro) sPro.addEventListener('change', calcProd);
    if(cPro) cPro.addEventListener('input', calcProd);

    // --- Lógica del Modal de Ticket (LA CORRECCIÓN) ---
    const urlParams = new URLSearchParams(window.location.search);
    const pagoId = urlParams.get('pago_exitoso');

    // IMPORTANTE: Si pagoId es '0' o nulo, no hace nada. Debe ser > 0
    if (pagoId && pagoId !== '0') {
        const modalElemento = document.getElementById('modal-ticket');
        const modalTicket = new bootstrap.Modal(modalElemento);
        
        // Primero intentamos traer los datos
        fetch('obtener_pago_ticket.php?id=' + pagoId)
            .then(response => {
                if (!response.ok) throw new Error('Error en el servidor');
                return response.json();
            })
            .then(data => {
                const cliente = data.nombre ? (data.nombre + ' ' + data.apellido) : 'Público General';
                document.getElementById('ticket-detalle').innerHTML = `
                    <div class="d-flex justify-content-between mb-1"><span>Folio:</span> <b>#${data.id_pago}</b></div>
                    <div class="d-flex justify-content-between mb-1"><span>Fecha:</span> <span>${data.fecha_pago}</span></div>
                    <div class="d-flex justify-content-between mb-3"><span>Cliente:</span> <span class="text-uppercase">${cliente}</span></div>
                    <div class="text-start small text-muted border-top pt-2">CONCEPTO:</div>
                    <div class="text-start fw-bold mb-3">${data.concepto}</div>
                    <div class="h1 fw-bold text-center my-3">$ ${parseFloat(data.monto).toFixed(2)}</div>
                `;
                modalTicket.show();
            })
            .catch(error => {
                console.error(error);
                // Plan B: Si falla el fetch, avisamos que se guardó pero no se puede mostrar el ticket
                document.getElementById('ticket-detalle').innerHTML = `<div class="alert alert-danger">Pago guardado (#${pagoId}), pero no se pudo cargar el detalle del ticket.</div>`;
                modalTicket.show();
            });
    }
});

function imprimirTicket() {
    var contenido = document.getElementById('ticket-print').innerHTML;
    var ventana = window.open('', 'PRINT', 'height=600,width=400');
    ventana.document.write('<html><head><title>Imprimir Ticket</title><style>body{font-family:sans-serif;text-align:center;padding:20px;}.d-flex{display:flex;justify-content:space-between;}</style></head><body>');
    ventana.document.write(contenido);
    ventana.document.write('</body></html>');
    ventana.document.close();
    ventana.focus();
    ventana.print();
    ventana.close();
}
</script>

<?php include 'footer.php'; ?>