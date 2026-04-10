<?php
include 'config.php';
include 'validar_entrenador.php';
include 'header_entrenador.php';

$id_profe = $_SESSION['id_entrenador'];

$total_socios = $conexion->query("SELECT COUNT(*) as total FROM socios WHERE id_entrenador = $id_profe")->fetch_assoc()['total'];
?>

<div class="page-wrapper">
    <div class="page-header d-print-none text-white bg-purple-lt p-4 mb-4 shadow-sm">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <h2 class="page-title">¡Qué onda, Coach <?php echo $_SESSION['nombre']; ?>!</h2>
                    <div class="text-muted mt-1">Hoy es <?php echo date('d/m/Y'); ?>. A darle con todo.</div>
                </div>
            </div>
        </div>
    </div>

    <div class="page-body">
        <div class="container-xl">
            <div class="row row-deck row-cards">
                
                <div class="col-sm-6 col-lg-4">
                    <div class="card shadow-sm border-0">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="subheader">Mis Socios Activos</div>
                            </div>
                            <div class="h1 mb-3"><?php echo $total_socios; ?> Alumnos</div>
                            <div class="progress progress-sm">
                                <div class="progress-bar bg-purple" style="width: 100%"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 mt-4">
                    <div class="card shadow-sm">
                        <div class="card-header bg-white">
                            <h3 class="card-title fw-bold">Seguimiento de Mis Socios</h3>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-vcenter card-table">
                                <thead class="bg-light">
                                    <tr>
                                        <th>Socio</th>
                                        <th>Contacto</th>
                                        <th>Estatus</th>
                                        <th class="w-1">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $query = "SELECT * FROM socios WHERE id_entrenador = $id_profe AND estado = 'activo'";
                                    $res = $conexion->query($query);
                                    
                                    if($res->num_rows > 0){
                                        while($socio = $res->fetch_assoc()){
                                    ?>
                                    <tr>
                                        <td>
                                            <div class="d-flex py-1 align-items-center">
                                                <span class="avatar me-2 rounded-circle bg-purple-lt"><?php echo substr($socio['nombre'], 0, 1); ?></span>
                                                <div class="flex-fill">
                                                    <div class="font-weight-medium"><?php echo $socio['nombre'] . " " . $socio['apellido']; ?></div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="text-muted"><?php echo $socio['correo']; ?></div>
                                            <div class="text-muted small"><?php echo $socio['telefono']; ?></div>
                                        </td>
                                        <td>
                                            <span class="badge bg-success-lt">En Entrenamiento</span>
                                        </td>
                                        <td>
                                            <div class="btn-list flex-nowrap">
                                                <a href="asignar_plan.php?id=<?php echo $socio['id_socio']; ?>" class="btn btn-lime btn-sm" title="Nutrición Manual">
                                                    <i class="ti ti-meat me-1"></i> Dieta
                                                </a>
                                                <a href="evaluar_socio_entrenador.php?id=<?php echo $socio['id_socio']; ?>" class="btn btn-purple btn-sm" title="Nueva Evaluación">
                                                    <i class="ti ti-chart-line me-1"></i> Evaluar
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php 
                                        } 
                                    } else {
                                        echo "<tr><td colspan='4' class='text-center text-muted p-4'>Aún no tienes socios asignados. ¡Dile al Admin!</td></tr>";
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>