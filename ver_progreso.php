<?php 
include 'config.php';
include 'validar_admin.php'; // este archivo valida que seas admin para entrar a esta pagina
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
                    <i class="ti ti-arrow-back me-2"></i>Volver
                </a>
            </div>
        </div>

<div class="card shadow-sm border-0">
    <div class="table-responsive">
        <table class="table card-table table-vcenter text-nowrap table-striped">
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Peso (kg)</th>
                    <th>IMC</th> 
                    <th>Cintura (cm)</th>
                    <th>Cadera (cm)</th>
                    <th>% Grasa</th>
                    <th>% Músculo</th>
                    <th>Evolución</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $res_evals = $conexion->query("SELECT * FROM evaluaciones WHERE id_socio = '$id_socio' ORDER BY fecha_evaluacion DESC");
                $evaluaciones = $res_evals->fetch_all(MYSQLI_ASSOC);

                if(count($evaluaciones) > 0): 
                    foreach($evaluaciones as $index => $ev): 
                        // Lógica para comparar con la evaluación anterior
                        $prev = isset($evaluaciones[$index + 1]) ? $evaluaciones[$index + 1] : null;
                        $diff_peso = $prev ? $ev['peso'] - $prev['peso'] : 0;
                ?>
                <tr>
                    <td>
                        <strong><?php echo date('d/m/Y', strtotime($ev['fecha_evaluacion'])); ?></strong>
                    </td>
                    <td class="font-weight-bold"><?php echo $ev['peso']; ?> kg</td>
                    <td>
                        <span class="badge bg-blue-lt">
                            <?php echo !empty($ev['imc']) ? $ev['imc'] : '---'; ?>
                        </span>
                    </td>
                    <td><?php echo $ev['cintura']; ?> cm</td>
                    <td><?php echo $ev['cadera']; ?> cm</td>
                    <td><?php echo $ev['porcentaje_grasa']; ?>%</td>
                    <td><?php echo $ev['porcentaje_musculo']; ?>%</td>
                    <td>
                        <?php if($prev): ?>
                            <?php if($diff_peso < 0): ?>
                                <span class="text-green"><i class="ti ti-trending-down"></i> Bajó <?php echo abs($diff_peso); ?>kg</span>
                            <?php elseif($diff_peso > 0): ?>
                                <span class="text-red"><i class="ti ti-trending-up"></i> Subió <?php echo $diff_peso; ?>kg</span>
                            <?php else: ?>
                                <span class="text-muted">Sin cambios</span>
                            <?php endif; ?>
                        <?php else: ?>
                            <span class="badge bg-gray-lt">Inicial</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; else: ?>
                    <tr>
                        <td colspan="8" class="text-center py-4">No hay evaluaciones registradas.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include 'footer.php'; ?>