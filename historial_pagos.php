<?php 
include 'config.php';
include 'validar_admin.php'; 
include 'header.php'; 

$fecha_inicio = $_GET['fecha_inicio'] ?? date('Y-m-d');
$fecha_fin = $_GET['fecha_fin'] ?? date('Y-m-d');
$filtro_tipo = $_GET['tipo'] ?? 'todos';

// Consulta optimizada para tu estructura
$sql = "SELECT 
            p.*, 
            s.nombre AS socio_n, 
            s.apellido AS socio_a, 
            prov.nombre AS nombre_proveedor
        FROM pagos p 
        LEFT JOIN socios s ON p.id_socio = s.id_socio 
        -- Intentamos unir por el nombre que aparece en el concepto
        LEFT JOIN productos prod ON (p.concepto LIKE CONCAT('%', prod.nombre, '%') AND prod.nombre != '')
        LEFT JOIN proveedores prov ON prod.id_proveedor = prov.id_proveedor
        WHERE DATE(p.fecha_pago) BETWEEN '$fecha_inicio' AND '$fecha_fin'";

if ($filtro_tipo == 'membresia') {
    $sql .= " AND p.concepto NOT LIKE '%Producto%'";
} elseif ($filtro_tipo == 'producto') {
    $sql .= " AND p.concepto LIKE '%Producto%'";
}

$sql .= " ORDER BY p.fecha_pago DESC";
$resultado = $conexion->query($sql);
?>

<div class="page-wrapper">
    <div class="container-xl mt-4">
        <h2 class="page-title text-yellow mb-3">Historial de Pagos</h2>
        
        <div class="card mb-3 border-0 shadow-sm">
            <div class="card-body">
                <form method="GET" class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Desde</label>
                        <input type="date" name="fecha_inicio" class="form-control" value="<?php echo $fecha_inicio; ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Hasta</label>
                        <input type="date" name="fecha_fin" class="form-control" value="<?php echo $fecha_fin; ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Tipo</label>
                        <select name="tipo" class="form-select">
                            <option value="todos" <?php echo $filtro_tipo == 'todos' ? 'selected' : ''; ?>>Todos</option>
                            <option value="membresia" <?php echo $filtro_tipo == 'membresia' ? 'selected' : ''; ?>>Membresías</option>
                            <option value="producto" <?php echo $filtro_tipo == 'producto' ? 'selected' : ''; ?>>Productos</option>
                        </select>
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-yellow w-100 fw-bold">Filtrar</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="table-responsive">
                <table class="table table-vcenter card-table table-striped">
                    <thead class="bg-dark text-white">
                        <tr>
                            <th>Fecha</th>
                            <th>Socio</th>
                            <th>Concepto</th>
                            <th>Proveedor</th>
                            <th class="text-end">Monto</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($resultado && $resultado->num_rows > 0): while($p = $resultado->fetch_assoc()): ?>
                        <tr>
                            <td class="small text-muted"><?php echo date('d/m/y H:i', strtotime($p['fecha_pago'])); ?></td>
                            <td class="fw-bold"><?php echo $p['socio_n'] ? $p['socio_n']." ".$p['socio_a'] : 'Público'; ?></td>
                            <td><?php echo $p['concepto']; ?></td>
                            <td class="text-blue fw-bold">
                                <?php 
                                    // Verificación triple
                                    if (!empty($p['nombre_proveedor'])) {
                                        echo $p['nombre_proveedor'];
                                    } elseif (!empty($p['proveedor'])) {
                                        echo $p['proveedor'];
                                    } else {
                                        echo '<span class="text-muted small">Sin asignar</span>';
                                    }
                                ?>
                            </td>
                            <td class="text-end fw-bold">$<?php echo number_format($p['monto'], 2); ?></td>
                        </tr>
                        <?php endwhile; else: ?>
                        <tr><td colspan="5" class="text-center py-4">No hay datos para mostrar</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>