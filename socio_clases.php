<?php
session_start();
include 'config.php';
include 'header_socio.php';
$id_socio = $_SESSION['id_socio']; // ID del socio logueado
?>

<div class="page-wrapper">
    <div class="container-xl mt-4">
        <h2 class="mb-4">Reserva tu Clase</h2>
        <div class="row row-cards">
            <?php
            // Consultamos clases y contamos cuántos inscritos van en la tabla reservas_clases
            $sql = "SELECT c.*, 
                    (SELECT COUNT(*) FROM reservas_clases r 
                     WHERE r.id_clase = c.id_clase AND r.estado_reserva = 'Confirmada') as inscritos
                    FROM clases c WHERE c.estado = 'Activo'";
            $res = $conexion->query($sql);

            while($c = $res->fetch_assoc()):
                $disponibles = $c['capacidad'] - $c['inscritos'];
                $lleno = ($disponibles <= 0);
            ?>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <div class="subheader"><?php echo $c['horario']; ?></div>
                        <h3 class="card-title"><?php echo $c['nombre_clase']; ?></h3>
                        <p class="text-muted small"><?php echo $c['descripcion']; ?></p>
                        
                        <div class="mt-3">
                            <div class="d-flex justify-content-between mb-1">
                                <span class="small">Ocupación</span>
                                <span class="small font-weight-bold"><?php echo $c['inscritos']; ?>/<?php echo $c['capacidad']; ?></span>
                            </div>
                            <div class="progress progress-sm">
                                <div class="progress-bar bg-primary" style="width: <?php echo ($c['inscritos']/$c['capacidad'])*100; ?>%"></div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer text-end">
                        <a href="guardar_reserva.php?id=<?php echo $c['id_clase']; ?>" 
                           class="btn <?php echo $lleno ? 'btn-warning' : 'btn-primary'; ?> w-100">
                           <?php echo $lleno ? 'Entrar a Lista de Espera' : 'Reservar mi Lugar'; ?>
                        </a>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
    </div>
</div>