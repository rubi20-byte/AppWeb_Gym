<?php
include 'validar_admin.php';
include 'config.php';
include 'header.php';

$id = $_GET['id'];
$res = $conexion->query("SELECT * FROM productos WHERE id_producto = $id");
$p = $res->fetch_assoc();
?>

<div class="page-wrapper">
    <div class="container-xl mt-4">
        <div class="page-header mb-4">
            <div class="row align-items-center">
                <div class="col-auto">
                    <a href="inventario.php" class="btn btn-secondary btn-icon"><i class="ti ti-arrow-left"></i></a>
                </div>
                <div class="col">
                    <h2 class="page-title">Editar Producto: <?php echo $p['nombre']; ?></h2>
                </div>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-status-top bg-yellow"></div>
            <form action="actualizar_producto.php" method="POST" class="card-body">
                <input type="hidden" name="id_producto" value="<?php echo $p['id_producto']; ?>">
                <div class="row g-3">
                    <div class="col-md-8">
                        <label class="form-label">Nombre</label>
                        <input type="text" name="nombre" class="form-control" value="<?php echo $p['nombre']; ?>" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Categoría</label>
                        <select name="categoria" class="form-select">
                            <option value="Bebidas" <?php if($p['categoria']=='Bebidas') echo 'selected'; ?>>Bebidas</option>
                            <option value="Suplementos" <?php if($p['categoria']=='Suplementos') echo 'selected'; ?>>Suplementos</option>
                            <option value="Accesorios" <?php if($p['categoria']=='Accesorios') echo 'selected'; ?>>Accesorios</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Precio Venta</label>
                        <input type="number" step="0.01" name="p_venta" class="form-control" value="<?php echo $p['precio_venta']; ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Stock Actual</label>
                        <input type="number" name="stock" class="form-control" value="<?php echo $p['stock']; ?>" required>
                    </div>
                </div>
                <div class="form-footer">
                    <button type="submit" class="btn btn-yellow w-100">Actualizar Producto</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php include 'footer.php'; ?>