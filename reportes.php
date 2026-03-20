<?php 
include 'config.php';
include 'validar_admin.php'; // este archivo valida que seas admin para entrar a esta pagina
include 'header.php'; 


// Estadísticas de Socios
$total_socios = $conexion->query("SELECT COUNT(*) as t FROM socios")->fetch_assoc()['t'];
$socios_activos = $conexion->query("SELECT COUNT(*) as t FROM socios WHERE estado = 'activo'")->fetch_assoc()['t'];
$socios_vencidos = $conexion->query("SELECT COUNT(*) as t FROM socios WHERE estado = 'vencido'")->fetch_assoc()['t'];

// Ingresos del mes actual (Membresías de socios + Pases Diarios)
$res_ingresos = $conexion->query("
    SELECT (
        /* Suma de membresías de socios registrados este mes */
        (SELECT IFNULL(SUM(m.precio), 0) 
         FROM socios s 
         JOIN membresias m ON s.id_membresia = m.id_membresia 
         WHERE MONTH(s.fecha_registro) = MONTH(CURRENT_DATE()) 
         AND YEAR(s.fecha_registro) = YEAR(CURRENT_DATE()))
        + 
        /* Suma de pases diarios registrados en la tabla pagos */
        (SELECT IFNULL(SUM(monto), 0) 
         FROM pagos 
         WHERE concepto = 'Pase Diario' 
         AND MONTH(fecha_pago) = MONTH(CURRENT_DATE()) 
         AND YEAR(fecha_pago) = YEAR(CURRENT_DATE()))
    ) as total
");

$ingresos_mes = $res_ingresos->fetch_assoc()['total'] ?? 0;

// Proyeccion de ingresos (lo que deberían pagar los activos)
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
                        Reportes y Estadísticas
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
                            $res_m = $conexion->query("SELECT id_membresia, nombre as nombre_membresia FROM membresias");
                            while($m = $res_m->fetch_assoc()): 
                                $id_m = $m['id_membresia'];
                                $nombre_m = $m['nombre_membresia'];

                                if($nombre_m == 'Visita Diaria' || $nombre_m == 'Pase Diario') {
                                    $res_c = $conexion->query("SELECT COUNT(*) as total FROM pagos WHERE concepto = 'Pase Diario'");
                                    $cantidad = $res_c->fetch_assoc()['total'];
                                } else {
                                    $res_c = $conexion->query("SELECT COUNT(*) as total FROM socios WHERE id_membresia = '$id_m' AND estado = 'activo'");
                                    $cantidad = $res_c->fetch_assoc()['total'];
                                }
                                $porcentaje = ($total_socios > 0) ? ($cantidad / $total_socios) * 100 : ($cantidad * 5); 
                            ?>
                            <div class="col-md-4 mb-3">
                                <div class="mb-2">
                                    <div class="d-flex align-items-center mb-1">
                                        <div class="font-weight-bold"><?php echo $nombre_m; ?></div>
                                        <div class="ms-auto"><span class="text-muted small"><?php echo $cantidad; ?> registros</span></div>
                                    </div>
                                    <div class="progress progress-sm">
                                        <div class="progress-bar bg-yellow" style="width: <?php echo min($porcentaje, 100); ?>%"></div>
                                    </div>
                                </div>
                            </div>
                            <?php endwhile; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-12">
                <div class="card shadow-sm border-0">
                    <div class="card-status-top bg-yellow"></div>
                    <div class="card-header">
                        <h3 class="card-title text-dark font-weight-bold">Historial de Pases Diarios (Hoy)</h3>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-vcenter card-table table-striped">
                            <thead>
                                <tr>
                                    <th>Nombre del Visitante</th>
                                    <th>Monto</th>
                                    <th>Fecha</th>
                                    <th class="w-1">Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $hoy = date('Y-m-d');
                                $res_p = $conexion->query("SELECT referencia, monto, fecha_pago FROM pagos WHERE concepto = 'Pase Diario' AND DATE(fecha_pago) = '$hoy' ORDER BY id_pago DESC");
                                if($res_p->num_rows > 0):
                                    while($p = $res_p->fetch_assoc()): ?>
                                    <tr>
                                        <td class="font-weight-bold"><?php echo str_replace("Pase Diario: ", "", $p['referencia']); ?></td>
                                        <td class="text-green">$<?php echo number_format($p['monto'], 2); ?></td>
                                        <td><?php echo date('d/m/Y', strtotime($p['fecha_pago'])); ?></td>
                                        <td><span class="badge bg-green-lt">PAGADO</span></td>
                                    </tr>
                                <?php endwhile; else: ?>
                                    <tr><td colspan="4" class="text-center text-muted">No hay pases registrados hoy.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mb-4">
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
                                    $hoy_dt = new DateTime();
                                    $diff = $hoy_dt->diff($vence);
                                    $dias = (int)$diff->format("%r%a");
                                ?>
                                <tr>
                                    <td><div class="font-weight-bold text-dark"><?php echo $row['nombre']." ".$row['apellido']; ?></div></td>
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

        <hr class="my-5">
        <div class="row row-cards">
            <div class="col-12">
                <h3 class="text-yellow mb-3">Resumen de Caja General</h3>
            </div>
            <?php 
            $res_c = $conexion->query("SELECT 
                SUM(CASE WHEN metodo_pago = 'Efectivo' THEN monto ELSE 0 END) as ef,
                SUM(CASE WHEN metodo_pago = 'Tarjeta' THEN monto ELSE 0 END) as tj,
                SUM(CASE WHEN metodo_pago = 'Transferencia' THEN monto ELSE 0 END) as tr,
                SUM(monto) as total FROM pagos WHERE DATE(fecha_pago) = '$hoy'");
            $c = $res_c->fetch_assoc();
            ?>
            <div class="col-md-4">
                <div class="card card-sm">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <span class="bg-green text-white avatar me-3"><i class="ti ti-cash"></i></span>
                            <div>
                                <div class="font-weight-medium">Efectivo Hoy</div>
                                <div class="text-muted">$<?php echo number_format($c['ef'] ?? 0, 2); ?></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card card-sm">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <span class="bg-blue text-white avatar me-3"><i class="ti ti-credit-card"></i></span>
                            <div>
                                <div class="font-weight-medium">Otros (Tarjeta/Transf)</div>
                                <div class="text-muted">$<?php echo number_format(($c['tj'] + $c['tr']) ?? 0, 2); ?></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card card-sm bg-yellow-lt">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <span class="bg-yellow text-white avatar me-3"><i class="ti ti-currency-dollar"></i></span>
                            <div>
                                <div class="font-weight-bold">TOTAL CAJA HOY</div>
                                <div class="h3 mb-0">$<?php echo number_format($c['total'] ?? 0, 2); ?></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div> </div> <?php include 'footer.php'; ?>