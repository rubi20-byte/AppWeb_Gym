<?php 
include 'config.php';
include 'validar.php'; // este archivo valida que seas admin para entrar a esta pagina
include 'header.php'; 


$id = $_GET['id'];
$socio_res = $conexion->query("SELECT nombre, apellido FROM socios WHERE id_socio = $id");
$s = $socio_res->fetch_assoc();
?>

<div class="page-wrapper">
    <div class="container-xl mt-4">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Historial de Membresías: <?php echo $s['nombre']." ".$s['apellido']; ?></h3>
            </div>
            <div class="table-responsive">
                <table class="table card-table table-vcenter text-nowrap">
                    <thead>
                        <tr>
                            <th>Plan</th>
                            <th>Fecha Inicio</th>
                            <th>Fecha Fin</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Unimos con la tabla membresias para traer el nombre del plan
                        $hist = $conexion->query("SELECT h.*, m.nombre as nombre_plan 
                                                FROM socios_membresias h 
                                                JOIN membresias m ON h.id_membresia = m.id_membresia 
                                                WHERE h.id_socio = $id 
                                                ORDER BY h.fecha_inicio DESC");
                        while($h = $hist->fetch_assoc()):
                        ?>
                        <tr>
                            <td><?php echo $h['nombre_plan']; ?></td>
                            <td><?php echo date('d/m/Y', strtotime($h['fecha_inicio'])); ?></td>
                            <td><?php echo date('d/m/Y', strtotime($h['fecha_fin'])); ?></td>
                            <td><span class="badge bg-green-lt text-uppercase"><?php echo $h['estado']; ?></span></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="mt-3">
            <a href="socios.php" class="btn btn-secondary">Regresar</a>
        </div>
    </div>
</div>
<?php include 'footer.php'; ?>