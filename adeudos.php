<?php 
include 'config.php';
include 'validar_admin.php'; 
include 'header.php'; 

// Consulta socios con membresia vencida para el reporte de adeudos
$res_adeudos = $conexion->query("
    SELECT s.id_socio, s.nombre, s.apellido, s.telefono, s.fecha_vencimiento, m.nombre as membresia, m.precio 
    FROM socios s 
    JOIN membresias m ON s.id_membresia = m.id_membresia 
    WHERE s.estado = 'vencido' 
    ORDER BY s.fecha_vencimiento ASC");

// Calculo total de adeudos pendientes (monto de membresias vencidas)
$total_pendiente = $conexion->query("
    SELECT SUM(m.precio) as total 
    FROM socios s 
    JOIN membresias m ON s.id_membresia = m.id_membresia 
    WHERE s.estado = 'vencido'")->fetch_assoc()['total'] ?? 0;
?>

<div class="page-wrapper">
    <div class="container-xl mt-4">
        <div class="page-header mb-4">
            <div class="row align-items-center">
                <div class="col">
                    <h2 class="page-title text-red">Reporte de Adeudos y Vencidos</h2>
                    <p class="text-muted">Lista de socios con pagos pendientes por membresía expirada.</p>
                </div>
                <div class="col-auto">
                    <div class="card bg-red-lt border-0 shadow-sm p-2 px-3">
                        <span class="text-uppercase small fw-bold">Total Pendiente de Cobro</span>
                        <div class="h2 mb-0 fw-bold text-red">$<?php echo number_format($total_pendiente, 2); ?></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-status-top bg-red"></div>
            <div class="table-responsive">
                <table class="table table-vcenter card-table table-striped">
                    <thead>
                        <tr>
                            <th>Socio</th>
                            <th>Membresía</th>
                            <th>Venció el</th>
                            <th>Monto Pendiente</th>
                            <th>Contacto</th>
                            <th class="w-1">Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if($res_adeudos->num_rows > 0): ?>
                            <?php while($row = $res_adeudos->fetch_assoc()): 
                                // Convertimos fechas para calcular dias de atraso
                                $vence = new DateTime($row['fecha_vencimiento']);
                                $hoy = new DateTime();
                                $diff = $hoy->diff($vence);
                                $dias_atraso = $diff->days;
                            ?>
                            <tr>
                                <td>
                                    <div class="font-weight-bold text-dark"><?php echo $row['nombre']." ".$row['apellido']; ?></div>
                                    <div class="text-muted small">ID: #<?php echo $row['id_socio']; ?></div>
                                </td>
                                <td><?php echo $row['membresia']; ?></td>
                                <td>
                                    <span class="text-red font-weight-bold"><?php echo date('d/m/Y', strtotime($row['fecha_vencimiento'])); ?></span>
                                    <div class="small text-muted">(Hace <?php echo $dias_atraso; ?> días)</div>
                                </td>
                                <td class="fw-bold text-dark">$<?php echo number_format($row['precio'], 2); ?></td>
                                <td>
                                    <a href="https://wa.me/<?php echo $row['telefono']; ?>" target="_blank" class="btn btn-sm btn-ghost-success">
                                        <i class="ti ti-brand-whatsapp me-1"></i> <?php echo $row['telefono']; ?>
                                    </a>
                                </td>
                                <td>
                                    <a href="pagos.php?id_socio=<?php echo $row['id_socio']; ?>" class="btn btn-primary btn-sm">
                                        Cobrar
                                    </a>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center py-4">
                                    <i class="ti ti-check text-success fs-1"></i>
                                    <p class="text-muted mt-2">¡Increíble! No hay socios con pagos pendientes hoy.</p>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-4 text-muted small italic text-center">
            * Este reporte se basa en los socios con estado "vencido" en la base de datos.
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>