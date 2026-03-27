<?php
session_start();
include 'config.php';
include 'header_socio.php';

$id_socio = $_SESSION['id_socio'] ?? 0;

if ($id_socio == 0) {
    die("Error: No se encontró la sesión del socio.");
}

/** 1. DATOS DEL SOCIO Y SU OBJETIVO (Tabla: socios y rutinas) **/
$sql_socio = "SELECT s.sexo, s.fecha_nacimiento, r.nombre_rutina, r.objetivo as meta_texto
              FROM socios s 
              LEFT JOIN rutinas r ON s.id_rutina = r.id_rutina 
              WHERE s.id_socio = '$id_socio'";
$res_socio = $conexion->query($sql_socio);
$socio = $res_socio->fetch_assoc();

// Cálculo de edad
$edad = 0;
if (!empty($socio['fecha_nacimiento'])) {
    $f_nac = new DateTime($socio['fecha_nacimiento']);
    $hoy = new DateTime();
    $edad = $hoy->diff($f_nac)->y;
}

/** 2. DATOS FÍSICOS (Tabla: evaluaciones) **/
$sql_eval = "SELECT peso, talla FROM evaluaciones WHERE id_socio = '$id_socio' ORDER BY id_evaluacion DESC LIMIT 1";
$res_eval = $conexion->query($sql_eval);
$eval = $res_eval->fetch_assoc();

$peso  = $eval['peso'] ?? 70;
$talla = $eval['talla'] ?? 170;
$sexo  = $socio['sexo'] ?? 'Mujer';

/** 3. CÁLCULO DE META CALÓRICA **/
if ($sexo == 'Mujer') {
    $tmb = (10 * $peso) + (6.25 * $talla) - (5 * $edad) - 161;
} else {
    $tmb = (10 * $peso) + (6.25 * $talla) - (5 * $edad) + 5;
}

$meta_diaria = round($tmb * 1.55);

// Ajuste según objetivo de la rutina
$obj_texto = $socio['meta_texto'] ?? '';
if (stripos($obj_texto, 'Pérdida') !== false) { $meta_diaria -= 500; }
if (stripos($obj_texto, 'Ganar') !== false) { $meta_diaria += 400; }
?>

<div class="page-wrapper">
    <div class="container-xl mt-4">
        <div class="card mb-4 shadow-sm border-0 bg-primary-lt">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="mb-1 text-primary">Mi Plan Semanal</h2>
                    <p class="text-muted mb-0">
                        <strong>Edad:</strong> <?php echo $edad; ?> años | 
                        <strong>Objetivo:</strong> <?php echo $socio['nombre_rutina'] ?? 'Sin rutina'; ?>
                    </p>
                </div>
                <div class="text-end">
                    <div class="h1 mb-0 fw-bold text-primary"><?php echo $meta_diaria; ?></div>
                    <div class="text-uppercase text-muted small">Meta Diaria (kcal)</div>
                </div>
            </div>
        </div>

        <div class="row row-cards">
            <?php 
            $dias = ['Lunes', 'Martes', 'Miercoles', 'Jueves', 'Viernes', 'Sabado', 'Domingo'];
            foreach($dias as $dia): 
                // CONTADOR DE CALORÍAS POR DÍA
                $sql_suma = "SELECT SUM(calorias_calculadas) as total 
                             FROM planes_socio_semanal 
                             WHERE id_socio = '$id_socio' AND dia_semana = '$dia'";
                $res_suma = $conexion->query($sql_suma);
                $consumido = $res_suma->fetch_assoc()['total'] ?? 0;
            ?>
            <div class="col-md-4 mb-3">
                <div class="card h-100 shadow-sm border-light">
                    <div class="card-header d-flex justify-content-between align-items-center bg-white">
                        <h3 class="card-title text-blue fw-bold"><?php echo $dia; ?></h3>
                        <span class="badge bg-green-lt">
                            <?php echo round($consumido); ?> / <?php echo $meta_diaria; ?> kcal
                        </span>
                    </div>
                    <div class="card-body p-0">
                        <?php 
                        $momentos = ['Desayuno', 'Comida', 'Cena', 'Snack 1'];
                        foreach($momentos as $m):
                            $sql_p = "SELECT p.*, a.nombre 
                                      FROM planes_socio_semanal p 
                                      JOIN alimentos a ON p.id_alimento = a.id_alimento 
                                      WHERE p.id_socio = '$id_socio' AND p.dia_semana = '$dia' AND p.momento = '$m'";
                            $res_p = $conexion->query($sql_p);
                            $item = $res_p->fetch_assoc();
                        ?>
                        <div class="p-3 border-bottom list-group-item-action">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <small class="text-muted fw-bold text-uppercase" style="font-size: 0.6rem;"><?php echo $m; ?></small>
                                    <div class="fw-bold text-dark">
                                        <?php echo $item ? htmlspecialchars($item['nombre']) : '<span class="text-muted fw-normal">Sin asignar</span>'; ?>
                                    </div>
                                    <?php if($item): ?>
                                        <small class="text-blue"><?php echo $item['cantidad_gramos']; ?>g — <strong><?php echo round($item['calorias_calculadas']); ?> kcal</strong></small>
                                    <?php endif; ?>
                                </div>
                                <div class="d-flex gap-1">
                                    <?php if($item): ?>
                                        <a href="eliminar_alimentos.php?id=<?php echo $item['id_plan']; ?>" 
                                           class="btn btn-icon btn-sm btn-outline-danger rounded-circle"
                                           onclick="return confirm('¿Quitar este alimento?');">
                                            <i class="ti ti-minus"></i>
                                        </a>
                                    <?php endif; ?>
                                    <button class="btn btn-icon btn-sm btn-outline-primary rounded-circle" 
                                            onclick="abrirModalPlan('<?php echo $dia; ?>', '<?php echo $m; ?>')">
                                        <i class="ti ti-plus"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<div class="modal modal-blur fade" id="modal-asignar" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form class="modal-content" action="guardar_plan_socio.php" method="POST">
            <div class="modal-header">
                <h5 class="modal-title">Asignar Alimento: <span id="txt-dia"></span> - <span id="txt-momento"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="dia" id="input-dia">
                <input type="hidden" name="momento" id="input-momento">
                <div class="mb-3">
                    <label class="form-label">Alimento</label>
                    <select name="id_alimento" class="form-select" required>
                        <option value="">Selecciona...</option>
                        <?php 
                        $alms = $conexion->query("SELECT * FROM alimentos ORDER BY nombre ASC");
                        while($a = $alms->fetch_assoc()){
                            echo "<option value='{$a['id_alimento']}'>{$a['nombre']} ({$a['calorias_por_100g']} kcal/100g)</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Gramos</label>
                    <input type="number" name="gramos" class="form-control" required placeholder="Ej. 150">
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary ms-auto">Guardar</button>
            </div>
        </form>
    </div>
</div>

<script>
function abrirModalPlan(dia, momento) {
    document.getElementById('txt-dia').innerText = dia;
    document.getElementById('txt-momento').innerText = momento;
    document.getElementById('input-dia').value = dia;
    document.getElementById('input-momento').value = momento;
    new bootstrap.Modal(document.getElementById('modal-asignar')).show();
}
</script>

<?php include 'footer.php'; ?>