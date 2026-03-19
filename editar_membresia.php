<?php 
include 'config.php';
include 'validar.php'; // este archivo valida que seas admin para entrar a esta pagina
include 'header.php'; 


$id = $_GET['id'];
$resultado = $conexion->query("SELECT * FROM membresias WHERE id_membresia = $id");
$m = $resultado->fetch_assoc();

if ($_POST) {
    $nom = mysqli_real_escape_string($conexion, $_POST['nombre']);
    $dur = (int)$_POST['duracion_meses'];
    $pre = (float)$_POST['precio'];
    $des = mysqli_real_escape_string($conexion, $_POST['descripcion']);
    $est = $_POST['estado'];

    $sql = "UPDATE membresias SET 
            nombre='$nom', duracion_meses=$dur, precio=$pre, descripcion='$des', estado='$est' 
            WHERE id_membresia=$id";
    
    if ($conexion->query($sql)) {
        echo "<script>window.location='membresias.php';</script>";
    }
}
?>
<div class="page-wrapper">
    <div class="container-xl mt-4">
        <form method="POST" class="card col-md-8 mx-auto shadow">
            <div class="card-header bg-yellow-lt">
                <h3 class="card-title">Editar Plan: <?php echo $m['nombre']; ?></h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label class="form-label">Nombre</label>
                        <input type="text" name="nombre" class="form-control" value="<?php echo $m['nombre']; ?>" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Meses</label>
                        <input type="number" name="duracion_meses" class="form-control" value="<?php echo $m['duracion_meses']; ?>" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Precio</label>
                        <input type="number" step="0.01" name="precio" class="form-control" value="<?php echo $m['precio']; ?>" required>
                    </div>
                    <div class="col-md-12 mb-3">
                        <label class="form-label">Descripción</label>
                        <textarea name="descripcion" class="form-control" rows="2"><?php echo $m['descripcion']; ?></textarea>
                    </div>
                    <div class="col-md-12">
                        <label class="form-label">Estado</label>
                        <select name="estado" class="form-select">
                            <option value="activo" <?php echo ($m['estado']=='activo')?'selected':''; ?>>Activo</option>
                            <option value="inactivo" <?php echo ($m['estado']=='inactivo')?'selected':''; ?>>Inactivo</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="card-footer text-end">
                <button type="submit" class="btn btn-warning">Actualizar Plan</button>
            </div>
        </form>
    </div>
</div>
<?php include 'footer.php'; ?>