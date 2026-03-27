<?php 
include 'config.php';
include 'validar_admin.php'; 
include 'header.php'; 

// 1. Obtener el ID del entrenador desde la URL
$id_ent = isset($_GET['id']) ? $_GET['id'] : '';

// 2. Consultar el nombre del entrenador
$res_ent = $conexion->query("SELECT nombre FROM entrenadores WHERE id_entrenador = '$id_ent'");
$ent = $res_ent->fetch_assoc();
?>

<div class="page-wrapper">
    <div class="container-xl mt-4">
        <div class="row align-items-center mb-4">
            <div class="col">
                <h2 class="page-title text-purple">Socios Asignados</h2>
                <div class="text-muted mt-1">Entrenador: <strong><?php echo $ent['nombre']; ?></strong></div>
            </div>
            <div class="col-auto">
                <a href="entrenadores.php" class="btn btn-secondary">
                    <i class="ti ti-arrow-back me-2"></i>Volver
                </a>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="table-responsive">
                <?php if(isset($_GET['status']) && $_GET['status'] == 'success'): ?>
                    <div class="alert alert-success alert-dismissible" role="alert">
                        <div class="d-flex">
                            <div><i class="ti ti-check me-2"></i></div>
                            <div>Rutina actualizada correctamente.</div>
                        </div>
                        <a class="btn-close" data-bs-dismiss="modal" aria-label="close"></a>
                    </div>
                <?php endif; ?>
                <table class="table card-table table-vcenter text-nowrap">
                    <thead>
                        <tr>
                            <th>Nombre del Socio</th>
                            <th>Estado</th>
                            <th>Teléfono</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        // 3. Consulta con LEFT JOIN para saber qué rutina tiene actualmente
                        $res = $conexion->query("SELECT s.*, r.nombre_rutina 
                                               FROM socios s 
                                               LEFT JOIN rutinas r ON s.id_rutina = r.id_rutina 
                                               WHERE s.id_entrenador = '$id_ent'");

                        if($res && $res->num_rows > 0): 
                            while($s = $res->fetch_assoc()): 
                        ?>
                        <tr>
                            <td>
                                <strong><?php echo $s['nombre'] . " " . $s['apellido']; ?></strong>
                                <div class="text-muted small">Rutina: <?php echo $s['nombre_rutina'] ?? 'Ninguna'; ?></div>
                            </td>
                            <td>
                                <span class="badge bg-green-lt"><?php echo strtoupper($s['estado']); ?></span>
                            </td>
                            <td class="text-muted">
                                <?php echo $s['telefono']; ?>
                            </td>
                            <td class="text-center">
                                <div class="btn-list justify-content-center">
                                    <button class="btn btn-pink btn-sm" 
                                            onclick="abrirModalRutina('<?php echo $s['id_socio']; ?>', '<?php echo $s['nombre'].' '.$s['apellido']; ?>', '<?php echo $s['id_rutina']; ?>')">
                                        <i class="ti ti-stretching me-1"></i> Rutina
                                    </button>

                                    <a href="nueva_evaluacion.php?id_socio=<?php echo $s['id_socio']; ?>&id_ent=<?php echo $id_ent; ?>" 
                                       class="btn btn-warning btn-sm" title="Nueva Evaluación">
                                        <i class="ti ti-plus me-1"></i> Evaluar
                                    </a>
                                    <a href="ver_progreso.php?id=<?php echo $s['id_socio']; ?>" 
                                       class="btn btn-info btn-sm" title="Ver Historial">
                                        <i class="ti ti-history me-1"></i> Historial
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endwhile; else: ?>
                            <tr>
                                <td colspan="5" class="text-center py-4">
                                    No hay socios asignados a este entrenador.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal modal-blur fade" id="modal-rutina" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
        <form class="modal-content shadow-lg" action="guardar_rutina_socio.php" method="POST">
            <div class="modal-header">
                <h5 class="modal-title">Asignar Rutina</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Socio: <strong id="lbl-socio"></strong></label>
                    <input type="hidden" name="id_socio" id="input-id-socio">
                    <input type="hidden" name="id_entrenador" value="<?php echo $id_ent; ?>">
                </div>
                <div class="mb-3">
                    <label class="form-label">Selecciona la Rutina</label>
                    <select name="id_rutina" id="select-rutina" class="form-select" required>
                        <option value="">Selecciona una rutina...</option>
                        <?php 
                        $rutinas = $conexion->query("SELECT id_rutina, nombre_rutina FROM rutinas ORDER BY nombre_rutina ASC");
                        while($r = $rutinas->fetch_assoc()){
                            echo "<option value='{$r['id_rutina']}'>{$r['nombre_rutina']}</option>";
                        }
                        ?>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-link link-secondary me-auto" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-pink">Guardar Cambios</button>
            </div>
        </form>
    </div>
</div>

<script>
function abrirModalRutina(idSocio, nombreSocio, idRutinaActual) {
    document.getElementById('input-id-socio').value = idSocio;
    document.getElementById('lbl-socio').innerText = nombreSocio;
    
    // Si ya tiene una rutina, la seleccionamos en el combo
    var select = document.getElementById('select-rutina');
    select.value = idRutinaActual;

    var myModal = new bootstrap.Modal(document.getElementById('modal-rutina'));
    myModal.show();
}
</script>

<?php include 'footer.php'; ?>