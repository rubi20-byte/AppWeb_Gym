<?php 
include 'config.php';
include 'validar_admin.php'; // este archivo valida que seas admin para entrar a esta pagina
include 'header.php'; 

$hoy = date('Y-m-d');

// 1. Consultas para los indicadores (KPIs)
$total_socios = $conexion->query("SELECT COUNT(*) as total FROM socios")->fetch_assoc()['total'];
$activos = $conexion->query("SELECT COUNT(*) as total FROM socios WHERE fecha_vencimiento >= '$hoy' AND estado = 'activo'")->fetch_assoc()['total'];
$vencidos = $conexion->query("SELECT COUNT(*) as total FROM socios WHERE fecha_vencimiento < '$hoy' OR estado IN ('vencido', 'inactivo')")->fetch_assoc()['total'];
$total_entrenadores = $conexion->query("SELECT COUNT(*) as total FROM entrenadores WHERE estado = 'activo'")->fetch_assoc()['total'];

// 2. Consulta para la tabla de últimas inscripciones
$recientes = $conexion->query("SELECT s.*, m.nombre as plan FROM socios s JOIN membresias m ON s.id_membresia = m.id_membresia ORDER BY s.id_socio DESC LIMIT 5");
?>

<div class="page-wrapper">
    <div class="container-xl mt-4">
        <div class="row align-items-center mb-4">
            <div class="col">
                <h2 class="page-title">Panel de Control (Admin)</h2>
                <div class="text-muted mt-1">Gestión del Gimnasio Rubi.</div>
            </div>
            <div class="col-auto ms-auto">
                <div class="btn-list">
                    <a href="pase_diario.php" class="btn btn-success">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-ticket" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                            <path d="M15 5l-10 10a2.121 2.121 0 0 0 3 3l10 -10a2.121 2.121 0 0 0 -3 -3z"></path>
                            <path d="M19 15v3h-3"></path>
                            <path d="M9 11l.5 11.5l1.5 1.5l1.5 -1.5l.5 -11.5"></path>
                        </svg>
                        Vender Pase Diario
                    </a>
                    <a href="nuevo_entrenador.php" class="btn btn-purple text-white"><i class="ti ti-plus me-2"></i> Registrar Entrenador</a>
                    <a href="nuevo_socio.php" class="btn btn-primary"><i class="ti ti-user-plus me-2"></i> Inscribir Socio</a>
                </div>
            </div>
        </div>

        <div class="row row-cards mb-4">
            <div class="col-sm-6 col-lg-3">
                <a href="socios.php" class="card card-sm card-link">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-auto"><span class="bg-blue text-white avatar"><i class="ti ti-users"></i></span></div>
                            <div class="col"><div class="font-weight-medium"><?php echo $total_socios; ?> Socios</div><div class="text-muted small">Total registrados</div></div>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-sm-6 col-lg-3">
                <a href="socios.php" class="card card-sm card-link">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-auto"><span class="bg-green text-white avatar"><i class="ti ti-check"></i></span></div>
                            <div class="col"><div class="font-weight-medium"><?php echo $activos; ?> Activos</div><div class="text-muted small">Al corriente</div></div>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-sm-6 col-lg-3">
                <a href="socios_vencidos.php" class="card card-sm card-link">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-auto"><span class="bg-red text-white avatar"><i class="ti ti-alert-triangle"></i></span></div>
                            <div class="col"><div class="font-weight-medium text-red"><?php echo $vencidos; ?> Vencidos</div><div class="text-muted small">Bloqueados / Morosos</div></div>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-sm-6 col-lg-3">
                <a href="entrenadores.php" class="card card-sm card-link">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-auto"><span class="bg-purple text-white avatar"><i class="ti ti-barbell"></i></span></div>
                            <div class="col"><div class="font-weight-medium"><?php echo $total_entrenadores; ?> Coaches</div><div class="text-muted small">Equipo activo</div></div>
                        </div>
                    </div>
                </a>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-header"><h3 class="card-title">Últimas Inscripciones</h3></div>
            <div class="table-responsive">
                <table class="table card-table table-vcenter">
                    <thead><tr><th>SOCIO</th><th>PLAN</th><th>REGISTRADO</th><th>VENCE EL</th><th>ESTADO</th></tr></thead>
                    <tbody>
                        <?php while($r = $recientes->fetch_assoc()): 
                            $vence_time = strtotime($r['fecha_vencimiento']);
                            $bloqueado = ($vence_time < strtotime($hoy) || $r['estado'] != 'activo');
                        ?>
                        <tr>
                            <td><strong><?php echo $r['nombre']." ".$r['apellido']; ?></strong></td>
                            <td><span class="badge bg-blue-lt"><?php echo $r['plan']; ?></span></td>
                            <td><?php echo date('d/m/Y', strtotime($r['fecha_registro'])); ?></td>
                            <td><?php echo date('d/m/Y', $vence_time); ?></td>
                            <td><span class="badge bg-<?php echo $bloqueado ? 'red' : 'green'; ?>-lt"><?php echo $bloqueado ? 'BLOQUEADO' : 'ACTIVO'; ?></span></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php include 'footer.php'; ?>