<?php
include 'config.php';
include 'validar_entrenador.php'; 
include 'header_entrenador.php'; 

$id_entrenador = $_SESSION['id_entrenador']; 

$query_clases = "SELECT * FROM clases 
                 WHERE id_entrenador = $id_entrenador 
                 AND estado = 'activo' 
                 ORDER BY fecha_clase ASC, hora_inicio ASC";
$res_clases = $conexion->query($query_clases);
?>

<div class="page-wrapper">
    <div class="container-xl mt-4">
        <div class="page-header mb-4">
            <h2 class="page-title text-purple">
                Mis Clases Programadas
            </h2>
            <p class="text-muted">Gestión de horarios y asistencia para instructores.</p>
        </div>

        <div class="row row-cards">
            <?php if ($res_clases && $res_clases->num_rows > 0): ?>
                <?php while($clase = $res_clases->fetch_assoc()): ?>
                <div class="col-md-6 col-lg-4">
                    <div class="card card-stacked shadow-sm border-purple-subtle">
                        <div class="card-status-start bg-purple"></div>
                        
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                <div class="subheader text-purple fw-bold">
                                    <i class="ti ti-calendar-check me-1"></i> 
                                    <?php echo date('d/m/Y', strtotime($clase['fecha_clase'])); ?>
                                </div>
                                <div class="ms-auto">
                                    <span class="badge bg-purple-lt">
                                        <?php echo date('H:i', strtotime($clase['hora_inicio'])); ?> - <?php echo date('H:i', strtotime($clase['hora_fin'])); ?>
                                    </span>
                                </div>
                            </div>
                            
                            <h3 class="card-title h2 mb-2"><?php echo htmlspecialchars($clase['nombre_clase']); ?></h3>
                            <p class="text-muted small mb-3">
                                <?php echo !empty($clase['descripcion']) ? htmlspecialchars($clase['descripcion']) : 'Clase de entrenamiento grupal.'; ?>
                            </p>

                            <div class="row g-2 small">
                                <div class="col-6 text-muted">
                                    <i class="ti ti-users me-1"></i> Capacidad: <strong><?php echo $clase['capacidad']; ?></strong>
                                </div>
                                <div class="col-6 text-end text-purple">
                                    <i class="ti ti-clock-play me-1"></i> Llenando Cupos...
                                </div>
                            </div>
                        </div>
                        
                        <div class="card-footer bg-purple-lt border-0">
                            <a href="lista_asistencia.php?id=<?php echo $clase['id_clase']; ?>" class="btn btn-purple w-100 fw-bold">
                                <i class="ti ti-clipboard-check me-2"></i> PASAR LISTA
                            </a>
                        </div>
                    </div>
                </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="col-12">
                    <div class="empty border-dashed py-5">
                        <div class="empty-icon text-purple">
                            <i class="ti ti-calendar-off" style="font-size: 3rem;"></i>
                        </div>
                        <p class="empty-title">Sin clases asignadas</p>
                        <p class="empty-subtitle text-muted">
                            Aún no tienes sesiones programadas en el sistema.
                        </p>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>