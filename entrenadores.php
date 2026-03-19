<?php 
include 'config.php';
include 'validar.php'; // este archivo valida que seas admin para entrar a esta pagina
include 'header.php'; 

?>

<div class="page-wrapper">
    <div class="container-xl mt-4">
        <div class="row align-items-center mb-4">
            <div class="col">
                <h2 class="page-title text-purple">Equipo de Entrenadores</h2>
                <div class="text-muted mt-1">Gestión de personal y comisiones.</div>
            </div>
            <div class="col-auto">
                <a href="nuevo_entrenador.php" class="btn btn-purple text-white">
                    <i class="ti ti-plus me-2"></i> Nuevo Entrenador
                </a>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="table-responsive">
                <table class="table card-table table-vcenter text-nowrap">
                    <thead>
                        <tr>
                            <th>Entrenador</th>
                            <th>Contacto</th>
                            <th>Turno / Especialidad</th>
                            <th>Comisión</th>
                            <th class="w-1 text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Consulta a la base de datos
                        $res = $conexion->query("SELECT * FROM entrenadores ORDER BY id_entrenador DESC");
                        while($e = $res->fetch_assoc()):
                        ?>
                        <tr>
                            <td><div class="font-weight-medium"><?php echo $e['nombre']; ?></div></td>
                            <td class="text-muted"><?php echo $e['telefono']; ?><br><small><?php echo $e['correo']; ?></small></td>
                            <td>
                                <div><?php echo $e['especialidad']; ?></div>
                                <div class="badge bg-purple-lt"><?php echo $e['turno']; ?></div>
                            </td>
                            <td class="font-weight-bold text-green">$<?php echo number_format($e['tarifa_comision'], 2); ?></td>
                            <td>
                                <div class="btn-list flex-nowrap justify-content-center">
                                    <a href="seguimiento_entrenador.php?id=<?php echo $e['id_entrenador']; ?>" 
                                       class="btn btn-white btn-icon" 
                                       title="Ver Seguimiento">
                                        <i class="ti ti-users text-purple"></i>
                                    </a>

                                    <a href="editar_entrenador.php?id=<?php echo $e['id_entrenador']; ?>" class="btn btn-white btn-icon"><i class="ti ti-edit text-blue"></i></a>
                                    <a href="eliminar_entrenador.php?id=<?php echo $e['id_entrenador']; ?>" class="btn btn-white btn-icon" onclick="return confirm('¿Eliminar?');"><i class="ti ti-trash text-red"></i></a>
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