<?php 
include 'config.php';
include 'validar_admin.php'; 
include 'header.php'; 

// 1. Capturar la fecha buscada (si no hay, usamos la de hoy para la consulta inicial)
$fecha_busqueda = isset($_GET['fecha']) ? $_GET['fecha'] : date('Y-m-d');

// 2. Consulta filtrada por la fecha seleccionada
// Unimos con la tabla socios para traer los nombres
$sql = "SELECT p.*, s.nombre, s.apellido 
        FROM pagos p 
        LEFT JOIN socios s ON p.id_socio = s.id_socio 
        WHERE DATE(p.fecha_pago) = '$fecha_busqueda' 
        ORDER BY p.fecha_pago DESC";

$resultado = $conexion->query($sql);

// 3. Calcular el total de ese día para mostrarlo arriba
$total_dia = 0;
?>

<div class="page-wrapper">
    <div class="container-xl mt-4">
        <div class="page-header mb-4">
            <div class="row align-items-center">
                <div class="col">
                    <h2 class="page-title text-azure">Historial de Pagos</h2>
                    <p class="text-muted">Consulta los movimientos realizados en fechas anteriores.</p>
                </div>
            </div>
        </div>

        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <form title="Filtrar por fecha" method="GET" action="historial_pagos.php" class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Seleccionar fecha de búsqueda:</label>
                        <input type="date" name="fecha" class="form-control" 
                               value="<?php echo $fecha_busqueda; ?>" 
                               onchange="this.form.submit()"> </div>
                    <div class="col-md-4">
                        <a href="historial_pagos.php" class="btn btn-secondary">Limpiar filtro</a>
                    </div>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="table-responsive">
                <table class="table table-vcenter card-table table-striped">
                    <thead class="bg-azure-lt">
                        <tr>
                            <th>Hora</th>
                            <th>Socio / Cliente</th>
                            <th>Concepto</th>
                            <th>Método</th>
                            <th>Referencia</th>
                            <th>Monto</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if($resultado->num_rows > 0): ?>
                            <?php while($row = $resultado->fetch_assoc()): 
                                $total_dia += $row['monto']; ?>
                            <tr>
                                <td class="text-muted small">
                                    <?php echo date('H:i:s', strtotime($row['fecha_pago'])); ?>
                                </td>
                                <td>
                                    <?php 
                                    // Si no hay socio, es un pase diario o cliente externo
                                    if($row['nombre']) {
                                        echo "<strong>".$row['nombre']." ".$row['apellido']."</strong>";
                                    } else {
                                        echo "<span class='text-orange font-weight-bold'>".$row['referencia']."</span>";
                                    }
                                    ?>
                                </td>
                                <td><?php echo $row['concepto']; ?></td>
                                <td>
                                    <span class="badge bg-blue-lt"><?php echo $row['metodo_pago'] ?: 'No reg.'; ?></span>
                                </td>
                                <td class="text-muted small"><?php echo $row['referencia']; ?></td>
                                <td class="fw-bold text-dark">$<?php echo number_format($row['monto'], 2); ?></td>
                            </tr>
                            <?php endwhile; ?>
                            <tr class="bg-light">
                                <td colspan="5" class="text-end fw-bold">TOTAL DEL DÍA:</td>
                                <td class="fw-bold text-azure h3">$<?php echo number_format($total_dia, 2); ?></td>
                            </tr>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <div class="text-muted">No se encontraron pagos para la fecha seleccionada.</div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>