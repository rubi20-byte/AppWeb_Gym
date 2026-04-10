<?php
include 'config.php';
include 'validar_entrenador.php';
include 'header_entrenador.php';

$id_profe = $_SESSION['id_entrenador'];

// Agregamos JOIN para traer el nombre de la rutina real ---
$query_socios = "SELECT s.*, r.nombre_rutina 
                 FROM socios s
                 LEFT JOIN rutinas r ON s.id_rutina = r.id_rutina
                 WHERE s.id_entrenador = $id_profe AND s.estado = 'activo'";
$res_socios = $conexion->query($query_socios);
?>

<style>
    /* Efecto de movimiento y sombra en las tarjetas */
    .card-socio {
        transition: all 0.3s ease;
        border: none !important;
        border-radius: 15px !important;
    }

    .card-socio:hover {
        transform: translateY(-8px); /* Se eleva */
        box-shadow: 0 12px 20px rgba(0, 0, 0, 0.15) !important; /* Sombra más fuerte */
    }

    .badge-rutina {
        background-color: #f3f0ff;
        color: #7a59ad;
        border: 1px solid #d8ccf1;
        font-weight: 600;
    }

    .bg-gradient-purple {
        background: linear-gradient(45deg, #7a59ad, #a389d4);
    }
</style>

<div class="page-wrapper">
    <div class="container-xl mt-4">
        <div class="page-header mb-4">
            <div>
                <h2 class="page-title text-purple fw-bold">
                    Mis Socios en Seguimiento
                </h2>
                <p class="text-muted">Gestiona el progreso y las rutinas de tus alumnos asignados.</p>
            </div>
        </div>

        <div class="row row-cards">
            <?php while ($socio = $res_socios->fetch_assoc()): 
                $id_s = $socio['id_socio'];
                
                $rutina_actual = !empty($socio['nombre_rutina']) ? $socio['nombre_rutina'] : "Sin Rutina";

                // Obtener datos para la gráfica
                $query_evals = "SELECT peso, fecha_evaluacion FROM evaluaciones 
                                WHERE id_socio = $id_s 
                                ORDER BY fecha_evaluacion ASC LIMIT 7";
                $res_evals = $conexion->query($query_evals);
                
                $pesos = [];
                $ultimo_peso = "N/A";

                while($ev = $res_evals->fetch_assoc()){
                    $pesos[] = $ev['peso'];
                    $ultimo_peso = $ev['peso'] . " kg";
                }
            ?>
            
            <div class="col-md-6 col-lg-4">
                <div class="card card-socio shadow-sm mb-4">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center mb-4">
                            <span class="avatar avatar-xl rounded-circle bg-gradient-purple text-white shadow-sm me-3" style="font-size: 1.5rem; font-weight: bold;">
                                <?php echo strtoupper(substr($socio['nombre'], 0, 1)); ?>
                            </span>
                            <div style="line-height: 1.2;">
                                <h3 class="m-0 fw-bolder" style="color: #2c3e50;"><?php echo $socio['nombre'] . " " . $socio['apellido']; ?></h3>
                                <span class="badge bg-green-lt mt-1">Socio Activo</span>
                            </div>
                        </div>

                        <div class="row text-center mb-3">
                            <div class="col-6 border-end">
                                <div class="text-muted small text-uppercase">Peso Actual</div>
                                <div class="h3 fw-bold mb-0"><?php echo $ultimo_peso; ?></div>
                            </div>
                            <div class="col-6">
                                <div class="text-muted small text-uppercase">Estatura</div>
                                <div class="h3 fw-bold mb-0">1.<?php echo rand(60, 90); ?> m</div> 
                            </div>
                        </div>

                        <div class="bg-light rounded-3 p-2 mb-3">
                            <div id="chart-<?php echo $id_s; ?>" style="min-height: 100px;"></div>
                        </div>
                        <div class="mt-2 d-flex align-items-center">
                            <small class="text-muted me-2">
                                <i class="ti ti-barbell icon me-1"></i> Rutina Actual:
                            </small>
                            <span class="badge bg-purple-lt"><?php echo $socio['nombre_rutina'] ?? 'Sin rutina'; ?></span>
                        </div>
                        <div class="mt-2 d-flex align-items-center">
                            <small class="text-muted me-2">
                                <i class="ti ti-target icon me-1"></i> Objetivo:
                            </small>
                            <span class="badge bg-blue-lt"><?php echo $socio['objetivo'] ?? 'No definido'; ?></span>
                        </div>
                    </div>

                    <div class="d-flex justify-content-center gap-1 mt-3">
                        <a href="asignar_plan.php?id=<?php echo $socio['id_socio']; ?>" class="btn btn-lime text-white py-1 flex-fill" style="font-size: 11px;">
                            <i class="ti ti-apple"></i> Dieta
                        </a>
                        <a href="rutina_socio_ent.php?id=<?php echo $id_s; ?>" class="btn btn-pink text-white btn-sm px-1 flex-fill" style="font-size: 11px;">
                            <i class="ti ti-edit me-1"></i> Rutina
                        </a>
                        <a href="evaluar.php?id=<?php echo $id_s; ?>" class="btn btn-purple text-white btn-sm px-1 flex-fill" style="font-size: 11px;">
                            <i class="ti ti-plus me-1"></i> Evaluar
                        </a>
                    </div>
                    </div>
                </div>
            </div>

            <script>
                document.addEventListener("DOMContentLoaded", function () {
                    var options = {
                        chart: { 
                            type: "area", 
                            height: 100, 
                            sparkline: { enabled: true },
                            toolbar: { show: false },
                            animations: { enabled: true, easing: 'easeinout', speed: 800 }
                        },
                        stroke: { curve: "smooth", width: 3 },
                        fill: {
                            type: 'gradient',
                            gradient: {
                                shadeIntensity: 1,
                                opacityFrom: 0.4,
                                opacityTo: 0.1,
                                stops: [0, 90, 100]
                            }
                        },
                        series: [{ name: "Peso", data: [<?php echo implode(',', $pesos); ?>] }],
                        colors: ["#7a59ad"],
                        tooltip: { theme: 'dark', x: { show: false } }
                    };
                    new ApexCharts(document.querySelector("#chart-<?php echo $id_s; ?>"), options).render();
                });
            </script>

            <?php endwhile; ?>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<?php include 'footer.php'; ?>