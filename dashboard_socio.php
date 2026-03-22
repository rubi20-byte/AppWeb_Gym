<?php 
include 'config.php';
session_start();

// ID de prueba (Esmeralda)
$id_socio = isset($_SESSION['id_socio']) ? $_SESSION['id_socio'] : 7; 

include 'header_socio.php'; 

// 1. Datos del socio y membresía
$sql_socio = "SELECT s.*, m.nombre AS plan_nombre 
              FROM socios s 
              LEFT JOIN membresias m ON s.id_membresia = m.id_membresia 
              WHERE s.id_socio = '$id_socio'";
$res_socio = $conexion->query($sql_socio);
$datos = $res_socio->fetch_assoc();

// 2. Datos para la gráfica y estadísticas
$res_evals = $conexion->query("SELECT * FROM evaluaciones WHERE id_socio = '$id_socio' ORDER BY fecha_evaluacion ASC");
$fechas = []; $pesos = []; $imcs = []; $grasas = [];

while($row = $res_evals->fetch_assoc()){
    $fechas[] = date('d/m', strtotime($row['fecha_evaluacion']));
    $pesos[] = (float)$row['peso'];
    $imcs[] = (float)$row['imc'];
    $grasas[] = (float)$row['porcentaje_grasa'];
}

// Valores actuales (el último registro)
$ultimo_peso = end($pesos) ?: 0;
$ultimo_imc = end($imcs) ?: 0;
$ultima_grasa = end($grasas) ?: 0;

// Cálculo de días restantes de membresía
$fecha_venc = new DateTime($datos['fecha_vencimiento']);
$hoy = new DateTime();
$dias_restantes = $hoy->diff($fecha_venc)->format("%r%a");
?>

<div class="page-wrapper">
    <div class="page-body">
        <div class="container-xl">
            <div class="row row-cards">
                
                <div class="col-lg-4">
                    <div class="card shadow-sm mb-3">
                        <div class="card-body text-center p-4">
                            <?php $foto = (!empty($datos['foto'])) ? 'uploads/fotos/'.$datos['foto'] : 'static/default-user.png'; ?>
                            <span class="avatar avatar-xl mb-3 rounded-circle shadow-sm" style="background-image: url(<?php echo $foto; ?>); width: 110px; height: 110px;"></span>
                            <h3 class="m-0 mb-1"><?php echo $datos['nombre']; ?></h3>
                            <div class="text-muted small"><?php echo $datos['plan_nombre']; ?></div>
                            <div class="mt-3">
                                <span class="badge <?php echo ($dias_restantes > 5) ? 'bg-green-lt' : 'bg-red-lt'; ?> p-2">
                                    <?php echo ($dias_restantes > 0) ? "Vence en $dias_restantes días" : "Membresía Vencida"; ?>
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="card bg-yellow-lt shadow-sm">
                        <div class="card-body">
                            <div class="subheader text-yellow mb-2">Mi Código de Acceso</div>
                            <div class="h1 text-dark mb-0 font-weight-bold" style="letter-spacing: 1px;">
                                <?php echo $datos['qr_codigo']; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-8">
                    <div class="card shadow-sm mb-3">
                        <div class="card-header bg-white">
                            <h3 class="card-title text-yellow font-weight-bold">
                                <i class="ti ti-chart-line me-2"></i>Mi Evolución de Peso
                            </h3>
                        </div>
                        <div class="card-body">
                            <div id="chart-evolucion-final" style="min-height: 280px;"></div>
                        </div>
                    </div>

                    <div class="row row-cards">
                        <div class="col-sm-4">
                            <div class="card card-sm shadow-sm border-0">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-auto">
                                            <span class="bg-blue text-white avatar shadow-sm rounded"><i class="ti ti-weight"></i></span>
                                        </div>
                                        <div class="col">
                                            <div class="font-weight-medium">Peso Actual</div>
                                            <div class="text-muted h3 m-0 font-weight-bold"><?php echo $ultimo_peso; ?> kg</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="card card-sm shadow-sm border-0">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-auto">
                                            <span class="bg-green text-white avatar shadow-sm rounded"><i class="ti ti-activity"></i></span>
                                        </div>
                                        <div class="col">
                                            <div class="font-weight-medium">Último IMC</div>
                                            <div class="text-muted h3 m-0 font-weight-bold"><?php echo $ultimo_imc; ?></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="card card-sm shadow-sm border-0">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-auto">
                                            <span class="bg-orange text-white avatar shadow-sm rounded"><i class="ti ti-flame"></i></span>
                                        </div>
                                        <div class="col">
                                            <div class="font-weight-medium">% Grasa</div>
                                            <div class="text-muted h3 m-0 font-weight-bold"><?php echo $ultima_grasa; ?>%</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div> </div> <div class="col-12 mt-3">
                    <div class="card shadow-sm border-0 bg-dark text-white">
                        <div class="card-body d-flex align-items-center justify-content-between p-4">
                            <div>
                                <h3 class="mb-1 text-yellow font-weight-bold">¡Sigue así, <?php echo $datos['nombre']; ?>!</h3>
                                <p class="text-muted-dark m-0 h4">Has bajado <strong><?php echo (isset($pesos[0])) ? $pesos[0] - $ultimo_peso : 0; ?> kg</strong> desde tu inicio.</p>
                            </div>
                            <div class="d-none d-md-block text-yellow h1 m-0">
                                <i class="ti ti-trophy" style="font-size: 2.5rem;"></i>
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
    new ApexCharts(document.getElementById('chart-evolucion-final'), {
        chart: { type: 'area', height: 280, toolbar: {show:false} },
        series: [{ name: 'Peso', data: <?php echo json_encode($pesos); ?> }],
        xaxis: { categories: <?php echo json_encode($fechas); ?>, axisBorder: {show:false} },
        colors: ['#f59f00'],
        stroke: { curve: 'smooth', width: 3 },
        fill: { type: 'gradient', gradient: { opacityFrom: 0.6, opacityTo: 0.1 } },
        dataLabels: { enabled: true, style: { colors: ['#000'] }, background: { enabled: true, foreColor: '#fff', borderRadius: 2, padding: 4, borderColor: '#f59f00' } }
    }).render();
});
</script>

<?php include 'footer.php'; ?>~