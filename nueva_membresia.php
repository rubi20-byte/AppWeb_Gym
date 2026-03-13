<?php 
include 'config.php';
include 'header.php'; 

if ($_POST) {
    $nom = mysqli_real_escape_string($conexion, $_POST['nombre']);
    $dur = (int)$_POST['duracion_meses'];
    $pre = (float)$_POST['precio'];
    $est = $_POST['estado'];

    $sql = "INSERT INTO membresias (nombre, duracion_meses, precio, estado) 
            VALUES ('$nom', $dur, $pre, '$est')";
    
    if ($conexion->query($sql)) {
        echo "<script>window.location='membresias.php';</script>";
    }
}
?>

<div class="page-wrapper">
    <div class="container-xl mt-4">
        <form method="POST" class="card col-md-6 mx-auto shadow">
            <div class="card-header bg-primary-lt"><h3 class="card-title">Crear Nuevo Plan</h3></div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label">Nombre de la Membresía</label>
                    <input type="text" name="nombre" class="form-control" placeholder="Ej. Plan Anual VIP" required>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Duración (Meses)</label>
                        <input type="number" name="duracion_meses" class="form-control" min="1" max="24" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Precio (MXN)</label>
                        <input type="number" step="0.01" name="precio" class="form-control" required>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Estado Inicial</label>
                    <select name="estado" class="form-select">
                        <option value="activo">Activo (Disponible para socios)</option>
                        <option value="inactivo">Inactivo (Ocultar)</option>
                    </select>
                </div>
            </div>
            <div class="card-footer text-end">
                <a href="membresias.php" class="btn btn-link">Cancelar</a>
                <button type="submit" class="btn btn-primary">Guardar Plan</button>
            </div>
        </form>
    </div>
</div>
<?php include 'footer.php'; ?>