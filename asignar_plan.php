<?php
include 'config.php';
include 'header_entrenador.php'; 

$id_socio = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id_socio <= 0) {
    echo "<div class='container mt-5'><div class='alert alert-danger fw-bold text-uppercase'>Socio no encontrado</div></div>";
    include 'footer.php'; exit;
}

$res_s = $conexion->query("SELECT nombre, apellido, calorias_objetivo, objetivo FROM socios WHERE id_socio = $id_socio");
$socio = $res_s->fetch_assoc();

$meta_real = $socio['objetivo'] ?? 'Perdida de Peso'; 
$recomendacion = "";
$clase_alerta = "bg-lime-lt text-lime border-lime";

if ($meta_real == 'Perdida de Peso') {
    $recomendacion = "Para perdida de peso, el rango sugerido es de 1600 a 1900 kcal diarias.";
    $clase_alerta = "bg-blue-lt text-blue border-blue";
} elseif ($meta_real == 'Ganancia Muscular') {
    $recomendacion = "Para aumento de masa muscular, el rango sugerido es de 2600 a 3000 kcal diarias.";
    $clase_alerta = "bg-orange-lt text-orange border-orange";
} elseif ($meta_real == 'Resistencia') {
    $recomendacion = "Para resistencia, se recomienda un balance de 2200 a 2500 kcal con enfoque en carbohidratos.";
    $clase_alerta = "bg-purple-lt text-purple border-purple";
}
?>

<div class="page-wrapper">
    <div class="container-xl mt-3">
        
        <?php if(isset($_GET['success'])): ?>
            <div class="alert alert-important alert-success alert-dismissible shadow-sm mb-4" role="alert">
                <div class="d-flex">
                    <div><i class="ti ti-check icon alert-icon"></i></div>
                    <div class="fw-bold small">Cambios enviados al socio con éxito</div>
                </div>
                <a class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="close"></a>
            </div>
        <?php endif; ?>

        <div class="row row-cards justify-content-center">
            <div class="col-md-10 col-lg-8">
                <div class="card mb-3 shadow-sm border-0">
                    <div class="card-status-top bg-lime"></div>
                    <div class="card-body">
                        <div class="row align-items-center mb-3">
                            <div class="col">
                                <h3 class="card-title text-lime text-uppercase mb-1"><?php echo $socio['nombre'] . ' ' . $socio['apellido']; ?></h3>
                                
                                <form action="actualizar_objetivo.php" method="POST" class="d-flex align-items-center gap-2 mt-2">
                                    <input type="hidden" name="id_socio" value="<?php echo $id_socio; ?>">
                                    <label class="small fw-bold text-uppercase text-muted">Objetivo:</label>
                                    <select name="objetivo" class="form-select form-select-sm border-secondary-subtle w-auto" onchange="this.form.submit()">
                                        <option value="Perdida de Peso" <?php if($meta_real == 'Perdida de Peso') echo 'selected'; ?>>Perdida de Peso</option>
                                        <option value="Ganancia Muscular" <?php if($meta_real == 'Ganancia Muscular') echo 'selected'; ?>>Ganancia Muscular</option>
                                        <option value="Resistencia" <?php if($meta_real == 'Resistencia') echo 'selected'; ?>>Resistencia</option>
                                    </select>
                                </form>
                            </div>
                            
                            <div class="col-auto">
                                <form action="actualizar_meta.php" method="POST" class="d-flex gap-2">
                                    <input type="hidden" name="id_socio" value="<?php echo $id_socio; ?>">
                                    <div class="input-group" style="max-width: 230px;">
                                        <input type="number" name="calorias" class="form-control fw-bold border-lime text-center" value="<?php echo $socio['calorias_objetivo']; ?>">
                                        <span class="input-group-text bg-lime-lt text-lime border-lime fw-bold">KCAL</span>
                                        <button type="submit" class="btn btn-lime">Cambiar</button>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <div class="p-3 rounded border <?php echo $clase_alerta; ?>">
                            <div class="d-flex align-items-center">
                                <i class="ti ti-bulb me-2 icon"></i>
                                <div>
                                    <span class="fw-bold text-uppercase small d-block">Sugerencia del sistema</span>
                                    <span class="small"><?php echo $recomendacion; ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <form action="guardar_plan_entrenador.php" method="POST" class="card shadow-sm border-0">
                    <div class="card-header bg-lime text-white"><h3 class="card-title text-white">Asignar Comida</h3></div>
                    <div class="card-body p-4">
                        <input type="hidden" name="id_socio" value="<?php echo $id_socio; ?>">
                        <div class="row g-4">
                            <div class="col-md-6">
                                <label class="form-label fw-bold small">Día</label>
                                <select name="dia" class="form-select border-secondary-subtle">
                                    <option value="Lunes">Lunes</option><option value="Martes">Martes</option>
                                    <option value="Miercoles">Miercoles</option><option value="Jueves">Jueves</option>
                                    <option value="Viernes">Viernes</option><option value="Sabado">Sabado</option>
                                    <option value="Domingo">Domingo</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small">Momento</label>
                                <select name="momento" class="form-select border-secondary-subtle">
                                    <option value="Desayuno">Desayuno</option><option value="Almuerzo">Almuerzo</option> 
                                    <option value="Cena">Cena</option><option value="Snack">Snack</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label text-lime fw-bold small">Seleccionar Alimento</label>
                                <select name="id_alimento" id="select-alimento" required>
                                    <option value="">Buscar alimento...</option>
                                    <?php 
                                    $alims = $conexion->query("SELECT id_alimento, nombre, calorias_por_100g FROM alimentos ORDER BY nombre ASC");
                                    while($a = $alims->fetch_assoc()): ?>
                                        <option value="<?php echo $a['id_alimento']; ?>" data-cal="<?php echo $a['calorias_por_100g']; ?>">
                                            <?php echo $a['nombre']; ?> (<?php echo $a['calorias_por_100g']; ?> kcal)
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small">Gramos</label>
                                <input type="number" name="gramos" id="i-gramos" class="form-control" value="100">
                            </div>
                            <div class="col-md-6">
                                <div class="bg-lime-lt p-3 rounded text-center border border-lime-subtle">
                                    <label class="form-label text-lime small fw-bold mb-0 text-uppercase">Calorías Calculadas</label>
                                    <div class="h1 text-lime mb-0 fw-bold"><span id="res-kcal">0.0</span></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer bg-light text-end">
                        <button type="submit" class="btn btn-lime btn-pill px-5 fw-bold">Guardar en Agenda</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>

<script>
document.addEventListener("DOMContentLoaded", function () {
    var ts = new TomSelect('#select-alimento', { create: false, maxOptions: 500 });
    var inGramos = document.getElementById('i-gramos');
    var resKcal = document.getElementById('res-kcal');

    function calcular() {
        var id = ts.getValue();
        var rawOption = document.querySelector('#select-alimento option[value="'+id+'"]');
        var gr = parseFloat(inGramos.value) || 0;
        if (rawOption) {
            var cal100 = parseFloat(rawOption.getAttribute('data-cal'));
            resKcal.innerText = ((cal100 / 100) * gr).toFixed(1);
        } else {
            resKcal.innerText = "0.0";
        }
    }
    ts.on('change', calcular);
    inGramos.addEventListener('input', calcular);
});
</script>

<?php include 'footer.php'; ?>