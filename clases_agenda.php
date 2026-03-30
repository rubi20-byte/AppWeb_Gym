<?php
include 'validar_socio.php';
include 'config.php';
include 'header_socio.php';

// Validamos el ID del socio desde la sesión
$id_usuario = $_SESSION['id_socio'] ?? $_SESSION['id_usuario']; 
$fecha_hoy = date('Y-m-d');
$hora_actual = date('H:i:s');
?>

<style>
    .card-hover:hover {
        transform: translateY(-4px);
        transition: all 0.3s ease;
        box-shadow: 0 8px 15px rgba(0,0,0,0.1) !important;
    }
    .badge-card {
        position: absolute; 
        top: 12px; 
        right: 12px; 
        z-index: 10; 
        border-radius: 8px; 
        font-size: 0.7rem; 
        font-weight: 600;
    }
</style>

<div class="page-wrapper">
    <div class="container-xl mt-4">
        <div class="page-header mb-4 text-center">
            <h2 class="page-title text-azure justify-content-center" style="font-size: 1.8rem;">
                Cartelera de Clases
            </h2>
        </div>

        <div class="row row-cards">
            <?php
            // Usamos id_socio para filtrar las que YA NO deben aparecer
            $sql = "SELECT c.*, e.nombre as entrenador 
                    FROM clases c 
                    LEFT JOIN entrenadores e ON c.id_entrenador = e.id_entrenador 
                    WHERE c.estado = 'activo' 
                    AND c.fecha_clase >= '$fecha_hoy'
                    AND NOT (c.fecha_clase = '$fecha_hoy' AND c.hora_fin < '$hora_actual')
                    AND c.id_clase NOT IN (
                        SELECT id_clase 
                        FROM reservas_clases 
                        WHERE id_socio = '$id_usuario'
                    )
                    ORDER BY c.fecha_clase ASC, c.hora_inicio ASC";

            $clases = $conexion->query($sql);
            
            if ($clases && $clases->num_rows > 0):
                while($c = $clases->fetch_assoc()):
                    $id_clase = $c['id_clase'];
                    $fecha_clase = $c['fecha_clase']; 
                    
                    // Conteo de inscritos actual
                    $inscritos_q = $conexion->query("SELECT COUNT(*) as total FROM reservas_clases 
                                                   WHERE id_clase = '$id_clase' AND fecha_clase = '$fecha_clase'")->fetch_assoc();
                    $total_inscritos = $inscritos_q['total'];
                    
                    // Lógica de Estados
                    $clase_terminada = ($fecha_clase == $fecha_hoy && $c['hora_fin'] < $hora_actual);
                    if ($clase_terminada) {
                        $estado_clase = "TERMINADA"; $badge_color = "bg-secondary";
                    } elseif ($total_inscritos >= $c['capacidad']) {
                        $estado_clase = "CERRADA"; $badge_color = "bg-danger";
                    } else {
                        $estado_clase = "ABIERTA"; $badge_color = "bg-success";
                    }
            ?>
            <div class="col-md-4">
                <div class="card card-stacked shadow-sm border-0 card-hover" style="border-radius: 15px; overflow: hidden;">
                    <div class="badge <?php echo $badge_color; ?> badge-card p-2 shadow-sm">
                        <i class="ti ti-point-filled me-1"></i> <?php echo $estado_clase; ?>
                    </div>

                    <div class="card-body pt-4">
                        <div class="d-flex align-items-center mb-3">
                            <div class="bg-azure-lt p-2 rounded-3 me-3 text-center" style="min-width: 55px;">
                                <div class="text-uppercase fw-bold text-azure" style="font-size: 0.6rem;">FECHA</div>
                                <div class="fw-bold fs-3"><?php echo date('d/m', strtotime($c['fecha_clase'])); ?></div>
                            </div>
                            <div>
                                <div class="subheader mb-0 text-dark fw-bold" style="font-size: 0.85rem;">
                                    <i class="ti ti-clock me-1 text-azure"></i> <?php echo date('h:i A', strtotime($c['hora_inicio'])); ?>
                                </div>
                                <div class="text-muted small">
                                    <i class="ti ti-user-check me-1"></i> <?php echo $c['entrenador'] ?? 'Instructor Gral.'; ?>
                                </div>
                            </div>
                        </div>

                        <h3 class="card-title h2 mb-2 text-uppercase fw-bolder" style="letter-spacing: -0.5px;">
                            <?php echo $c['nombre_clase']; ?>
                        </h3>
                        <p class="text-secondary small mb-3" style="min-height: 40px;"><?php echo $c['descripcion']; ?></p>
                        
                        <div class="mt-4 p-2 bg-light rounded-2">
                            <div class="d-flex justify-content-between mb-1 small fw-bold">
                                <span class="text-muted">DISPONIBILIDAD</span>
                                <span class="<?php echo ($total_inscritos >= $c['capacidad']) ? 'text-danger' : 'text-primary'; ?>">
                                    <?php echo $total_inscritos; ?> / <?php echo $c['capacidad']; ?>
                                </span>
                            </div>
                            <div class="progress progress-sm" style="height: 6px;">
                                <?php $porcentaje = ($total_inscritos / $c['capacidad']) * 100; ?>
                                <div class="progress-bar bg-azure" style="width: <?php echo $porcentaje; ?>%"></div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card-footer bg-white border-0 pt-0 pb-3">
                        <?php if($clase_terminada): ?>
                            <button class="btn btn-outline-secondary w-100 py-2" style="border-radius: 10px;" disabled>Clase Finalizada</button>
                        <?php elseif($total_inscritos >= $c['capacidad']): ?>
                            <button class="btn btn-danger-lt w-100 py-2" style="border-radius: 10px;" disabled>Cupo Agotado</button>
                        <?php else: ?>
                            <a href="reservar.php?id=<?php echo $id_clase; ?>" class="btn btn-azure w-100 py-2 shadow-sm" style="border-radius: 10px; border: none;">
                                <i class="ti ti-plus me-1"></i> Reservar Ahora
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
            <?php else: ?>
                <div class="col-12 text-center py-5">
                    <div class="empty">
                        <div class="empty-img"><i class="ti ti-calendar-off display-1 text-muted"></i></div>
                        <p class="empty-title">¡No hay clases pendientes!</p>
                        <p class="empty-subtitle text-muted">Ya estás inscrito en todas las clases disponibles o no hay cartelera nueva.</p>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>