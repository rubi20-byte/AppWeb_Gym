<?php 
include 'config.php';
include 'validar_entrenador.php';
include 'header_entrenador.php'; 

$id_rutina = $_GET['id'];

// Obtener datos de la rutina
$rutina = $conexion->query("SELECT * FROM rutinas WHERE id_rutina = $id_rutina")->fetch_assoc();

$ejercicios = $conexion->query("SELECT * FROM rutina_ejercicio WHERE id_rutina = $id_rutina ORDER BY orden ASC");
?>

<div class="page-wrapper">
    <div class="container-xl mt-4">
        <div class="mb-3">
            <a href="rutinas_entrenador.php" class="btn btn-secondary">
                <i class="ti ti-arrow-left me-2"></i> Volver a Rutinas
            </a>
        </div>

        <div class="page-header mb-4">
            <h2 class="page-title text-pink fw-bold">Ejercicios de: <?php echo $rutina['nombre_rutina']; ?></h2>
            <p class="text-muted">Objetivo: <span class="badge bg-pink-lt"><?php echo $rutina['objetivo']; ?></span></p>
        </div>

        <div class="row row-cards">
            <div class="col-md-4">
                <form action="guardar_rutina_ejercicio.php" method="POST" class="card shadow-sm border-0" style="border-radius: 12px;">
                    <input type="hidden" name="id_rutina" value="<?php echo $id_rutina; ?>">
                    <div class="card-header bg-pink-lt"><h3 class="card-title fw-bold text-pink">Agregar Ejercicio</h3></div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Nombre del Ejercicio</label>
                            <input type="text" name="nombre_ejercicio" class="form-control" placeholder="Ej: Sentadilla Búlgara" required>
                        </div>
                        <div class="row">
                            <div class="col-6 mb-3">
                                <label class="form-label">Series</label>
                                <input type="number" name="series" class="form-control" value="4">
                            </div>
                            <div class="col-6 mb-3">
                                <label class="form-label">Reps</label>
                                <input type="text" name="repeticiones" class="form-control" placeholder="12-15">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Tiempo de Descanso</label>
                            <select name="descanso" class="form-select">
                                <option value="30 seg">30 seg</option>
                                <option value="45 seg">45 seg</option>
                                <option value="1 min" selected>1 min</option>
                                <option value="1:30 min">1:30 min</option>
                                <option value="2 min">2 min</option>
                                <option value="3 min">3 min</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Video (YouTube)</label>
                            <input type="text" name="url_video" class="form-control" placeholder="Link opcional...">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Orden</label>
                            <input type="number" name="orden" class="form-control" value="1">
                        </div>
                    </div>
                    <div class="card-footer text-end">
                        <button type="submit" class="btn btn-pink w-100 shadow-sm">Añadir a la lista</button>
                    </div>
                </form>
            </div>

            <div class="col-md-8">
                <div class="card shadow-sm border-0" style="border-radius: 12px;">
                    <div class="table-responsive">
                        <table class="table table-vcenter card-table">
                            <thead>
                                <tr>
                                    <th>Orden</th>
                                    <th>Ejercicio</th>
                                    <th>Sets/Reps</th>
                                    <th>Descanso</th>
                                    <th>Video</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if($ejercicios->num_rows > 0): ?>
                                    <?php while($ej = $ejercicios->fetch_assoc()): ?>
                                    <tr>
                                        <td><span class="badge bg-light text-dark"><?php echo $ej['orden']; ?></span></td>
                                        <td><strong><?php echo $ej['nombre_ejercicio']; ?></strong></td>
                                        <td><?php echo $ej['series'] . " x " . $ej['repeticiones']; ?></td>
                                        <td>
                                            <span class="badge bg-azure-lt">
                                                <?php echo htmlspecialchars($ej['descanso']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php if($ej['url_video']): ?>
                                                <a href="<?php echo $ej['url_video']; ?>" target="_blank" class="badge bg-red-lt">Ver</a>
                                            <?php else: ?>
                                                <span class="text-muted small">N/A</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-end">
                                            <a href="eliminar_ejercicio.php?id=<?php echo $ej['id_relacion']; ?>&id_rutina=<?php echo $id_rutina; ?>" 
                                               class="text-danger" onclick="return confirm('¿Eliminar ejercicio?')">
                                                <i class="ti ti-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr><td colspan="5" class="text-center py-4 text-muted">Aún no hay ejercicios agregados.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>