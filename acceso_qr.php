<?php
include 'validar_admin.php';
include 'config.php';
include 'header.php'; 

// 1. Consulta para Socios Activos
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

// 3. Consulta para Entrenadores (Ajustada a tu SQL real)
$query_entrenadores = "SELECT id_entrenador, nombre, correo, password, estado 
                       FROM entrenadores 
                       WHERE estado = 'activo' 
                       ORDER BY nombre ASC";
$entrenadores_acceso = $conexion->query($query_entrenadores);
?>

<div class="page-wrapper">
    <div class="container-xl mt-4">
        
        <h2 class="page-title text-azure mb-3">Control de Acceso: Socios Activos</h2>
        <div class="card mb-5">
            <div class="table-responsive">
                <table class="table table-vcenter card-table">
                    <thead>
                        <tr>
                            <th>Nombre</th><th>Email</th><th>Contraseña</th><th>Estatus</th><th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($res_activos && $res_activos->num_rows > 0): ?>
                            <?php while($s = $res_activos->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($s['nombre'] . " " . $s['apellido']); ?></td>
                                <td><?php echo htmlspecialchars($s['correo']); ?></td>
                                <td><code class="text-azure"><?php echo htmlspecialchars($s['qr_codigo']); ?></code></td>
                                <td><span class="badge bg-green-lt">ACTIVO</span></td>
                                <td><a href="ver_qr.php?id=<?php echo $s['id_socio']; ?>" class="btn btn-sm btn-azure">Ver QR</a></td>
                            </tr>
                            <?php endwhile; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <h2 class="page-title text-danger mb-3">Historial de Socios Bloqueados</h2>
        <div class="card mb-5">
            <div class="table-responsive">
                <table class="table table-vcenter card-table bg-light">
                    <thead>
                        <tr>
                            <th>Nombre</th><th>Email</th><th>Contraseña</th><th>Estatus</th><th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if($res_bloqueados && $res_bloqueados->num_rows > 0): ?>
                            <?php while($b = $res_bloqueados->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($b['nombre'] . " " . $b['apellido']); ?></td>
                                <td><del><?php echo htmlspecialchars($b['qr_codigo']); ?></del></td>
                                <td><span class="badge bg-danger text-white">BLOQUEADO</span></td>
                                <td><a href="reactivar_socio.php?id=<?php echo $b['id_socio']; ?>" class="btn btn-sm btn-outline-success">Reactivar</a></td>
                            </tr>
                            <?php endwhile; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <h2 class="page-title text-primary mb-3">Control de Acceso: Entrenadores</h2>
        <div class="card shadow-sm">
            <div class="table-responsive">
                <table class="table table-vcenter card-table table-striped">
                    <thead>
                        <tr>
                            <th>Entrenador</th>
                            <th>Email</th>
                            <th>Llave de Acceso</th>
                            <th>Estatus</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($entrenadores_acceso && $entrenadores_acceso->num_rows > 0): ?>
                            <?php while($ent = $entrenadores_acceso->fetch_assoc()): ?>
                            <tr>
                                <td class="fw-bold"><?php echo htmlspecialchars($ent['nombre']); ?></td>
                                <td class="text-muted"><?php echo htmlspecialchars($ent['correo']); ?></td>
                                <td>
                                    <?php if(strlen($ent['password']) > 20): ?>
                                        <span class="badge bg-warning-lt" title="Contraseña Encriptada">Segura (Hash)</span>
                                    <?php else: ?>
                                        <code class="bg-blue-lt px-2 py-1 rounded small"><?php echo htmlspecialchars($ent['password'] ?? 'Sin asignar'); ?></code>
                                    <?php endif; ?>
                                </td>
                                <td><span class="badge bg-green-lt"><?php echo strtoupper($ent['estado']); ?></span></td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="4" class="text-center py-4">No hay entrenadores registrados.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>
<?php include 'footer.php'; ?>