<?php 
include 'config.php';
include 'validar_admin.php'; // este archivo valida que seas admin para entrar a esta pagina
include 'header.php'; 

?>

<div class="page-wrapper">
    <div class="container-xl mt-4">
        <div class="row align-items-center mb-4">
            <div class="col"><h2 class="page-title text-cyan">Planes y Membresías</h2></div>
            <div class="col-auto">
                <a href="nueva_membresia.php" class="btn btn-cyan">
                    <i class="ti ti-plus me-2"></i> Nuevo Plan
                </a>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="table-responsive">
                <table class="table card-table table-vcenter">
                    <thead>
                        <tr>
                            <th>Nombre del Plan</th>
                            <th>Duración</th>
                            <th>Precio</th>
                            <th>Estado</th>
                            <th class="w-1">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $res = $conexion->query("SELECT * FROM membresias ORDER BY precio ASC");
                        while($row = $res->fetch_assoc()):
                            $status_color = ($row['estado'] == 'activo') ? 'green' : 'red';
                        ?>
                        <tr>
                            <td><div class="font-weight-medium"><?php echo $row['nombre']; ?></div></td>
                            <td><?php echo $row['duracion_meses']; ?> Mes(es)</td>
                            <td class="text-muted">$<?php echo number_format($row['precio'], 2); ?></td>
                            <td><span class="badge bg-<?php echo $status_color; ?>-lt"><?php echo strtoupper($row['estado']); ?></span></td>
                            <td>
                                <div class="btn-list flex-nowrap">
                                    <a href="editar_membresia.php?id=<?php echo $row['id_membresia']; ?>" class="btn btn-white btn-icon"><i class="ti ti-edit text-blue"></i></a>
                                    <a href="eliminar_membresia.php?id=<?php echo $row['id_membresia']; ?>" class="btn btn-white btn-icon" onclick="return confirm('¿Eliminar este plan?');"><i class="ti ti-trash text-red"></i></a>
                                </div>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php include 'footer.php'; ?>