<?php 
include 'config.php';
include 'header.php'; 
?>

<div class="page-wrapper">
    <div class="container-xl mt-4">
        <div class="row align-items-center mb-4">
            <div class="col">
                <h2 class="page-title">Estado de Membresías</h2>
                <div class="text-muted mt-1">Monitoreo de vencimientos automáticos.</div>
            </div>
            <div class="col-auto">
                <a href="nuevo_socio.php" class="btn btn-primary"> + Nuevo Socio</a>
            </div>
        </div>

        <div class="card">
            <div class="table-responsive">
                <table class="table card-table table-vcenter">
                    <thead>
                        <tr>
                            <th>Socio</th>
                            <th>Plan Contratado</th>
                            <th>Fecha Inicio</th>
                            <th>Fecha Vencimiento</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $res = $conexion->query("SELECT s.*, m.nombre as plan FROM socios s JOIN membresias m ON s.id_membresia = m.id_membresia ORDER BY s.fecha_vencimiento ASC");
                        
                        while($row = $res->fetch_assoc()):
                            $vence = strtotime($row['fecha_vencimiento']);
                            $hoy = strtotime(date('Y-m-d'));
                            
                            // Si la fecha de hoy es mayor a la de vencimiento... ¡Ya caducó!
                            $esta_vencido = ($hoy > $vence);
                            $badge_color = $esta_vencido ? 'red' : 'green';
                            $badge_text = $esta_vencido ? 'VENCIDA' : 'ACTIVA';
                        ?>
                        <tr>
                            <td><strong><?php echo $row['nombre']." ".$row['apellido']; ?></strong></td>
                            <td><span class="badge bg-blue-lt"><?php echo $row['plan']; ?></span></td>
                            <td><?php echo date('d M Y', strtotime($row['fecha_registro'])); ?></td>
                            <td><?php echo date('d M Y', $vence); ?></td>
                            <td>
                                <span class="badge bg-<?php echo $badge_color; ?>-lt">
                                    <?php echo $badge_text; ?>
                                </span>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>