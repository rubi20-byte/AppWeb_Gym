<?php
include 'validar_admin.php';
include 'config.php';
include 'header.php';

$res = $conexion->query("SELECT * FROM productos WHERE estado = 'activo' ORDER BY nombre ASC");
?>

<div class="page-wrapper">
    <div class="container-xl mt-4">
        <div class="page-header mb-4">
            <div class="row align-items-center">
                <div class="col">
                    <h2 class="page-title text-yellow">Gestión de Inventario</h2>
                </div>
                <div class="col-auto">
                    <a href="nuevo_producto.php" class="btn btn-yellow">
                        <i class="ti ti-plus me-2"></i> Agregar Producto
                    </a>
                </div>
            </div>
        </div>

        <div class="card border-yellow-subtle">
            <div class="table-responsive">
                <table class="table table-vcenter card-table">
                    <thead>
                        <tr>
                            <th>Producto</th>
                            <th>Categoría</th>
                            <th>Precio Venta</th>
                            <th>Stock</th>
                            <th class="w-1"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($p = $res->fetch_assoc()): ?>
                        <tr>
                            <td class="fw-bold text-dark"><?php echo htmlspecialchars($p['nombre']); ?></td>
                            <td><span class="badge bg-yellow-lt"><?php echo $p['categoria']; ?></span></td>
                            <td class="fw-bold">$<?php echo number_format($p['precio_venta'], 2); ?></td>
                            <td>
                            <?php if($p['stock'] <= $p['stock_minimo']): ?>
                                <span class="badge bg-red-lt text-red fw-bold">
                                    <i class="ti ti-alert-triangle me-1"></i> <?php echo $p['stock']; ?>
                                </span>
                                <?php else: ?>
                                    <span class="badge bg-green-lt"><?php echo $p['stock']; ?></span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end">
                                    <div class="btn-list flex-nowrap">
                                        <a href="editar_producto.php?id=<?php echo $p['id_producto']; ?>" class="btn btn-icon btn-outline" title="Editar">
                                            <i class="ti ti-edit text-yellow"></i>
                                        </a>
                                        <a href="eliminar_producto.php?id=<?php echo $p['id_producto']; ?>" onclick="return confirm('¿Estás seguro de que deseas eliminar este producto?');" class="btn btn-icon btn-outline" title="Eliminar">
                                            <i class="ti ti-trash text-danger"></i>
                                        </a>
                                    </div>
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