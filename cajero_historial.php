<?php 
include 'config.php';
include 'validar_caja.php'; // Validación de acceso para Recepción y Admin

session_start();
if(!isset($_SESSION['rol'])){ header("Location: login.php"); exit(); }
include 'header_caja.php'; 

$fecha = isset($_GET['fecha']) ? $_GET['fecha'] : date('Y-m-d');

$sql = "SELECT p.*, s.nombre, s.apellido 
        FROM pagos p 
        LEFT JOIN socios s ON p.id_socio = s.id_socio 
        WHERE DATE(p.fecha_pago) = '$fecha' 
        ORDER BY p.fecha_pago DESC";

$res = $conexion->query($sql);
$total = 0;
?>

<div class="page-wrapper">
    <div class="container-xl mt-4">
        <div class="page-header mb-4">
            <h2 class="page-title text-azure">Historial de Pagos</h2>
        </div>

        <div class="card mb-3 shadow-sm border-0">
            <div class="card-body">
                <form method="GET" class="row g-2 align-items-center">
                    <div class="col-auto"><strong>Consultar Fecha:</strong></div>
                    <div class="col-auto">
                        <input type="date" name="fecha" value="<?php echo $fecha; ?>" class="form-control" onchange="this.form.submit()">
                    </div>
                </form>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="table-responsive">
                <table class="table table-vcenter card-table table-striped">
                    <thead class="bg-azure-lt">
                        <tr>
                            <th>Hora</th>
                            <th>Socio / Cliente</th>
                            <th>Categoría</th>
                            <th>Concepto Detallado</th>
                            <th>Método</th>
                            <th>Referencia</th>
                            <th class="text-end">Monto</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if($res && $res->num_rows > 0): ?>
                            <?php while($r = $res->fetch_assoc()): $total += $r['monto']; ?>
                            <tr>
                                <td class="text-muted small"><?php echo date('H:i', strtotime($r['fecha_pago'])); ?></td>
                                <td>
                                    <?php echo $r['nombre'] ? "<strong>".$r['nombre']." ".$r['apellido']."</strong>" : "<span class='text-orange font-weight-bold'>Venta Directa</span>"; ?>
                                </td>
                                <td>
                                    <?php echo !empty($r['id_membresia']) ? '<span class="badge bg-yellow text-black">Membresía</span>' : '<span class="badge bg-blue">Producto</span>'; ?>
                                </td>
                                <td class="small"><?php echo $r['concepto'] ?: 'Venta General'; ?></td>
                                <td><span class="badge badge-outline text-azure"><?php echo $r['metodo_pago'] ?: 'Efectivo'; ?></span></td>
                                <td class="text-muted small"><?php echo $r['referencia'] ?: '-'; ?></td>
                                <td class="fw-bold text-dark text-end">$<?php echo number_format($r['monto'], 2); ?></td>
                            </tr>
                            <?php endwhile; ?>
                            <tr class="bg-light">
                                <td colspan="6" class="text-end fw-bold">TOTAL DE VENTAS:</td>
                                <td class="h3 text-azure fw-bold text-end">$<?php echo number_format($total, 2); ?></td>
                            </tr>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">No se encontraron movimientos registrados.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php include 'footer.php'; ?>