<?php 
include 'config.php';
include 'validar_admin.php'; 
include 'header.php'; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 1. Recibimos y limpiamos los datos
    $nombre = mysqli_real_escape_string($conexion, $_POST['nombre']);
    $especialidad = mysqli_real_escape_string($conexion, $_POST['especialidad']);
    $telefono = mysqli_real_escape_string($conexion, $_POST['telefono']);
    $correo = mysqli_real_escape_string($conexion, $_POST['correo']);
    $turno = $_POST['turno'];
    $comision = $_POST['tarifa_comision']; // Corregido para que coincida con el name del HTML
    $fecha = $_POST['fecha_contratacion']; 
    
    // 2. Encriptamos la contraseña
    $pass_plana = $_POST['password'];

    // 3. SQL con todas las columnas
    $sql = "INSERT INTO entrenadores (nombre, especialidad, telefono, correo, password, fecha_contratacion, tarifa_comision, turno, estado) 
            VALUES ('$nombre', '$especialidad', '$telefono', '$correo', '$pass_plana', '$fecha', '$comision', '$turno', 'activo')";

    if ($conexion->query($sql)) {
        echo "<script>window.location='entrenadores.php?msj=ok';</script>";
        exit();
    } else {
        echo "<div class='alert alert-danger'>Error al guardar: " . $conexion->error . "</div>";
    }
}
?>

<div class="page-wrapper">
    <div class="container-xl mt-4">
        <form method="POST" class="card col-md-10 mx-auto shadow">
            <div class="card-header bg-purple-lt">
                <h3 class="card-title">Registro de Entrenadores</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Nombre Completo</label>
                        <input type="text" name="nombre" class="form-control" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Especialidad</label>
                        <input type="text" name="especialidad" class="form-control" placeholder="Ej. Pesas / Funcional">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Teléfono</label>
                        <input type="text" name="telefono" class="form-control">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Correo (Usuario)</label>
                        <input type="email" name="correo" class="form-control" required>
                    </div>
                    
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Contraseña de Acceso</label>
                        <input type="password" name="password" class="form-control" placeholder="Crea una clave" required>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label">Fecha de Contratación</label>
                        <input type="date" name="fecha_contratacion" class="form-control" value="<?php echo date('Y-m-d'); ?>">
                    </div>
                    
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Turno</label>
                        <select name="turno" class="form-select">
                            <option value="Matutino">Matutino</option>
                            <option value="Vespertino">Vespertino</option>
                            <option value="Completo" selected>Completo</option>
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Comisión por Socio ($)</label>
                        <input type="number" step="0.01" name="tarifa_comision" class="form-control" placeholder="0.00">
                    </div>
                </div>
            </div>
            <div class="card-footer text-end bg-light">
                <a href="entrenadores.php" class="btn btn-link text-purple">Cancelar</a>
                <button type="submit" class="btn btn-purple text-white shadow-sm">
                    <i class="ti ti-user-plus me-2"></i>Dar de Alta Entrenador
                </button>
            </div>
        </form>
    </div>
</div>