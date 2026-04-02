<?php 
include 'config.php';
include 'validar_admin.php'; 
include 'header.php'; 

$error_msg = ""; 

if ($_POST) {
    // Limpieza de datos
    $nom   = mysqli_real_escape_string($conexion, strip_tags($_POST['nombre']));
    $ape   = mysqli_real_escape_string($conexion, strip_tags($_POST['apellido']));
    $sexo  = mysqli_real_escape_string($conexion, $_POST['sexo']); 
    $tel   = mysqli_real_escape_string($conexion, $_POST['telefono']);
    $con_e = mysqli_real_escape_string($conexion, $_POST['contacto_emergencia']);
    $cor   = mysqli_real_escape_string($conexion, $_POST['correo']); 
    $dir   = mysqli_real_escape_string($conexion, $_POST['direccion']);
    $f_nac = $_POST['fecha_nacimiento'];
    $id_mem = $_POST['id_membresia'];
    
    // Manejo de valores NULL para IDs opcionales
    $id_ent = !empty($_POST['id_entrenador']) ? $_POST['id_entrenador'] : "NULL";
    $id_titular = !empty($_POST['id_titular']) ? $_POST['id_titular'] : "NULL";
    
    $f_reg = date('Y-m-d');

    // Validación de correo duplicado
    $check_correo = $conexion->query("SELECT id_socio FROM socios WHERE correo = '$cor'");
    
    if (empty($cor)) {
        $error_msg = "El correo electrónico es obligatorio.";
    } elseif ($check_correo->num_rows > 0) {
        $error_msg = "Este correo ya está registrado con otro socio.";
    } else {
        // Generar Código QR (Primeras 3 letras nombre + 3 letras apellido + fecha nac)
        $limpio_nom = str_replace(' ', '', strtoupper($nom));
        $limpio_ape = str_replace(' ', '', strtoupper($ape));
        $limpio_fec = str_replace('-', '', $f_nac);
        $codigo_qr = substr($limpio_nom, 0, 3) . substr($limpio_ape, 0, 3) . $limpio_fec;

        // Manejo de la Foto
        $nombre_foto = "default.png";
        if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
            $extension = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
            $nombre_foto = "foto_" . time() . "_" . $codigo_qr . "." . $extension;
            if (!file_exists('uploads/fotos/')) { mkdir('uploads/fotos/', 0777, true); }
            move_uploaded_file($_FILES['foto']['tmp_name'], "uploads/fotos/" . $nombre_foto);
        }

        // Calcular Fecha de Vencimiento
        if ($id_titular != "NULL") {
            // Si depende de un titular, hereda su fecha de vencimiento
            $tit_res = $conexion->query("SELECT fecha_vencimiento FROM socios WHERE id_socio = $id_titular");
            $t_data = $tit_res->fetch_assoc();
            $f_ven = $t_data['fecha_vencimiento'];
        } else {
            // Si es independiente, se calcula según los meses del plan
            $mem_res = $conexion->query("SELECT duracion_meses FROM membresias WHERE id_membresia = $id_mem");
            if($mem_res && $mem_res->num_rows > 0){
                $m = $mem_res->fetch_assoc();
                $meses = $m['duracion_meses']; 
                $f_ven = date('Y-m-d', strtotime($f_reg . " + $meses month")); 
            } else {
                $f_ven = $f_reg;
            }
        }

        // INSERT CORREGIDO (Cambiamos 'tobacco' por 'telefono')
        $sql = "INSERT INTO socios (id_titular, nombre, apellido, sexo, telefono, contacto_emergencia, correo, direccion, fecha_nacimiento, fecha_registro, fecha_vencimiento, id_membresia, id_entrenador, qr_codigo, foto, estado) 
                VALUES ($id_titular, '$nom', '$ape', '$sexo', '$tel', '$con_e', '$cor', '$dir', '$f_nac', '$f_reg', '$f_ven', $id_mem, $id_ent, '$codigo_qr', '$nombre_foto', 'activo')";
        
        if ($conexion->query($sql)) {
            $id_nuevo_socio = $conexion->insert_id;
            // Guardar en el historial de membresías
            $sql_historial = "INSERT INTO socios_membresias (id_socio, id_membresia, fecha_inicio, fecha_fin, estado) 
                              VALUES ($id_nuevo_socio, $id_mem, '$f_reg', '$f_ven', 'activa')";
            $conexion->query($sql_historial);
            
            echo "<script>window.location='socios.php';</script>";
            exit();
        } else {
            $error_msg = "Error al registrar: " . $conexion->error;
        }
    }
}
?>

