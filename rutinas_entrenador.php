<?php
include 'config.php';
include 'validar_entrenador.php';
include 'header_entrenador.php';

// Consultar todas las rutinas
$query_rutinas = "SELECT * FROM rutinas ORDER BY id_rutina DESC";
$res_rutinas = $conexion->query($query_rutinas);
?>

<div class="page-wrapper">
    <div class="container-xl mt-4">
        <div class="page-header mb-4">
            <div class="row align-items-center">
                <div class="col">
                    <h2 class="page-title text-pink fw-bold">Gestión de Rutinas</h2>
                </div>
                <div class="col-auto">
                    <button class="btn btn-pink shadow-sm" data-bs-toggle="modal" data-bs-target="#modal-nueva-rutina">
                        <i class="ti ti-plus me-1"></i> Crear Nueva Rutina
                    </button>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm" style="border-radius: 15px;">
            <div class="table-responsive">
                <table class="table table-vcenter card-table">
                    <thead>
                        <tr>
                            <th>Nombre del Plan</th>
                            <th>Objetivo</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($rutina = $res_rutinas->fetch_assoc()): 
                            $id_r = $rutina['id_rutina'];
                        ?>
                        <tr>
    <td class="fw-bold"><?php echo $rutina['nombre_rutina']; ?></td>
    <td class="text-muted small"><?php echo $rutina['objetivo']; ?></td>
    <td class="text-center">
        <a href="gestionar_ejercicios_ent.php?id=<?php echo $id_r; ?>" class="btn btn-white text-pink shadow-sm">
            <i class="ti ti-stretching me-2"></i> Ejercicios
        </a>
    </td>
</tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal modal-blur fade" id="modal-nueva-rutina" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <form action="guardar_cabecera_rutina.php" method="POST" class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold text-pink">Nuevo Plan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Nombre de la Rutina</label>
                    <input type="text" name="nombre_rutina" class="form-control" placeholder="Ej: Quema Grasa Nivel 1" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Objetivo</label>
                    <select name="objetivo" class="form-select">
                        <option value="Pérdida de peso">Pérdida de peso</option>
                        <option value="Ganancia muscular">Ganancia muscular</option>
                        <option value="Resistencia">Resistencia</option>
                        <option value="Definición">Definición</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-link link-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-pink ms-auto">Guardar Rutina</button>
            </div>
        </form>
    </div>
</div>

<?php include 'footer.php'; ?>