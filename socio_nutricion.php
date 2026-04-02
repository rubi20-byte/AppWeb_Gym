<?php
include 'validar_socio.php';
include 'config.php';
include 'header_socio.php';

$id_socio = $_SESSION['id_socio'] ?? 0;

/** 1. DATOS DEL SOCIO **/
$sql_socio_data = "SELECT s.calorias_objetivo, r.nombre_rutina, s.objetivo FROM socios s 
                  LEFT JOIN rutinas r ON s.id_rutina = r.id_rutina 
                  WHERE s.id_socio = '$id_socio'";
$res_socio = $conexion->query($sql_socio_data);
$socio = $res_socio->fetch_assoc();

$meta_diaria = ($socio['calorias_objetivo'] > 0) ? $socio['calorias_objetivo'] : 2000; 
?>

<div class="page-wrapper">
    <div class="container-xl mt-4">
        
        <div class="card mb-4 shadow-sm border-0 bg-primary-lt">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="mb-1 text-primary">Mi Plan Nutricional</h2>
                    <div class="text-muted fw-bold">
                        <i class="ti ti-barbell me-1"></i> Rutina: 
                        <span class="text-body fw-normal"><?php echo htmlspecialchars($socio['nombre_rutina'] ?? 'Sin asignar'); ?></span>
                    </div>
                    <div class="text-muted fw-bold mt-1">
                        <i class="ti ti-target me-1"></i> Objetivo: 
                        <span class="text-body fw-normal"><?php echo htmlspecialchars($socio['objetivo'] ?? 'No definido'); ?></span>
                    </div>
                </div>
                <div class="text-end">
                    <div class="h1 mb-0 fw-bold text-primary"><?php echo $meta_diaria; ?></div>
                    <div class="text-uppercase text-muted small">Kcal Meta</div>
                </div>
            </div>
        </div>

        <div class="mb-3">
            <a href="limpiar_semana.php" class="btn btn-outline-danger btn-sm" onclick="return confirm('¿Borrar todo el plan?');">
                <i class="ti ti-trash"></i> Limpiar Semana
            </a>
        </div>

        <div class="row row-cards">
            <?php 
            $dias_db = ['Lunes', 'Martes', 'Miercoles', 'Jueves', 'Viernes', 'Sabado', 'Domingo'];
            $dias_vista = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'];

            for($i=0; $i < count($dias_db); $i++): 
                $dia_actual = $dias_db[$i];
                $sql_suma = "SELECT SUM(calorias_calculadas) as total FROM planes_socio_semanal WHERE id_socio = '$id_socio' AND dia_semana = '$dia_actual'";
                $consumido = $conexion->query($sql_suma)->fetch_assoc()['total'] ?? 0;
            ?>
            <div class="col-md-4 mb-3">
                <div class="card h-100 shadow-sm">
                    <div class="card-header d-flex justify-content-between align-items-center bg-transparent">
                        <h3 class="card-title fw-bold"><?php echo $dias_vista[$i]; ?></h3>
                        <span class="badge bg-green-lt"><?php echo round($consumido); ?> / <?php echo $meta_diaria; ?></span>
                    </div>
                    <div class="card-body p-0">
                        <?php 
                        $momentos = ['Desayuno', 'Almuerzo', 'Cena', 'Snack'];
                        foreach($momentos as $m):
                            $sql_p = "SELECT p.*, a.nombre 
                                      FROM planes_socio_semanal p 
                                      INNER JOIN alimentos a ON p.id_alimento = a.id_alimento 
                                      WHERE p.id_socio = '$id_socio' AND p.dia_semana = '$dia_actual' AND p.momento = '$m' LIMIT 1";
                            
                            $item = $conexion->query($sql_p)->fetch_assoc();
                        ?>
                        <div class="p-3 border-bottom">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <small class="text-muted fw-bold text-uppercase" style="font-size: 0.6rem;"><?php echo $m; ?></small>
                                    <div class="fw-bold"><?php echo $item ? htmlspecialchars($item['nombre']) : "Sin asignar"; ?></div>
                                    <?php if($item): ?>
                                        <small class="text-blue"><strong><?php echo round($item['calorias_calculadas']); ?> kcal</strong> (<?php echo $item['cantidad_gramos']; ?>g)</small>
                                    <?php endif; ?>
                                </div>
                                <div class="d-flex gap-1">
                                    <?php if($item): ?>
                                        <a href="eliminar_alimentos.php?id=<?php echo $item['id_plan']; ?>" class="btn btn-icon btn-sm btn-outline-danger rounded-circle"><i class="ti ti-minus"></i></a>
                                    <?php endif; ?>
                                    <button class="btn btn-icon btn-sm btn-outline-primary rounded-circle" onclick="abrirModalPlan('<?php echo $dia_actual; ?>', '<?php echo $m; ?>')"><i class="ti ti-plus"></i></button>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <?php endfor; ?>
        </div>
    </div>
</div>

<div class="modal modal-blur fade" id="modal-asignar" tabindex="-1">
    <div class="modal-dialog">
        <form class="modal-content" method="POST" action="guardar_plan_socio.php">
            <div class="modal-header">
                <h5 class="modal-title">Agregar a <span id="txt-dia"></span> (<span id="txt-momento"></span>)</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="dia" id="input-dia">
                <input type="hidden" name="momento" id="input-momento">
                <div class="mb-3">
                    <label class="form-label">Alimento:</label>
                    <select name="id_alimento" id="select-buscar-alimento" class="form-select" required>
                        <option value="">Buscar alimento...</option>
                        <?php 
                        $alims = $conexion->query("SELECT id_alimento, nombre, calorias_por_100g FROM alimentos ORDER BY nombre ASC");
                        while($a = $alims->fetch_assoc()): ?>
                            <option value="<?php echo $a['id_alimento']; ?>"><?php echo $a['nombre']; ?> (<?php echo $a['calorias_por_100g']; ?> kcal/100g)</option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Cantidad (Gramos):</label>
                    <input type="number" name="gramos" class="form-control" value="100">
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary w-100">Añadir al Plan</button>
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

document.addEventListener("DOMContentLoaded", function () {
    if(document.getElementById('select-buscar-alimento')){
        new TomSelect("#select-buscar-alimento", { create: false, placeholder: "Escribe para buscar..." });
    }
});
</script>

<link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
<?php include 'footer.php'; ?>