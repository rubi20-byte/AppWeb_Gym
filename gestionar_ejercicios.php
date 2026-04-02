<?php 
include 'validar_admin.php';
include 'config.php';
include 'header.php'; 

$id_rutina = $_GET['id'];

// Obtener datos de la rutina
$rutina = $conexion->query("SELECT * FROM rutinas WHERE id_rutina = $id_rutina")->fetch_assoc();

// Obtener ejercicios ya agregados
$ejercicios = $conexion->query("SELECT * FROM rutina_ejercicio WHERE id_rutina = $id_rutina ORDER BY orden ASC");
?>

<div class="page-wrapper">
    <div class="container-xl"><br>
        <a href="admin_rutinas.php" class="btn btn-secondary">
        <i class="ti ti-arrow-left me-2"></i> Volver
        </a>
        <div class="page-header">
            <h2 class="page-title text-pink">Ejercicios de: <?php echo $rutina['nombre_rutina']; ?></h2>
            <p class="text-muted">Objetivo: <?php echo $rutina['objetivo']; ?></p>
        </div>
        <div class="row row-cards">
            <div class="col-md-4">
                <form action="guardar_rutina_ejercicio.php" method="POST" class="card">
                    <input type="hidden" name="id_rutina" value="<?php echo $id_rutina; ?>">
                    <div class="card-header"><h3 class="card-title text-pink">Agregar Ejercicio</h3></div>
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
                            <label class="form-label">Link de Video (YouTube)</label>
                            <input type="text" name="url_video" class="form-control" placeholder="https://youtube.com/...">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Orden</label>
                            <input type="number" name="orden" class="form-control" value="1">
                        </div>
                    </div>
                    <div class="card-footer text-end">
                        <button type="submit" class="btn btn-pink">Añadir a la lista</button>
                    </div>
                </form>
            </div>

            <div class="col-md-8">
                <div class="card">
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
                                <?php while($ej = $ejercicios->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo $ej['orden']; ?></td>
                                    <td><strong><?php echo $ej['nombre_ejercicio']; ?></strong></td>
                                    <td><?php echo $ej['series'] . " x " . $ej['repeticiones']; ?></td>
                                    <td><span class="badge bg-azure-lt">
                                        <?php echo htmlspecialchars($ej['descanso']); ?>
                                    </span></td>
                                    <td>
                                        <?php if($ej['url_video']): ?>
                                            <a href="<?php echo $ej['url_video']; ?>" target="_blank" class="badge bg-red-lt">Ver Video</a>
                                        <?php else: ?>
                                            <span class="text-muted small">Sin video</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-end">
                                        <a href="eliminar_ejercicio.php?id=<?php echo $ej['id_relacion']; ?>&id_rutina=<?php echo $id_rutina; ?>" class="ti ti-trash text-danger"></a>
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