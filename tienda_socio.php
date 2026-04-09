<?php
include 'config.php';
include 'validar_socio.php';
include 'header_socio.php'; 

$query = "SELECT * FROM productos WHERE estado = 'activo' AND stock > 0 ORDER BY nombre ASC";
$res_productos = $conexion->query($query);
?>

<?php if(isset($_GET['status']) && $_GET['status'] == 'success'): ?>
    <div class="alert alert-important alert-success alert-dismissible shadow-sm mb-4" role="alert">
        <div class="d-flex">
            <div><i class="ti ti-check icon alert-icon"></i></div>
            <div>
                ¡Compra exitosa! Has adquirido <strong><?php echo htmlspecialchars($_GET['item']); ?></strong>.
            </div>
        </div>
        <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
    </div>
<?php endif; ?>

<div class="page-wrapper">
    <div class="container-xl mt-4">
        <div class="page-header mb-4">
            <div class="row align-items-center">
                <div class="col">
                    <h2 class="page-title text-yellow text-uppercase">
                        Productos Disponibles
                    </h2>
                    <p class="text-muted small">Adquiere lo que necesites y recógelo en recepción.</p>
                </div>
            </div>
        </div>

        <div class="row row-cards">
            <?php if ($res_productos && $res_productos->num_rows > 0): ?>
                <?php while($p = $res_productos->fetch_assoc()): ?>
                <div class="col-sm-6 col-lg-3">
                    <div class="card card-stacked shadow-sm border-0">
                        <div class="card-status-top bg-yellow"></div>
                        
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                <span class="badge bg-yellow-lt"><?php echo htmlspecialchars($p['categoria']); ?></span>
                                <div class="ms-auto h2 mb-0 text-yellow fw-bold">
                                    $<?php echo number_format($p['precio_venta'], 2); ?>
                                </div>
                            </div>
                            
                            <h3 class="card-title fw-bold mb-1"><?php echo htmlspecialchars($p['nombre']); ?></h3>
                            
                            <div class="text-muted small mb-3">
                                <i class="ti ti-box me-1"></i> Stock: <?php echo $p['stock']; ?> unidades
                            </div>

                            <button onclick="confirmarCompra(<?php echo $p['id_producto']; ?>, '<?php echo $p['nombre']; ?>')" 
                                    class="btn btn-yellow w-100 fw-bold">
                                <i class="ti ti-shopping-cart-plus me-2"></i> COMPRAR
                            </button>
                        </div>
                    </div>
                </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="col-12 text-center py-5">
                    <div class="empty border-dashed">
                        <div class="empty-icon text-muted"><i class="ti ti-package-off"></i></div>
                        <p class="empty-title">Sin productos por ahora</p>
                        <p class="empty-subtitle text-muted">Vuelve más tarde para ver las novedades del gimnasio.</p>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
function confirmarCompra(id, nombre) {
    if (confirm('¿Quieres comprar "' + nombre + '"? El monto se registrará en tu cuenta.')) {
        window.location.href = 'procesar_venta_socio.php?id=' + id;
    }
}
</script>

<?php include 'footer.php'; ?>