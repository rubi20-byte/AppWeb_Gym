<?php 
include 'validar_socio.php';
include 'config.php';

// ID del socio
$id_socio = $_SESSION['id_socio'] ?? 7; 

include 'header_socio.php'; 

// 1. Datos del socio
$res_socio = $conexion->query("SELECT * FROM socios WHERE id_socio = '$id_socio'");
$datos = $res_socio->fetch_assoc();

// --- CÁLCULO DE MEMBRESÍA ---
$fecha_pago = $datos['fecha_vencimiento'];
$dias_restantes = 0;
$estado_membresia = "Sin datos";

if ($fecha_pago) {
    $f_venc = new DateTime($fecha_pago);
    $hoy = new DateTime();
    $diff = $hoy->diff($f_venc);
    $dias_restantes = (int)$diff->format("%r%a"); // %r trae el signo menos si ya pasó
}
// ----------------------------

// 2. Frase Motivacional
$frases = ["La disciplina es el puente entre tus metas y tus logros.", "No te detengas hasta que estés orgulloso.", "Tu único rival es la persona que fuiste ayer."];
$frase_del_dia = $frases[date('w') % count($frases)];

// 3. Clases Inscritas
$res_clases = $conexion->query("SELECT c.nombre_clase, c.hora_inicio FROM reservas_clases r JOIN clases c ON r.id_clase = c.id_clase WHERE r.id_socio = '$id_socio' LIMIT 2");

// 4. Datos de Peso
$res_evals = $conexion->query("SELECT * FROM evaluaciones WHERE id_socio = '$id_socio' ORDER BY fecha_evaluacion ASC");
$fechas = []; $pesos = [];
while($row = $res_evals->fetch_assoc()){
    $fechas[] = date('d/m', strtotime($row['fecha_evaluacion']));
    $pesos[] = (float)$row['peso'];
}
if(empty($pesos)) { $pesos = [0]; $fechas = ['Sin datos']; }
$ultimo_peso = end($pesos);
?>

<div class="page-wrapper">
    <div class="page-body">
        <div class="container-xl">
            
            <div class="card mb-3 border-0 shadow-sm bg-azure-lt">
                <div class="card-body p-4">
                    <h2 class="m-0 text-dark">¡Qué bueno verte, <?php echo explode(' ', $datos['nombre'])[0]; ?>!</h2>
                    <p class="text-muted mb-0">"<?php echo $frase_del_dia; ?>"</p>
                </div>
            </div>

            <div class="row row-cards">
                <div class="col-lg-4">
                    
                    <div class="card mb-3 border-0 shadow-sm">
                        <div class="card-body">
                            <div class="subheader mb-2">Estado de cuenta</div>
                            <div class="d-flex align-items-center mb-3">
                                <?php if ($dias_restantes > 0): ?>
                                    <span class="badge bg-green-lt p-2 w-100 h3">Activo - <?php echo $dias_restantes; ?> días restantes</span>
                                <?php else: ?>
                                    <span class="badge bg-red-lt p-2 w-100 h3">Membresía Vencida</span>
                                <?php endif; ?>
                            </div>
                            <div class="small text-muted text-center">
                                <i class="ti ti-calendar-event me-1"></i> Próximo pago: 
                                <strong><?php echo date('d/m/Y', strtotime($fecha_pago)); ?></strong>
                            </div>
                        </div>
                    </div>

                    <div class="card mb-3 border-0 shadow-sm">
                        <div class="card-body p-2 text-center">
                            <h3 class="card-title">Calorías Diarias</h3>
                            <div id="chart-rueda-cal" style="min-height: 200px;"></div>
                            <div class="mt-n2 text-muted small">Meta: 2,500 kcal</div>
                        </div>
                    </div>

                    <div class="card border-0 shadow-sm mb-3">
                        <div class="card-header bg-transparent"><h3 class="card-title fw-bold">Mis Clases</h3></div>
                        <div class="list-group list-group-flush">
                            <?php if($res_clases && $res_clases->num_rows > 0): 
                                while($c = $res_clases->fetch_assoc()): ?>
                                <div class="list-group-item">
                                    <div class="fw-bold"><?php echo $c['nombre_clase']; ?></div>
                                    <div class="text-muted small"><?php echo $c['hora_inicio']; ?></div>
                                </div>
                            <?php endwhile; else: ?>
                                <div class="p-3 text-center text-muted small">Sin clases para hoy</div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="col-lg-8">
                    <div class="card mb-3 border-0 shadow-sm">
                        <div class="card-header bg-transparent"><h3 class="card-title fw-bold">Evolución Corporal</h3></div>
                        <div class="card-body">
                            <div id="chart-peso-final" style="min-height: 300px;"></div>
                        </div>
                    </div>

                    <div class="card bg-dark text-white border-0 shadow-sm">
                        <div class="card-body p-4 d-flex justify-content-between align-items-center">
                            <div>
                                <div class="subheader text-yellow">Peso Actual Registrado</div>
                                <div class="h1 m-0 fw-bold"><?php echo $ultimo_peso; ?> kg</div>
                            </div>
                            <div class="avatar avatar-lg bg-yellow-lt text-yellow rounded">
                                <i class="ti ti-target-arrow" style="font-size: 1.5rem;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
document.addEventListener("DOMContentLoaded", function () {
    // Rueda de Calorías
    new ApexCharts(document.querySelector("#chart-rueda-cal"), {
        chart: { height: 200, type: 'radialBar' },
        series: [75],
        colors: ['#206bc4'],
        plotOptions: { radialBar: { hollow: { size: '60%' } } }
    }).render();

    // Gráfica de Peso
    new ApexCharts(document.querySelector("#chart-peso-final"), {
        chart: { type: 'area', height: 300, toolbar: {show:false} },
        series: [{ name: 'Peso', data: <?php echo json_encode($pesos); ?> }],
        xaxis: { categories: <?php echo json_encode($fechas); ?> },
        colors: ['#206bc4'],
        stroke: { curve: 'smooth', width: 3 },
        fill: { type: 'gradient', gradient: { opacityFrom: 0.5, opacityTo: 0.1 } }
    }).render();
});
</script>

<?php include 'footer.php'; ?>