<?php 
// usamos el candado para que solo entren clientes
include 'validar_socio.php'; 
include 'config.php';
include 'header_socio.php';

$id_socio = $_SESSION['id_socio'];
$sql = "SELECT * FROM socios WHERE id_socio = '$id_socio'";
$res = $conexion->query($sql);
$s = $res->fetch_assoc();

$color_status = (strtolower($s['estado']) == 'activo') ? 'bg-success' : 'bg-danger';
?>

<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover"/>
    <title>Mi Perfil | Gym Rubí</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/core@1.0.0-beta17/dist/css/tabler.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">
</head>
<body class="bg-light">
    <div class="page">
        <div class="page-wrapper">
            <div class="page-body">
                <div class="container-xl">
                    <div class="row row-deck row-cards">
                        
                        <div class="col-md-4">
                            <div class="card shadow-sm">
                                <div class="card-status-top bg-primary"></div>
                                <div class="card-body text-center py-5">
                                    <h3 class="mb-3">Mi Pase de Acceso</h3>
                                    <div class="mb-4">
                                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=<?php echo $s['id_socio']; ?>" 
                                             alt="QR" class="img-fluid border p-3 bg-white shadow-sm" style="border-radius: 10px;">
                                    </div>
                                    <div class="h2 mb-1"><?php echo $s['nombre']; ?></div>
                                    <span class="badge <?php echo $color_status; ?> text-white px-3 py-2 fs-6">
                                        Socio <?php echo $s['estado']; ?>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-8">
                            <div class="card shadow-sm mb-3">
                                <div class="card-header"><h3 class="card-title text-primary">Detalles de Membresía</h3></div>
                                <div class="card-body">
                                    <div class="row text-center">
                                        <div class="col-6 border-end">
                                            <div class="text-muted mb-1">Próximo Vencimiento</div>
                                            <div class="h2 font-weight-bold">
                                                <?php echo date('d/m/Y', strtotime($s['vencimiento'])); ?>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="text-muted mb-1">Tipo de Plan</div>
                                            <div class="h2 font-weight-bold text-success">Mensualidad</div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="card shadow-sm">
                                <div class="card-header d-flex justify-content-between">
                                    <h3 class="card-title">Resumen de Progreso</h3>
                                    <span class="badge bg-blue-lt">Nueva Evaluación pronto</span>
                                </div>
                                <div class="card-body py-5 text-center">
                                    <div class="text-muted">
                                        <i class="ti ti-chart-area fs-1"></i>
                                        <p class="mt-2">Muy pronto podrás ver aquí tus gráficas de IMC y peso.</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/@tabler/core@1.0.0-beta17/dist/js/tabler.min.js"></script>
</body>
</html>