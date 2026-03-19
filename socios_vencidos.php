<?php
include 'config.php';
include 'validar.php'; // este archivo valida que seas admin para entrar a esta pagina
include 'header.php'; 

// Esta es la consulta que filtra a los morosos
$sql = "SELECT * FROM socios WHERE estado = 'Vencido' OR fecha_vencimiento < CURDATE()";
$res = $conexion->query($sql);
?>

<div class="page-wrapper">
    <div class="container-xl mt-4">
        <div class="page-header mb-4">
            <h2 class="page-title text-danger">Socios Vencidos / Morosos</h2>
        </div>

        <div class="card shadow-sm border-0">
            <div class="table-responsive">
                <table class="table table-vcenter card-table table-striped">
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Vencimiento</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if($res && $res->num_rows > 0): ?>
                            <?php while($s = $res->fetch_assoc()): ?>
                            <tr>
                                <td><strong><?php echo $s['nombre']; ?></strong></td>
                                <td class="text-red"><?php echo date('d/m/Y', strtotime($s['fecha_vencimiento'])); ?></td>
                                <td><span class="badge bg-red-lt">Vencido</span></td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" class="text-center py-4 text-muted">No hay socios vencidos por ahora.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>