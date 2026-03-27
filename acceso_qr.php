<?php
include 'validar_admin.php';
include 'config.php';
include 'header.php'; 

// 1. Consulta para Socios Activos - Asegúrate que qr_codigo existe en tu BD
$query_activos = "SELECT id_socio, nombre, apellido, correo, qr_codigo, estado 
                  FROM socios 
                  WHERE eliminado = 0 
                  ORDER BY nombre ASC";
$res_activos = $conexion->query($query_activos);

// 2. Consulta para Socios Bloqueados
$query_bloqueados = "SELECT id_socio, nombre, apellido, correo, qr_codigo, estado 
                     FROM socios 
                     WHERE eliminado = 1 
                     ORDER BY nombre ASC";
$res_bloqueados = $conexion->query($query_bloqueados);
?>

<div class="page-wrapper">
    <div class="container-xl mt-4">
        
        <div class="page-header">
            <h2 class="page-title text-azure">
                <i></i> Control de Acceso: Socios Activos
            </h2>
        </div>

        <div class="card mt-3">
            <div class="table-responsive">
                <table class="table table-vcenter card-table">
                    <thead>
                        <tr>
                            <th>Nombre del Socio</th>
                            <th>Email (Usuario)</th>
                            <th>Código QR</th>
                            <th>Estatus</th>
                            <th class="w-1">Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($res_activos && $res_activos->num_rows > 0): ?>
                            <?php while($s = $res_activos->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($s['nombre'] . " " . $s['apellido']); ?></td>
                                <td class="text-muted"><?php echo htmlspecialchars($s['correo']); ?></td>
                                <td><code class="text-azure"><?php echo htmlspecialchars($s['qr_codigo']); ?></code></td>
                                <td>
                                    <span class="badge bg-green-lt"><?php echo strtoupper($s['estado']); ?></span>
                                </td>
                                <td>
                                    <a href="ver_qr.php?id=<?php echo $s['id_socio']; ?>" class="btn btn-sm btn-azure">Ver QR</a>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="5" class="text-center py-3">No hay socios activos.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <br><hr class="my-4"><br>

        <div class="page-header">
            <h2 class="page-title text-danger">
                <i></i> Historial de Socios Bloqueados
            </h2>
            <p class="text-muted">Estos socios no pueden acceder al sistema pero se conservan por integridad de datos.</p>
        </div>

        <div class="card mt-2">
            <div class="table-responsive">
                <table class="table table-vcenter card-table bg-light">
                    <thead>
                        <tr>
                            <th>Nombre del Socio</th>
                            <th>Email anterior</th>
                            <th>Código anterior</th>
                            <th>Estatus</th>
                            <th class="w-1">Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if($res_bloqueados && $res_bloqueados->num_rows > 0): ?>
                            <?php while($b = $res_bloqueados->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($b['nombre'] . " " . $b['apellido']); ?></td>
                                <td><?php echo htmlspecialchars($b['correo']); ?></td>
                                <td><del><?php echo htmlspecialchars($b['qr_codigo']); ?></del></td>
                                <td><span class="badge bg-danger text-white">BLOQUEADO</span></td>
                                <td>
                                    <a href="reactivar_socio.php?id=<?php echo $b['id_socio']; ?>" 
                                       class="btn btn-sm btn-outline-success"
                                       onclick="return confirm('¿Reactivar acceso para este socio?')">
                                        Reactivar
                                    </a>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="text-center py-3 text-muted">No hay socios bloqueados.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>