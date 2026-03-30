<?php 
include 'validar_admin.php';
include 'config.php';
include 'header.php'; 

$res_rutinas = $conexion->query("SELECT * FROM rutinas ORDER BY id_rutina DESC");
?>

<div class="page-wrapper">
    <div class="container-xl">
        <div class="page-header d-print-none">
            <div class="row align-items-center">
                <div class="col">
                    <h2 class="page-title text-pink">Gestión de Rutinas y Planes</h2>
                </div>
                <div class="col-auto ms-auto">
                    <button class="btn btn-pink text-white" data-bs-toggle="modal" data-bs-target="#modal-nueva-rutina">
                        <i class="ti ti-plus me-2"></i> Nueva Rutina
                    </button>
                </div>
            </div>
        </div>
        
        <div class="page-body">
            <div class="card">
                <div class="table-responsive">
                    <table class="table table-vcenter card-table">
                        <thead>
                            <tr>
                                <th>Nombre</th>
                                <th>Objetivo</th>
                                <th class="w-1">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($r = $res_rutinas->fetch_assoc()): ?>
                            <tr>
                                <td><strong><?php echo $r['nombre_rutina']; ?></strong></td>
                                <td class="text-muted"><?php echo $r['objetivo']; ?></td>
                                <td>
                                    <div class="btn-list flex-nowrap">
                                        <a href="gestionar_ejercicios.php?id=<?php echo $r['id_rutina']; ?>" class="btn btn-white text-pink">
                                            <i class="ti ti-list-details text-pink me-1"></i> Ejercicios
                                        </a>
                                        <a href="#" class="btn btn-white text-pink btn-icon"><i class="ti ti-trash me-1"></i></a>
                                    </div>
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

<div class="modal modal-blur fade" id="modal-nueva-rutina" tabindex="-1">
    <div class="modal-dialog">
        <form action="guardar_rutina.php" method="POST" class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Crear Nuevo Plan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Nombre de la Rutina</label>
                    <input type="text" name="nombre" class="form-control" placeholder="Ej: Quema Grasa Nivel 1" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Objetivo</label>
                    <select name="objetivo" class="form-select">
                        <option value="Pérdida de peso">Pérdida de peso</option>
                        <option value="Ganancia muscular">Ganancia muscular</option>
                        <option value="Resistencia">Resistencia</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary">Guardar Rutina</button>
            </div>
        </form>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/@tabler/core@1.0.0-beta17/dist/js/tabler.min.js"></script>
<?php include 'footer.php'; ?>