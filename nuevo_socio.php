<?php 
include 'config.php';
include 'header.php'; 

if ($_POST) {
    $nom   = mysqli_real_escape_string($conexion, $_POST['nombre']);
    $ape   = mysqli_real_escape_string($conexion, $_POST['apellido']);
    $tel   = mysqli_real_escape_string($conexion, $_POST['telefono']);
    $con_e = mysqli_real_escape_string($conexion, $_POST['contacto_emergencia']);
    $cor   = mysqli_real_escape_string($conexion, $_POST['correo']);
    $dir   = mysqli_real_escape_string($conexion, $_POST['direccion']);
    $f_nac = $_POST['fecha_nacimiento'];
    $id_mem = $_POST['id_membresia'];
    $id_ent = !empty($_POST['id_entrenador']) ? $_POST['id_entrenador'] : "NULL";
    
    // NUEVO: Capturar el Titular
    $id_titular = !empty($_POST['id_titular']) ? $_POST['id_titular'] : "NULL";
    
    $f_reg = date('Y-m-d');

    // QR y FOTO
    $limpio_nom = str_replace(' ', '', strtoupper($nom));
    $limpio_ape = str_replace(' ', '', strtoupper($ape));
    $limpio_fec = str_replace('-', '', $f_nac);
    $codigo_qr = substr($limpio_nom, 0, 3) . substr($limpio_ape, 0, 3) . $limpio_fec;

    $nombre_foto = "default.png";
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
        $extension = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
        $nombre_foto = "foto_" . time() . "_" . $codigo_qr . "." . $extension;
        if (!file_exists('uploads/fotos/')) { mkdir('uploads/fotos/', 0777, true); }
        move_uploaded_file($_FILES['foto']['tmp_name'], "uploads/fotos/" . $nombre_foto);
    }

    // LÓGICA DE FECHA: Si tiene titular, hereda su vencimiento. Si no, se calcula.
    if ($id_titular != "NULL") {
        $tit_res = $conexion->query("SELECT fecha_vencimiento FROM socios WHERE id_socio = $id_titular");
        $t_data = $tit_res->fetch_assoc();
        $f_ven = $t_data['fecha_vencimiento'];
    } else {
        $mem_res = $conexion->query("SELECT duracion_meses FROM membresias WHERE id_membresia = $id_mem");
        if($mem_res && $mem_res->num_rows > 0){
            $m = $mem_res->fetch_assoc();
            $meses = $m['duracion_meses']; 
            $f_ven = date('Y-m-d', strtotime($f_reg . " + $meses month")); 
        } else {
            $f_ven = $f_reg;
        }
    }

    $sql = "INSERT INTO socios (id_titular, nombre, apellido, telefono, contacto_emergencia, correo, direccion, fecha_nacimiento, fecha_registro, fecha_vencimiento, id_membresia, id_entrenador, qr_codigo, foto, estado) 
            VALUES ($id_titular, '$nom', '$ape', '$tel', '$con_e', '$cor', '$dir', '$f_nac', '$f_reg', '$f_ven', $id_mem, $id_ent, '$codigo_qr', '$nombre_foto', 'activo')";
    
    if ($conexion->query($sql)) {
        echo "<script>window.location='socios.php';</script>";
    } else {
        die("Error al guardar: " . $conexion->error);
    }
    
    if ($conexion->query($sql)) {
        $id_nuevo_socio = $conexion->insert_id; // Obtenemos el ID
        
        // historial de membresias (socios_membresias)
        $sql_historial = "INSERT INTO socios_membresias (id_socio, id_membresia, fecha_inicio, fecha_fin, estado) 
                          VALUES ($id_nuevo_socio, $id_mem, '$f_reg', '$f_ven', 'activa')";
        $conexion->query($sql_historial);

        echo "<script>window.location='socios.php';</script>";
    }
}
?>

<div class="page-wrapper">
    <div class="container-xl mt-4">
        <form method="POST" enctype="multipart/form-data" class="card col-md-10 mx-auto shadow">
            <div class="card-header bg-primary-lt">
                <h3 class="card-title">Nueva Inscripción de Socio</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12 mb-3 text-center">
                        <label class="form-label">Foto del Socio</label>
                        <input type="file" name="foto" class="form-control" accept="image/*">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Nombre(s)</label>
                        <input type="text" name="nombre" class="form-control" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Apellido(s)</label>
                        <input type="text" name="apellido" class="form-control" required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Teléfono</label>
                        <input type="text" name="telefono" class="form-control">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Contacto Emergencia</label>
                        <input type="text" name="contacto_emergencia" class="form-control">
                    </div>

                    <div class="col-md-8 mb-3">
                        <label class="form-label">Dirección</label>
                        <input type="text" name="direccion" class="form-control">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Fecha de Nacimiento</label>
                        <input type="date" name="fecha_nacimiento" class="form-control" required>
                    </div>

                    <div class="hr-text text-blue">Vínculo Familiar y Plan</div>

                    <div class="col-md-12 mb-3">
                        <label class="form-label text-purple">¿Es dependiente de un Titular? (Opcional)</label>
                        <select name="id_titular" class="form-select border-purple">
                            <option value="">-- No, es Titular Independiente --</option>
                            <?php 
                            $titulares = $conexion->query("SELECT id_socio, nombre, apellido FROM socios WHERE id_titular IS NULL ORDER BY nombre ASC");
                            while($t = $titulares->fetch_assoc()){
                                echo "<option value='{$t['id_socio']}'>{$t['nombre']} {$t['apellido']}</option>";
                            }
                            ?>
                        </select>
                        <small class="text-muted italic">Si seleccionas un titular, este socio heredará su fecha de vencimiento.</small>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Plan de Membresía</label>
                        <select name="id_membresia" class="form-select" required>
                            <option value="">Seleccione un plan...</option>
                            <?php 
                            $mems = $conexion->query("SELECT * FROM membresias WHERE estado = 'activo'");
                            while($m = $mems->fetch_assoc()){
                                echo "<option value='{$m['id_membresia']}'>{$m['nombre']} - \${$m['precio']}</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Entrenador Personal</label>
                        <select name="id_entrenador" class="form-select">
                            <option value="">-- Sin entrenador --</option>
                            <?php 
                            $profes = $conexion->query("SELECT id_entrenador, nombre FROM entrenadores WHERE estado = 'activo'");
                            while($p = $profes->fetch_assoc()){
                                echo "<option value='{$p['id_entrenador']}'>{$p['nombre']}</option>";
                            }
                            ?>
                        </select>
                    </div>
                </div>
            </div>
            <div class="card-footer text-end">
                <a href="socios.php" class="btn btn-link">Cancelar</a>
                <button type="submit" class="btn btn-primary">Registrar Socio</button>
            </div>
        </form>
    </div>
</div>
<?php include 'footer.php'; ?>