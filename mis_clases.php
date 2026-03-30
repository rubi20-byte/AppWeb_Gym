<?php
include 'validar_socio.php';
include 'config.php';
include 'header_socio.php';

$id_usuario = $_SESSION['id_socio'] ?? $_SESSION['id_usuario'];
$fecha_hoy = date('Y-m-d');
$hora_actual = date('H:i:s');
?>

<div class="page-wrapper">
    <div class="container-xl mt-4">
        <div class="page-header mb-4">
            <h2 class="page-title text-primary">Mis Clases Reservadas</h2>
            <p class="text-muted">Aquí puedes gestionar las clases a las que te has inscrito.</p>
        </div>

        <div class="row row-cards">
            <?php
            // Traemos las reservas del socio que aún no han pasado
            $sql = "SELECT r.id_reserva, c.nombre_clase, c.fecha_clase, c.hora_inicio, c.descripcion, e.nombre as entrenador 
                    FROM reservas_clases r
                    JOIN clases c ON r.id_clase = c.id_clase
                    LEFT JOIN entrenadores e ON c.id_entrenador = e.id_entrenador
                    WHERE r.id_socio = '$id_usuario' 
                    AND c.fecha_clase >= '$fecha_hoy'
                    AND NOT (c.fecha_clase = '$fecha_hoy' AND c.hora_fin < '$hora_actual')
                    ORDER BY c.fecha_clase ASC, c.hora_inicio ASC";

            $mis_clases = $conexion->query($sql);

            if ($mis_clases->num_rows > 0):
                while($r = $mis_clases->fetch_assoc()):
            ?>
            <div class="col-md-4">
                <div class="card shadow-sm border-0" style="border-radius: 12px;">
                    <div class="card-body">
                        <div class="subheader text-azure mb-2">
                            <?php echo date('d/m/Y', strtotime($r['fecha_clase'])); ?> - <?php echo date('h:i A', strtotime($r['hora_inicio'])); ?>
                        </div>
                        <h3 class="card-title fw-bold text-uppercase"><?php echo $r['nombre_clase']; ?></h3>
                        <div class="text-muted small mb-3">Con: <?php echo $r['entrenador'] ?? 'Staff'; ?></div>
                        
                        <a href="cancelar_clase.php?id=<?php echo $r['id_reserva']; ?>" 
                           class="btn btn-outline-danger w-100 py-2" 
                           onclick="return confirm('¿Seguro que quieres darte de baja de esta clase? El lugar quedará disponible para otro socio.')"
                           style="border-radius: 8px;">
                            <i class="ti ti-trash me-1"></i> Cancelar Reserva
                        </a>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
            <?php else: ?>
            <div class="col-12 text-center py-5">
                <div class="text-muted">Aún no tienes reservaciones activas.</div>
                <a href="clases_agenda.php" class="btn btn-primary mt-3">Ver catálogo de clases</a>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>