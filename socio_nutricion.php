<?php
include 'validar_socio.php';
include 'config.php';
include 'header_socio.php';

$id_socio = $_SESSION['id_socio'] ?? 0;

if ($id_socio == 0) {
    die("Error: No se encontró la sesión del socio.");
}

/** 1. DATOS DEL SOCIO Y SU OBJETIVO **/
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

/** 2. DATOS FÍSICOS **/
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

// Ajuste según objetivos
$obj_texto = $socio['meta_texto'] ?? '';
$filtro_sugerencia = "Mantener"; 

if (stripos($obj_texto, 'Pérdida') !== false || stripos($obj_texto, 'Bajar') !== false) { 
    $meta_diaria -= 500; 
    $filtro_sugerencia = "Bajar Peso";
} elseif (stripos($obj_texto, 'Ganar') !== false || stripos($obj_texto, 'Musculo') !== false) { 
    $meta_diaria += 400; 
    $filtro_sugerencia = "Ganar Musculo";
}

?>

<div class="page-wrapper">
    <div class="container-xl mt-4">
        
        <div class="mb-4">
            <h3 class="mb-3 text-primary">Sugerencias para tu objetivo: <?php echo $filtro_sugerencia; ?></h3>
            <div class="d-flex overflow-auto gap-3 pb-2" style="scrollbar-width: thin; -webkit-overflow-scrolling: touch;">
                <?php 
                $sql_sug = "SELECT * FROM sugerencias_comidas WHERE objetivo = '$filtro_sugerencia'";
                $res_sug = $conexion->query($sql_sug);
                
                if ($res_sug && $res_sug->num_rows > 0):
                    while($sug = $res_sug->fetch_assoc()):
                ?>
                <div class="card shadow-sm border-0" style="min-width: 180px; cursor: pointer;" 
                     onclick="recomendarComida('<?php echo $sug['id_sugerencia']; ?>', '<?php echo addslashes($sug['nombre_alimento']); ?>', '<?php echo $sug['momento']; ?>')">
                    <div class="card-body p-3 text-center">
                        <div class="avatar avatar-md bg-primary-lt mb-2">
                            <i class="ti ti-tools-kitchen-2"></i>
                        </div>
                        <div class="fw-bold text-truncate"><?php echo $sug['nombre_alimento']; ?></div>
                        <small class="text-muted d-block"><?php echo $sug['momento']; ?></small>
                        <small class="badge bg-blue-lt mt-1"><?php echo $sug['calorias']; ?> kcal</small>
                    </div>
                </div>
                <?php endwhile; else: ?>
                    <p class="text-muted small">No hay sugerencias configuradas para este objetivo.</p>
                <?php endif; ?>
            </div>
        </div>

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

        <a href="limpiar_plan.php" class="btn btn-danger btn-sm mb-3" onclick="return confirm('¿Borrar todo el plan?');">
            <i class="ti ti-trash"></i> Limpiar Semana
        </a>

        <div class="row row-cards">
            <?php 
            $dias = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'];
            foreach($dias as $dia): 
                $sql_suma = "SELECT SUM(calorias_calculadas) as total FROM planes_socio_semanal WHERE id_socio = '$id_socio' AND dia_semana = '$dia'";
                $res_suma = $conexion->query($sql_suma);
                $consumido = $res_suma->fetch_assoc()['total'] ?? 0;
                
                // CAMBIO DE COLOR SI SE PASA DE CALORÍAS
                $color_card = ($consumido > $meta_diaria) ? 'border-danger bg-red-lt' : 'border-light';
            ?>
            <div class="col-md-4 mb-3">
                <div class="card h-100 shadow-sm <?php echo $color_card; ?>">
                    <div class="card-header d-flex justify-content-between align-items-center bg-transparent">
                        <h3 class="card-title <?php echo ($consumido > $meta_diaria) ? 'text-danger' : 'text-blue'; ?> fw-bold"><?php echo $dia; ?></h3>
                        <span class="badge <?php echo ($consumido > $meta_diaria) ? 'bg-red text-white' : 'bg-green-lt'; ?>">
                            <?php echo round($consumido); ?> / <?php echo $meta_diaria; ?> kcal
                        </span>
                    </div>
                    <div class="card-body p-0">
                        <?php 
                        // Sincronizado con nombres de tu tabla sugerencias
                        $momentos = ['Desayuno', 'Almuerzo', 'Cena', 'Snack'];
                        foreach($momentos as $m):
                            // CONSULTA MEJORADA: Busca nombre en Alimentos O en Sugerencias
                            $sql_p = "SELECT p.*, a.nombre as nombre_real, s.nombre_alimento as nombre_sug 
                                      FROM planes_socio_semanal p 
                                      LEFT JOIN alimentos a ON p.id_alimento = a.id_alimento 
                                      LEFT JOIN sugerencias_comidas s ON p.id_alimento = 0 AND s.momento = p.momento AND p.calorias_calculadas = s.calorias
                                      WHERE p.id_socio = '$id_socio' AND p.dia_semana = '$dia' AND p.momento = '$m'
                                      LIMIT 1";
                            $res_p = $conexion->query($sql_p);
                            $item = $res_p->fetch_assoc();
                            
                            $nombre_item = "Sin asignar";
                            $es_sugerencia = false;
                            
                            if($item) {
                                if(!empty($item['nombre_real'])) {
                                    $nombre_item = $item['nombre_real'];
                                } elseif(!empty($item['nombre_sug'])) {
                                    $nombre_item = $item['nombre_sug'];
                                    $es_sugerencia = true; // Para el color extra
                                }
                            }
                        ?>
                        <div class="p-3 border-bottom <?php echo $es_sugerencia ? 'bg-blue-lt' : ''; ?>">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <small class="text-muted fw-bold text-uppercase" style="font-size: 0.6rem;">
                                        <?php echo $m; ?> <?php echo $es_sugerencia ? '✨' : ''; ?>
                                    </small>
                                    <div class="fw-bold <?php echo $item ? 'text-dark' : 'text-muted fw-normal'; ?>">
                                        <?php echo htmlspecialchars($nombre_item); ?>
                                    </div>
                                    <?php if($item): ?>
                                        <small class="text-blue"><strong><?php echo round($item['calorias_calculadas']); ?> kcal</strong></small>
                                    <?php endif; ?>
                                </div>
                                <div class="d-flex gap-1">
                                    <?php if($item): ?>
                                        <a href="eliminar_alimentos.php?id=<?php echo $item['id_plan']; ?>" class="btn btn-icon btn-sm btn-outline-danger rounded-circle">
                                            <i class="ti ti-minus"></i>
                                        </a>
                                    <?php endif; ?>
                                    <button class="btn btn-icon btn-sm btn-outline-primary rounded-circle" onclick="abrirModalPlan('<?php echo $dia; ?>', '<?php echo $m; ?>')">
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
                <h5 class="modal-title">Asignar: <span id="txt-dia"></span> - <span id="txt-momento"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="dia" id="input-dia">
                <input type="hidden" name="momento" id="input-momento">
                <div class="mb-3">
                    <label class="form-label">Alimento</label>
                    <select name="id_alimento" id="select-buscar-alimento" class="form-select" required>
                        <option value="">Buscar alimento...</option>
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
                    <input type="number" name="gramos" class="form-control" required value="100">
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary ms-auto">Guardar en Plan</button>
            </div>
        </form>
    </div>
