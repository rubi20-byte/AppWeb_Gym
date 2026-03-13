<?php 
include 'config.php';
include 'header.php'; 

// 1. Obtener el ID del entrenador desde la URL
$id_ent = isset($_GET['id']) ? $_GET['id'] : '';

// 2. Consultar el nombre del entrenador
$res_ent = $conexion->query("SELECT nombre FROM entrenadores WHERE id_entrenador = '$id_ent'");
$ent = $res_ent->fetch_assoc();
?>

<div class="page-wrapper">
    <div class="container-xl mt-4">
        <div class="row align-items-center mb-4">
            <div class="col">
                <h2 class="page-title">Socios Asignados</h2>
                <div class="text-muted mt-1">Entrenador: <strong><?php echo $ent['nombre']; ?></strong></div>
            </div>
            <div class="col-auto">
                <a href="entrenadores.php" class="btn btn-secondary">
                    <i class="ti ti-arrow-back me-2"></i>Volver
                </a>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="table-responsive">
                <table class="table card-table table-vcenter text-nowrap">
                    <thead>
                        <tr>
                            <th>Nombre del Socio</th>
                            <th>Estado</th>
                            <th>Teléfono</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        // 3. Consulta básica que ya sabemos que funciona
                        $res = $conexion->query("SELECT * FROM socios WHERE id_entrenador = '$id_ent'");

                        if($res && $res->num_rows > 0): 
                            while($s = $res->fetch_assoc()): 
                        ?>
                        <tr>
                            <td><strong><?php echo $s['nombre'] . " " . $s['apellido']; ?></strong></td>
                            <td>
                                <span class="badge bg-green-lt"><?php echo strtoupper($s['estado']); ?></span>
                            </td>
                            <td class="text-muted">
                                <?php echo $s['telefono']; ?>
                            </td>
                            <td class="text-center">
                                <div class="btn-list justify-content-center">
                                    <a href="nueva_evaluacion.php?id_socio=<?php echo $s['id_socio']; ?>&id_ent=<?php echo $id_ent; ?>" 
                                       class="btn btn-warning btn-sm" title="Nueva Evaluación">
                                        <i class="ti ti-plus me-1"></i> Evaluar
                                    </a>
                                    <a href="ver_progreso.php?id=<?php echo $s['id_socio']; ?>" 
                                       class="btn btn-info btn-sm" title="Ver Historial">
                                        <i class="ti ti-history me-1"></i> Historial
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endwhile; else: ?>
                            <tr>
                                <td colspan="5" class="text-center py-4">
                                    No hay socios asignados a este entrenador.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>