<?php
include 'config.php'; // Asegúrate de incluir tu conexión
include 'validar_admin.php';
include 'header.php';

// Consultar proveedores activos
$query_prov = "SELECT id_proveedor, nombre FROM proveedores WHERE estado = 'activo' ORDER BY nombre ASC";
$res_prov = $conexion->query($query_prov);
?>

<div class="page-wrapper">
    <div class="container-xl mt-4">
        <div class="page-header d-print-none mb-4">
            <div class="row align-items-center">
                <div class="col-auto">
                    <a href="inventario.php" class="btn btn-secondary btn-icon" title="Volver al inventario">
                        <i class="ti ti-arrow-left"></i>
                    </a>
                </div>
                <div class="col">
                    <h2 class="page-title text-uppercase">Registrar Nuevo Producto</h2>
                    <div class="text-muted small mt-1">Asegúrate de completar todos los campos marcados con *</div>
                </div>
            </div>
        </div>

        <div class="row row-cards">
            <div class="col-12">
                <form action="guardar_producto.php" method="POST" class="card shadow-sm border-0">
                    <div class="card-status-top bg-yellow"></div>
                    
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label required">Nombre del Producto</label>
                                <input type="text" name="nombre" class="form-control" placeholder="Ej: Creatina Monohidratada 500g" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Categoría</label>
                                <select name="categoria" class="form-select">
                                    <option value="Bebidas">Bebidas</option>
                                    <option value="Suplementos">Suplementos</option>
                                    <option value="Accesorios">Accesorios</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Proveedor *</label>
                                <select name="id_proveedor" class="form-select" required>
                                    <option value="">Seleccionar...</option>
                                    <?php while($p = $res_prov->fetch_assoc()): ?>
                                        <option value="<?php echo $p['id_proveedor']; ?>"><?php echo $p['nombre']; ?></option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                            
                            <div class="col-md-4">
                                <label class="form-label required">Precio de Compra</label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" step="0.01" name="p_compra" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label required">Precio de Venta</label>
                                <div class="input-group">
                                    <span class="input-group-text text-yellow fw-bold">$</span>
                                    <input type="number" step="0.01" name="p_venta" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Stock Inicial</label>
                                <input type="number" name="stock" class="form-control" value="0">
                            </div>

                            <div class="col-12 mt-4">
                                <button type="submit" class="btn btn-yellow w-100 fw-bold py-2">
                                    <i class="ti ti-device-floppy me-2"></i> Guardar Producto
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php include 'footer.php'; ?>