<div class="page-wrapper">
    <div class="container-xl mt-4">
        <?php if($error_msg != ""): ?>
            <div class="alert alert-important alert-danger shadow-sm col-md-10 mx-auto mb-3">
                <i class="ti ti-alert-triangle me-2"></i> <?php echo $error_msg; ?>
            </div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data" class="card col-md-10 mx-auto shadow border-0">
            <div class="card-header bg-primary text-white">
                <h3 class="card-title">Nueva Inscripción de Socio</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12 mb-4 text-center">
                        <label class="form-label fw-bold">Foto del Perfil</label>
                        <input type="file" name="foto" class="form-control" accept="image/*">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Nombre(s)</label>
                        <input type="text" name="nombre" class="form-control" placeholder="Ej. Juan" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Apellido(s)</label>
                        <input type="text" name="apellido" class="form-control" placeholder="Ej. Pérez" required>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label">Sexo</label>
                        <select name="sexo" class="form-select" required>
                            <option value="Hombre">Hombre</option>
                            <option value="Mujer">Mujer</option>
                            <option value="Otro">Otro</option>
                        </select>
                    </div>

                    <div class="col-md-8 mb-3">
                        <label class="form-label">Correo Electrónico</label>
                        <input type="email" name="correo" class="form-control border-primary" placeholder="correo@ejemplo.com" required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Teléfono del Socio</label>
                        <input type="text" name="telefono" class="form-control" placeholder="000-000-0000">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Contacto de Emergencia (Nombre/Tel)</label>
                        <input type="text" name="contacto_emergencia" class="form-control" placeholder="Nombre - Teléfono">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Fecha de Nacimiento</label>
                        <input type="date" name="fecha_nacimiento" id="f_nac" class="form-control" required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label text-blue fw-bold">Edad Calculada</label>
                        <input type="text" id="edad_display" class="form-control bg-light" placeholder="0 años" readonly>
                    </div>

                    <div class="col-md-12 mb-3">
                        <label class="form-label">Dirección Particular</label>
                        <input type="text" name="direccion" class="form-control" placeholder="Calle, Número, Colonia">
                    </div>

                    <div class="hr-text text-blue fw-bold">Configuración de Membresía</div>

                    <div class="col-md-12 mb-3">
                        <label class="form-label">¿Es dependiente de un Titular?</label>
                        <select name="id_titular" class="form-select border-azure">
                            <option value="">-- No, es Titular Independiente --</option>
                            <?php 
                            $titulares = $conexion->query("SELECT id_socio, nombre, apellido FROM socios WHERE id_titular IS NULL ORDER BY nombre ASC");
                            while($t = $titulares->fetch_assoc()){
                                echo "<option value='{$t['id_socio']}'>{$t['nombre']} {$t['apellido']}</option>";
                            }
                            ?>
                        </select>
                        <small class="text-muted">Si seleccionas un titular, heredará su fecha de vencimiento automáticamente.</small>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Plan a Contratar</label>
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
                        <label class="form-label">Asignar Entrenador</label>
                        <select name="id_entrenador" class="form-select">
                            <option value="">-- Sin entrenador asignado --</option>
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
            <div class="card-footer text-end bg-light">
                <a href="socios.php" class="btn btn-link">Regresar</a>
                <button type="submit" class="btn btn-primary px-5 shadow-sm">Finalizar Registro</button>
            </div>
        </form>
    </div>
</div>

<script>
// Script para calcular la edad en tiempo real
document.getElementById('f_nac').addEventListener('change', function() {
    const fechaNac = new Date(this.value);
    const hoy = new Date();
    if (this.value) {
        let edad = hoy.getFullYear() - fechaNac.getFullYear();
        const mes = hoy.getMonth() - fechaNac.getMonth();
        if (mes < 0 || (mes === 0 && hoy.getDate() < fechaNac.getDate())) {
            edad--;
        }
        document.getElementById('edad_display').value = (edad >= 0) ? edad + " años" : "Fecha inválida";
    }
});
</script>

<?php include 'footer.php'; ?>