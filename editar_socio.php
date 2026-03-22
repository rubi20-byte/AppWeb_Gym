<?php 
include 'config.php';
include 'validar_admin.php'; 
include 'header.php'; 

$error_msg = "";

// 1. Obtener los datos actuales del socio
if (isset($_GET['id'])) {
    $id = mysqli_real_escape_string($conexion, $_GET['id']);
    $resultado = $conexion->query("SELECT * FROM socios WHERE id_socio = $id");
    $socio = $resultado->fetch_assoc();

    if (!$socio) {
        echo "<script>window.location='socios.php';</script>";
        exit;
    }
}

if ($_POST) {
    $id = $_POST['id_socio'];
    $nom = mysqli_real_escape_string($conexion, strip_tags($_POST['nombre']));
    $ape = mysqli_real_escape_string($conexion, strip_tags($_POST['apellido']));
    $tel = mysqli_real_escape_string($conexion, $_POST['telefono']);
    $tel_emergencia = mysqli_real_escape_string($conexion, $_POST['contacto_emergencia']);
    $cor = mysqli_real_escape_string($conexion, $_POST['correo']);
    $dir = mysqli_real_escape_string($conexion, $_POST['direccion']);
    $f_nac = $_POST['fecha_nacimiento'];
    $mem_id = $_POST['id_membresia'];
    $ent_id = ($_POST['id_entrenador'] == "") ? "NULL" : $_POST['id_entrenador']; 
    $est = $_POST['estado'];
    $f_hoy = date('Y-m-d');

    // --- NUEVAS VALIDACIONES ---
    // Verificar si el correo está vacío o si ya lo tiene otro socio (excluyendo al socio actual)
    $check_correo = $conexion->query("SELECT id_socio FROM socios WHERE correo = '$cor' AND id_socio != $id");
    
    if (empty($cor)) {
        $error_msg = "El correo electrónico es obligatorio para que el socio acceda a su panel.";
    } elseif ($check_correo->num_rows > 0) {
        $error_msg = "Este correo ya está registrado por otro socio.";
    } else {
        // --- CONTINÚA TU LÓGICA DE ACTUALIZACIÓN ---
        $sql_fecha_vencimiento = "";
        if ($mem_id != $socio['id_membresia']) {
            $mem_res = $conexion->query("SELECT duracion_meses FROM membresias WHERE id_membresia = $mem_id");
            if ($mem_res && $mem_res->num_rows > 0) {
                $m = $mem_res->fetch_assoc();
                $meses = $m['duracion_meses'];
                $nueva_fecha = date('Y-m-d', strtotime("+ $meses month"));
                $sql_fecha_vencimiento = ", fecha_vencimiento = '$nueva_fecha'";

                $sql_historial = "INSERT INTO socios_membresias (id_socio, id_membresia, fecha_inicio, fecha_fin, estado) 
                                  VALUES ($id, $mem_id, '$f_hoy', '$nueva_fecha', 'activa')";
                $conexion->query($sql_historial);
            }
        }

        $sql_update_foto = "";
        if (isset($_FILES['foto']) && $_FILES['foto']['name'] != "") {
            $ext = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
            $nombre_foto = "foto_" . time() . "_" . $id . "." . $ext;
            if (!file_exists('uploads/fotos/')) { mkdir('uploads/fotos/', 0777, true); }

            if(move_uploaded_file($_FILES['foto']['tmp_name'], "uploads/fotos/" . $nombre_foto)){
                $sql_update_foto = ", foto = '$nombre_foto'";
            }
        }

        $sql = "UPDATE socios SET 
                nombre = '$nom', 
                apellido = '$ape', 
                telefono = '$tel', 
                contacto_emergencia = '$tel_emergencia',
                correo = '$cor', 
                direccion = '$dir', 
                fecha_nacimiento = '$f_nac', 
                id_membresia = '$mem_id',
                id_entrenador = $ent_id,
                estado = '$est'
                $sql_fecha_vencimiento
                $sql_update_foto
                WHERE id_socio = $id";
        
        if ($conexion->query($sql)) {
            echo "<script>window.location='socios.php?res=editado';</script>";
            exit;
        } else {
            $error_msg = "Error en la base de datos: " . $conexion->error;
        }
    }
}

$query_entrenadores = "SELECT id_entrenador, nombre FROM entrenadores WHERE estado = 'activo' ORDER BY nombre ASC";
$resultado_entrenadores = $conexion->query($query_entrenadores);
?>

