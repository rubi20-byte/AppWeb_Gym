<?php 
include 'validar_socio.php';
include 'config.php';
include 'header_socio.php'; // Asumiendo que tienes un header para socios

// 1. OBTENER DATOS DEL SOCIO (Simulado con ID de sesión)
$id_socio = $_SESSION['id_socio']; 
$query = $conexion->query("SELECT * FROM socios WHERE id_socio = '$id_socio'");
$user = $query->fetch_assoc();

// 2. CÁLCULO DE EDAD "AL VUELO"
$nacimiento = new DateTime($user['fecha_nacimiento']);
$hoy = new DateTime();
$edad = $hoy->diff($nacimiento)->y;

// 3. FÓRMULA DEL EXCEL (Metas Diarias)
// Usamos Harris-Benedict según el sexo
if ($user['sexo'] == 'Mujer') {
    $tmb = (10 * $user['peso_kg']) + (6.25 * $user['estatura_cm']) - (5 * $edad) - 161;
} else {
    $tmb = (10 * $user['peso_kg']) + (6.25 * $user['estatura_cm']) - (5 * $edad) + 5;
}

// Factor de actividad Gym (x1.55) y ajuste por objetivo
$calorias_meta = round($tmb * 1.55);
if($user['objetivo'] == 'Bajar Peso') $calorias_meta -= 500;
if($user['objetivo'] == 'Ganar Musculo') $calorias_meta += 400;

// 4. CALORÍAS CONSUMIDAS HOY
$hoy_fecha = date('Y-m-d');
$res_comidas = $conexion->query("SELECT SUM(calorias_totales) as total FROM registro_comidas WHERE id_socio = '$id_socio' AND fecha = '$hoy_fecha'");
$consumido = $res_comidas->fetch_assoc()['total'] ?? 0;

$restante = $calorias_meta - $consumido;
$porcentaje = ($consumido / $calorias_meta) * 100;
?>

<div class="page-wrapper">
    <div class="container-xl mt-4">
        <div class="row row-cards">
            
            <div class="col-md-12">
                <div class="card shadow-sm border-0">
                    <div class="card-body">
                        <h3 class="card-title text-muted">Resumen Nutricional - Hoy</h3>
                        <div class="row align-items-center">
                            <div class="col-md-4 text-center">
                                <div class="h1 mb-0 font-weight-bold"><?php echo $calorias_meta; ?></div>
                                <div class="text-muted small text-uppercase">Meta Diaria (kcal)</div>
                            </div>
                            <div class="col-md-4 text-center border-start border-end">
                                <div class="h1 mb-0 text-blue font-weight-bold"><?php echo round($consumido); ?></div>
                                <div class="text-muted small text-uppercase">Consumido</div>
                            </div>
                            <div class="col-md-4 text-center">
                                <div class="h1 mb-0 <?php echo ($restante < 0) ? 'text-red' : 'text-green'; ?> font-weight-bold">
                                    <?php echo round($restante); ?>
                                </div>
                                <div class="text-muted small text-uppercase">Restante</div>
                            </div>
                        </div>
                        
                        <div class="progress mt-4" style="height: 12px;">
                            <div class="progress-bar bg-blue" style="width: <?php echo $porcentaje; ?>%" role="progressbar"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-12 text-center my-3">
                <button class="btn btn-primary btn-pill shadow" data-bs-toggle="modal" data-bs-target="#modal-alimento">
                    <i class="ti ti-plus me-2"></i> Registrar Alimento
                </button>
            </div>

            <div class="col-md-12">
                <div class="card shadow-sm">
                    <div class="card-header"><h3 class="card-title">Comidas Registradas</h3></div>
                    <div class="table-responsive">
                        <table class="table card-table table-vcenter">
                            <thead>
                                <tr>
                                    <th>Alimento</th>
                                    <th>Cantidad</th>
                                    <th>Calorías</th>
                                    <th>Momento</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $listado = $conexion->query("SELECT r.*, a.nombre_alimento 
                                                           FROM registro_comidas r 
                                                           JOIN alimentos a ON r.id_alimento = a.id_alimento 
                                                           WHERE r.id_socio = '$id_socio' AND r.fecha = '$hoy_fecha'");
                                while($c = $listado->fetch_assoc()):
                                ?>
                                <tr>
                                    <td><?php echo $c['nombre_alimento']; ?></td>
                                    <td class="text-muted"><?php echo $c['cantidad_gramos']; ?>g</td>
                                    <td class="font-weight-bold"><?php echo round($c['calorias_totales']); ?> kcal</td>
                                    <td><span class="badge bg-gray-lt"><?php echo $c['momento']; ?></span></td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<?php include 'footer.php'; ?>