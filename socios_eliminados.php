<?php 
include 'config.php';
include 'validar_admin.php'; 
include 'header.php'; 

// Solo los que marcamos como eliminados/bloqueados
$query = "SELECT id_socio, nombre, apellido, correo, qr_codigo, fecha_vencimiento 
          FROM socios 
          WHERE eliminado = 1 
          ORDER BY apellido ASC";
$res = $conexion->query($query);
?>

<div class="page-wrapper">
    <div class="container-xl mt-4">
        <div class="page-header d-flex justify-content-between align-items-center">
            <h2 class="page-title text-danger">Socios Bloqueados (Lista Negra)</h2>
            <a href="socios.php" class="btn btn-outline-secondary">Volver a Socios Activos</a>
        </div>

        <div class="card mt-3">
            <div class="table-responsive">
                <table class="table table-vcenter card-table">
                    <thead>
                        <tr>
                            <th>Socio</th>
                            <th>Correo / Usuario</th>
                            <th>Código QR</th>
                            <th>Estado Final</th>
                            <th class="w-1">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if($res->num_rows > 0): ?>
                            <?php while($s = $res->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $s['nombre'] . " " . $s['apellido']; ?></td>
                                <td class="text-muted"><?php echo $s['correo']; ?></td>
                                <td><code class="text-primary"><?php echo $s['qr_codigo']; ?></code></td>
                                <td><span class="badge bg-danger">BLOQUEADO</span></td>
                                <td>
                                    <a href="reactivar_socio.php?id=<?php echo $s['id_socio']; ?>" 
                                       class="btn btn-success btn-sm"
                                       onclick="return confirm('¿Deseas desbloquear a este socio?')">
                                        <i class="ti ti-check me-1"></i> Reactivar
                                    </a>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">No hay socios bloqueados actualmente.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>