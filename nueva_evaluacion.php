<?php 
include 'config.php';
include 'header.php'; 

// 1. Obtener IDs de la URL
$id_socio = isset($_GET['id_socio']) ? $_GET['id_socio'] : '';
$id_ent = isset($_GET['id_ent']) ? $_GET['id_ent'] : '';

// 2. Consultar nombre del socio para el encabezado
$res_socio = $conexion->query("SELECT nombre FROM socios WHERE id_socio = '$id_socio'");
$socio = $res_socio->fetch_assoc();

// 3. Procesar el formulario cuando se presione "Guardar"
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $fecha = $_POST['fecha'];
    $peso = $_POST['peso'];
    $cintura = $_POST['cintura'];
    $cadera = $_POST['cadera'];
    $grasa = $_POST['grasa'];
    $musculo = $_POST['musculo'];
    $notas = $_POST['notas'];

    $sql = "INSERT INTO evaluaciones (id_socio, fecha_evaluacion, peso, cintura, cadera, porcentaje_grasa, porcentaje_musculo, comentarios) 
            VALUES ('$id_socio', '$fecha', '$peso', '$cintura', '$cadera', '$grasa', '$musculo', '$notas')";

    if ($conexion->query($sql)) {
        echo "<script>alert('Evaluación guardada con éxito'); window.location='seguimiento_entrenador.php?id=$id_ent';</script>";
    } else {
        echo "<div class='alert alert-danger'>Error al guardar: " . $conexion->error . "</div>";
    }
}
?>

<div class="page-wrapper">
    <div class="container-xl mt-4">
        <div class="row align-items-center mb-4">
            <div class="col">
                <h2 class="page-title">Nueva Evaluación Física</h2>
                <div class="text-muted mt-1">Socio: <strong><?php echo $socio['nombre']; ?></strong></div>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-body">
                <form method="POST">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Fecha de Evaluación</label>
                            <input type="date" name="fecha" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Peso (kg)</label>
                            <input type="number" step="0.1" name="peso" class="form-control" placeholder="0.0" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Cintura (cm)</label>
                            <input type="number" step="0.1" name="cintura" class="form-control" placeholder="0.0">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Cadera (cm)</label>
                            <input type="number" step="0.1" name="cadera" class="form-control" placeholder="0.0">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">% Grasa Corporal</label>
                            <input type="number" step="0.1" name="grasa" class="form-control" placeholder="0.0">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">% Masa Muscular</label>
                            <input type="number" step="0.1" name="musculo" class="form-control" placeholder="0.0">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Notas o Comentarios</label>
                        <textarea name="notas" class="form-control" rows="3" placeholder="Observaciones del entrenador..."></textarea>
                    </div>

                    <div class="form-footer text-end">
                        <a href="seguimiento_entrenador.php?id=<?php echo $id_ent; ?>" class="btn btn-link">Cancelar</a>
                        <button type="submit" class="btn btn-purple text-white">
                            <i class="ti ti-device-floppy me-2"></i>Guardar Evaluación
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>