</div>

<div class="modal modal-blur fade" id="modal-recomendacion" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <form class="modal-content" action="guardar_plan_socio.php" method="POST">
            <div class="modal-header">
                <h5 class="modal-title">Añadir Sugerencia</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>¿A qué día quieres añadir <strong><span id="txt-rec-nombre"></span></strong>?</p>
                <input type="hidden" name="id_alimento" id="rec-id-alimento">
                <input type="hidden" name="gramos" value="100">
                <div class="mb-3">
                    <label class="form-label">Día</label>
                    <select name="dia" class="form-select">
                        <?php foreach($dias as $d) echo "<option value='$d'>$d</option>"; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Momento sugerido</label>
                    <input type="text" name="momento" id="rec-momento" class="form-control" readonly>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary w-100">Confirmar</button>
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
    
    var control = document.getElementById('select-buscar-alimento').tomselect;
    if(control) control.clear();
    
    new bootstrap.Modal(document.getElementById('modal-asignar')).show();
}

function recomendarComida(idSugerencia, nombre, momento) {
    document.getElementById('rec-id-alimento').value = idSugerencia;
    document.getElementById('txt-rec-nombre').innerText = nombre;
    document.getElementById('rec-momento').value = momento;
    new bootstrap.Modal(document.getElementById('modal-recomendacion')).show();
}

document.addEventListener("DOMContentLoaded", function () {
    if(document.getElementById('select-buscar-alimento')){
        new TomSelect("#select-buscar-alimento", {
            create: false,
            placeholder: "Escribe para buscar...",
        });
    }
});
</script>

<link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
<?php include 'footer.php'; ?>