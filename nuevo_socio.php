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

    // LÓGICA DE FECHA CORREGIDA
    $mem_res = $conexion->query("SELECT duracion_meses FROM membresias WHERE id_membresia = $id_mem");
    if($mem_res && $mem_res->num_rows > 0){
        $m = $mem_res->fetch_assoc();
        $meses = $m['duracion_meses']; // Si es anual, esto debe ser 12 en tu BD
        $f_ven = date('Y-m-d', strtotime($f_reg . " + $meses month")); 
    } else {
        $f_ven = $f_reg;
    }

    $sql = "INSERT INTO socios (nombre, apellido, telefono, contacto_emergencia, correo, direccion, fecha_nacimiento, fecha_registro, fecha_vencimiento, id_membresia, id_entrenador, qr_codigo, foto, estado) 
            VALUES ('$nom', '$ape', '$tel', '$con_e', '$cor', '$dir', '$f_nac', '$f_reg', '$f_ven', $id_mem, $id_ent, '$codigo_qr', '$nombre_foto', 'activo')";
    
    if ($conexion->query($sql)) {
        echo "<script>window.location='socios.php';</script>";
    } else {
        die("Error al guardar: " . $conexion->error);
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
                        <small class="text-muted">La foto se mostrará como avatar en la lista.</small>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Nombre(s)</label>
                        <input type="text" name="nombre" class="form-control" required placeholder="Ej. Juan">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Apellido(s)</label>
                        <input type="text" name="apellido" class="form-control" required placeholder="Ej. Pérez">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Teléfono</label>
                        <input type="text" name="telefono" class="form-control" placeholder="834 000 0000">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Contacto de Emergencia</label>
                        <input type="text" name="contacto_emergencia" class="form-control" placeholder="834 000 0000">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Correo Electrónico</label>
                        <input type="email" name="correo" class="form-control" placeholder="socio@ejemplo.com">
                    </div>

                    <div class="col-md-8 mb-3">
                        <label class="form-label">Dirección</label>
                        <input type="text" name="direccion" class="form-control" placeholder="Calle, Número y Colonia">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Fecha de Nacimiento</label>
                        <input type="date" name="fecha_nacimiento" class="form-control" required>
                    </div>

                    <div class="hr-text text-blue">Asignación de Plan y Staff</div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Plan de Membresía</label>
                        <select name="id_membresia" class="form-select" required>
                            <option value="">Seleccione un plan...</option>
                            <?php 
                            $mems = $conexion->query("SELECT * FROM membresias");
                            while($m = $mems->fetch_assoc()){
                                echo "<option value='{$m['id_membresia']}'>{$m['nombre']} - \${$m['precio']}</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label text-purple font-weight-bold">Entrenador Personal</label>
                        <select name="id_entrenador" class="form-select border-purple">
                            <option value="">-- Sin entrenador (Libre) --</option>
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
                <button type="submit" class="btn btn-primary">Registrar y Finalizar</button>
            </div>
        </form>
    </div>
</div>
<?php include 'footer.php'; ?>