<div class="page-wrapper">
    <div class="container-xl mt-4">
        
        <?php if($error_msg != ""): ?>
            <div class="alert alert-important alert-danger shadow-sm col-md-10 mx-auto mb-3">
                <i class="ti ti-alert-triangle me-2"></i> <?php echo $error_msg; ?>
            </div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data" class="card col-md-10 mx-auto shadow">
            <div class="card-header bg-yellow-lt">
                <h3 class="card-title">Editar Información del Socio #<?php echo $socio['id_socio']; ?></h3>
            </div>
            <div class="card-body">
                <input type="hidden" name="id_socio" value="<?php echo $socio['id_socio']; ?>">
                
                <div class="row">
                    <div class="col-md-12 mb-4 text-center">
                        <label class="form-label">Foto de Perfil</label>
                        <div class="mb-2">
                            <?php 
                            $foto_path = (!empty($socio['foto']) && file_exists("uploads/fotos/".$socio['foto'])) 
                                         ? "uploads/fotos/".$socio['foto'] 
                                         : "https://cdn-icons-png.flaticon.com/512/3135/3135715.png";
                            ?>
                            <span class="avatar avatar-xl rounded-circle shadow-sm" style="background-image: url(<?php echo $foto_path; ?>)"></span>
                        </div>
                        <input type="file" name="foto" class="form-control w-50 mx-auto" accept="image/*">
                        <small class="text-muted">Sube una nueva foto solo si deseas cambiar la actual.</small>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Nombre(s)</label>
                        <input type="text" name="nombre" class="form-control" value="<?php echo $socio['nombre']; ?>" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Apellido(s)</label>
                        <input type="text" name="apellido" class="form-control" value="<?php echo $socio['apellido']; ?>" required>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="form-label text-primary">Correo (Usuario de Acceso)</label>
                        <input type="email" name="correo" class="form-control border-primary" value="<?php echo $socio['correo']; ?>" required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Teléfono</label>
                        <input type="text" name="telefono" class="form-control" value="<?php echo $socio['telefono']; ?>">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Contacto de Emergencia</label>
                        <input type="text" name="contacto_emergencia" class="form-control" value="<?php echo $socio['contacto_emergencia']; ?>">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Fecha de Nacimiento</label>
                        <input type="date" name="fecha_nacimiento" class="form-control" value="<?php echo $socio['fecha_nacimiento']; ?>">
                    </div>
                    
                    <div class="col-md-12 mb-3">
                        <label class="form-label">Dirección</label>
                        <input type="text" name="direccion" class="form-control" value="<?php echo $socio['direccion']; ?>">
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label">Plan Actual</label>
                        <select name="id_membresia" class="form-select">
                            <?php 
                            $mems = $conexion->query("SELECT * FROM membresias");
                            while($m = $mems->fetch_assoc()){
                                $selected = ($m['id_membresia'] == $socio['id_membresia']) ? 'selected' : '';
                                echo "<option value='{$m['id_membresia']}' $selected>{$m['nombre']} ($" . number_format($m['precio'],2) . ")</option>";
                            }
                            ?>
                        </select>
                        <small class="text-blue">Si cambias el plan, se actualizará el vencimiento.</small>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label">Entrenador Asignado</label>
                        <select name="id_entrenador" class="form-select">
                            <option value="">-- Sin Entrenador --</option>
                            <?php 
                            $resultado_entrenadores->data_seek(0);
                            while($e = $resultado_entrenadores->fetch_assoc()): ?>
                                <option value="<?php echo $e['id_entrenador']; ?>" <?php echo ($e['id_entrenador'] == $socio['id_entrenador']) ? 'selected' : ''; ?>>
                                    <?php echo $e['nombre']; ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label">Estado</label>
                        <select name="estado" class="form-select">
                            <option value="activo" <?php if($socio['estado'] == 'activo') echo 'selected'; ?>>Activo</option>
                            <option value="inactivo" <?php if($socio['estado'] == 'inactivo') echo 'selected'; ?>>Inactivo</option>
                            <option value="vencido" <?php if($socio['estado'] == 'vencido') echo 'selected'; ?>>Vencido</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="card-footer text-end">
                <a href="socios.php" class="btn btn-link">Cancelar</a>
                <button type="submit" class="btn btn-warning">Actualizar Datos</button>
            </div>
        </form>
    </div>
</div>

<?php include 'footer.php'; ?>