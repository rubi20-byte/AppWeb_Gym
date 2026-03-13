<?php 
include 'config.php';
include 'header.php'; 

// 1. Estadísticas de Socios
$total_socios = $conexion->query("SELECT COUNT(*) as t FROM socios")->fetch_assoc()['t'];
$socios_activos = $conexion->query("SELECT COUNT(*) as t FROM socios WHERE estado = 'activo'")->fetch_assoc()['t'];
$socios_vencidos = $conexion->query("SELECT COUNT(*) as t FROM socios WHERE estado = 'vencido'")->fetch_assoc()['t'];

// 2. Ingresos del mes actual
$res_ingresos = $conexion->query("
    SELECT SUM(m.precio) as total 
    FROM socios s 
    JOIN membresias m ON s.id_membresia = m.id_membresia 
    WHERE MONTH(s.fecha_registro) = MONTH(CURRENT_DATE()) 
    AND YEAR(s.fecha_registro) = YEAR(CURRENT_DATE())
");
$ingresos_mes = $res_ingresos->fetch_assoc()['total'] ?? 0;

// 3. Proyección de ingresos (lo que deberían pagar los activos)
$res_proyeccion = $conexion->query("
    SELECT SUM(m.precio) as total 
    FROM socios s 
    JOIN membresias m ON s.id_membresia = m.id_membresia 
    WHERE s.estado = 'activo'
");
$proyeccion = $res_proyeccion->fetch_assoc()['total'] ?? 0;
?>

<div class="page-wrapper">
    <div class="container-xl mt-4">
        <div class="page-header mb-4">
            <div class="row align-items-center">
                <div class="col">
                    <h2 class="page-title text-yellow" style="font-size: 1.3rem;">
                        Módulo de Reportes y Estadísticas
                    </h2>
                    <p class="text-muted">Resumen financiero y operativo</p>
                </div>
            </div>
        </div>

        <div class="row row-cards mb-4">
            <div class="col-md-6">
                <div class="card border-0 shadow-sm bg-yellow-lt">
                    <div class="card-body d-flex align-items-center">
                        <span class="avatar avatar-xl bg-yellow text-white shadow"><i class="ti ti-currency-dollar fs-0"></i></span>
                        <div class="ms-4">
                            <div class="text-uppercase text-muted font-weight-bold" style="letter-spacing: 1px;">Ingresos del Mes</div>
                            <div class="display-6 font-weight-bold text-dark">$<?php echo number_format($ingresos_mes, 2); ?></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card border-0 shadow-sm bg-pink-lt">
                    <div class="card-body d-flex align-items-center">
                        <span class="avatar avatar-xl bg-pink text-white shadow"><i class="ti ti-trending-up fs-0"></i></span>
                        <div class="ms-4">
                            <div class="text-uppercase text-muted font-weight-bold" style="letter-spacing: 1px;">Proyección Activos</div>
                            <div class="display-6 font-weight-bold text-dark">$<?php echo number_format($proyeccion, 2); ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row row-cards mb-4">
            <div class="col-sm-4">
                <div class="card card-sm shadow-sm border-0">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-auto"><span class="bg-blue text-white avatar"><i class="ti ti-users"></i></span></div>
                            <div class="col">
                                <div class="font-weight-bold"><?php echo $total_socios; ?> Socios</div>
                                <div class="text-muted small">Registrados en total</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="card card-sm shadow-sm border-0">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-auto"><span class="bg-green text-white avatar"><i class="ti ti-user-check"></i></span></div>
                            <div class="col">
                                <div class="font-weight-bold text-green"><?php echo $socios_activos; ?> Activos</div>
                                <div class="text-muted small">Con membresía vigente</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="card card-sm shadow-sm border-0">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-auto"><span class="bg-red text-white avatar"><i class="ti ti-user-x"></i></span></div>
                            <div class="col">
                                <div class="font-weight-bold text-red"><?php echo $socios_vencidos; ?> Vencidos</div>
                                <div class="text-muted small">Requieren renovación</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row row-cards mb-4">
            <div class="col-12">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-light">
                        <h3 class="card-title text-dark font-weight-bold">Distribución de Membresías Activas</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <?php 
                            // Consultamos cuántos socios tiene cada membresía
                            $res_m = $conexion->query("
                                SELECT m.nombre as nombre_membresia, COUNT(s.id_socio) as total 
                                FROM membresias m 
                                LEFT JOIN socios s ON m.id_membresia = s.id_membresia 
                                GROUP BY m.id_membresia
                            ");

                            while($m = $res_m->fetch_assoc()): 
                                // Calculamos el porcentaje para la barra
                                $porcentaje = ($total_socios > 0) ? ($m['total'] / $total_socios) * 100 : 0;
                            ?>
                            <div class="col-md-4 mb-3">
                                <div class="mb-2">
                                    <div class="d-flex align-items-center mb-1">
                                        <div class="font-weight-bold"><?php echo $m['nombre_membresia']; ?></div>
                                        <div class="ms-auto">
                                            <span class="text-muted small"><?php echo $m['total']; ?> socios</span>
                                        </div>
                                    </div>
                                    <div class="progress progress-sm">
                                        <div class="progress-bar bg-yellow" style="width: <?php echo $porcentaje; ?>%"></div>
                                    </div>
                                </div>
                            </div>
                            <?php endwhile; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm border-0">
                    <div class="card-status-top bg-yellow"></div>
                    <div class="card-header">
                        <h3 class="card-title text-dark font-weight-bold">Próximos Vencimientos</h3>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-vcenter card-table table-striped">
                            <thead>
                                <tr>
                                    <th>Socio</th>
                                    <th>Membresía</th>
                                    <th>Fecha Vence</th>
                                    <th>Días Restantes</th>
                                    <th class="w-1">Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $res_list = $conexion->query("
                                    SELECT s.nombre, s.apellido, s.fecha_vencimiento, s.estado, m.nombre as m_nombre 
                                    FROM socios s 
                                    JOIN membresias m ON s.id_membresia = m.id_membresia 
                                    ORDER BY s.fecha_vencimiento ASC LIMIT 5
                                ");
                                while($row = $res_list->fetch_assoc()): 
                                    $vence = new DateTime($row['fecha_vencimiento']);
                                    $hoy = new DateTime();
                                    $diff = $hoy->diff($vence);
                                    $dias = (int)$diff->format("%r%a");
                                ?>
                                <tr>
                                    <td>
                                        <div class="font-weight-bold text-dark"><?php echo $row['nombre']." ".$row['apellido']; ?></div>
                                    </td>
                                    <td class="text-muted"><?php echo $row['m_nombre']; ?></td>
                                    <td><?php echo date('d/m/Y', strtotime($row['fecha_vencimiento'])); ?></td>
                                    <td>
                                        <?php if($dias <= 0): ?>
                                            <span class="text-red font-weight-bold"><i class="ti ti-clock-stop me-1"></i>Vencido</span>
                                        <?php elseif($dias <= 5): ?>
                                            <span class="badge bg-yellow text-yellow-fg">¡Vence en <?php echo $dias; ?> días!</span>
                                        <?php else: ?>
                                            <span class="text-muted"><?php echo $dias; ?> días</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="badge bg-<?php echo ($row['estado']=='activo') ? 'green' : 'red'; ?>-lt">
                                            <?php echo strtoupper($row['estado']); ?>
                                        </span>
                                    </td>
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