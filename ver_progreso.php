<?php 
include 'config.php';
include 'header.php'; 

$id_socio = isset($_GET['id']) ? $_GET['id'] : '';

// Consultar datos del socio
$res_socio = $conexion->query("SELECT nombre, id_entrenador FROM socios WHERE id_socio = '$id_socio'");
$socio = $res_socio->fetch_assoc();

if (!$socio) {
    echo "<div class='container mt-4'><div class='alert alert-danger'>Socio no encontrado.</div></div>";
    include 'footer.php'; exit;
}
?>

<div class="page-wrapper">
    <div class="container-xl mt-4">
        <div class="row align-items-center mb-4">
            <div class="col">
                <h2 class="page-title">Historial de Evaluaciones</h2>
                <div class="text-muted mt-1">Socio: <strong><?php echo $socio['nombre']; ?></strong></div>
            </div>
            <div class="col-auto">
                <a href="seguimiento_entrenador.php?id=<?php echo $socio['id_entrenador']; ?>" class="btn btn-secondary">
                    <i class="ti ti-arrow-back me-2"></i>Volver al Seguimiento
                </a>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="table-responsive">
                <table class="table card-table table-vcenter text-nowrap">
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Peso (kg)</th>
                            <th>Cintura (cm)</th>
                            <th>Cadera (cm)</th>
                            <th>% Grasa</th>
                            <th>% Músculo</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $res_evals = $conexion->query("SELECT * FROM evaluaciones WHERE id_socio = '$id_socio' ORDER BY fecha_evaluacion DESC");

                        if($res_evals && $res_evals->num_rows > 0): 
                            while($ev = $res_evals->fetch_assoc()): 
                        ?>
                        <tr>
                            <td>
                                <strong><?php echo date('d/m/Y', strtotime($ev['fecha_evaluacion'])); ?></strong>
                            </td>
                            <td><?php echo $ev['peso']; ?> kg</td>
                            <td><?php echo $ev['cintura']; ?> cm</td>
                            <td><?php echo $ev['cadera']; ?> cm</td>
                            <td><?php echo $ev['porcentaje_grasa']; ?>%</td>
                            <td><?php echo $ev['porcentaje_musculo']; ?>%</td>
                        </tr>
                        <?php endwhile; else: ?>
                            <tr>
                                <td colspan="6" class="text-center py-4">Este socio aún no tiene evaluaciones registradas.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>