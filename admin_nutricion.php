<?php
include 'validar_admin.php';
include 'config.php';
include 'header.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['btn_guardar'])) {
    $objetivo = $_POST['objetivo'];
    $momento = $_POST['momento'];
    $alimento = mysqli_real_escape_string($conexion, $_POST['nombre_alimento']);
    $detalle = mysqli_real_escape_string($conexion, $_POST['detalle_alimento']);
    $kcal = intval($_POST['calorias']);

    $sql = "INSERT INTO sugerencias_comidas (objetivo, momento, nombre_alimento, detalle_alimento, calorias) 
            VALUES ('$objetivo', '$momento', '$alimento', '$detalle', '$kcal')";
    
    if ($conexion->query($sql)) {
        echo "<script>window.location='admin_nutricion.php?msg=ok';</script>";
    }
}
?>

<div class="page-wrapper">
    <div class="container-xl mt-4">
        <div class="row align-items-center mb-3">
            <div class="col">
                <h2 class="page-title text-lime">Gestión de Planes Nutricionales</h2>
            </div>
            <div class="col-auto">
                <button type="button" class="btn btn-lime bg-lime-lt" data-bs-toggle="modal" data-bs-target="#modal-comida">
                    <i class="ti ti-plus me-2"></i>Nueva Comida Sugerida
                </button>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="table-responsive">
                <table class="table table-vcenter card-table">
                    <thead>
                        <tr>
                            <th>OBJETIVO</th>
                            <th>MOMENTO</th>
                            <th>ALIMENTO</th>
                            <th>KCAL</th>
                            <th class="w-1">ACCIONES</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $res = $conexion->query("SELECT * FROM sugerencias_comidas ORDER BY objetivo, momento ASC");
                        if($res->num_rows > 0):
                            while($row = $res->fetch_assoc()): 
                        ?>
                        <tr>
                            <td><span class="badge bg-blue-lt"><?php echo $row['objetivo']; ?></span></td>
                            <td><span class="badge bg-gray-lt"><?php echo $row['momento']; ?></span></td>
                            <td>
                                <div class="fw-bold"><?php echo $row['nombre_alimento']; ?></div>
                                <div class="text-muted small"><?php echo $row['detalle_alimento']; ?></div>
                            </td>
                            <td class="fw-bold text-dark"><?php echo $row['calorias']; ?></td>
                            <td>
                                <a href="eliminar_nutricion.php?id=<?php echo $row['id_sugerencia']; ?>" 
                                   class="btn-white text-red btn btn-icon" 
                                   onclick="return confirm('¿Eliminar esta sugerencia?')">
                                    <i class="ti ti-trash"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endwhile; else: ?>
                            <tr><td colspan="5" class="text-center py-4 text-muted">No hay comidas cargadas aún.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal modal-blur fade" id="modal-comida" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <form method="POST">
        <div class="modal-header">
          <h5 class="modal-title">Cargar Nueva Comida al Plan</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-lg-6 mb-3">
              <label class="form-label">Objetivo Fitness</label>
              <select name="objetivo" class="form-select" required>
                <option value="Bajar Peso">Bajar Peso</option>
                <option value="Mantener">Mantener</option>
                <option value="Ganar Musculo">Ganar Músculo</option>
              </select>
            </div>
            <div class="col-lg-6 mb-3">
              <label class="form-label">Momento</label>
              <select name="momento" class="form-select" required>
                <option value="Desayuno">Desayuno</option>
                <option value="Almuerzo">Comida</option>
                <option value="Cena">Cena</option>
                <option value="Snack">Snack</option>
              </select>
            </div>
          </div>
          <div class="mb-3">
            <label class="form-label">Nombre del Platillo</label>
            <input type="text" name="nombre_alimento" class="form-control" placeholder="Ej: Pechuga con Arroz" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Detalles / Cantidades</label>
            <textarea name="detalle_alimento" class="form-control" rows="2" placeholder="Ej: 200g pollo, 1 taza arroz..."></textarea>
          </div>
          <div class="mb-3">
            <label class="form-label">Calorías Totales</label>
            <input type="number" name="calorias" class="form-control" required>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-link link-secondary me-auto" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" name="btn_guardar" class="btn btn-lime">Guardar Sugerencia</button>
        </div>
      </form>
    </div>
  </div>
</div>

<?php include 'footer.php'; ?>