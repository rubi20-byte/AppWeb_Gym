<?php 
include 'config.php';
include 'validar.php'; // este archivo valida que seas admin para entrar a esta pagina
include 'header.php'; 


if ($_POST) {
    $nom = $_POST['nombre'];
    $esp = $_POST['especialidad'];
    $tel = $_POST['telefono'];
    $cor = $_POST['correo'];
    $f_con = $_POST['fecha_contratacion'];
    $com = $_POST['tarifa_comision'];
    $tur = $_POST['turno'];

    $sql = "INSERT INTO entrenadores (nombre, especialidad, telefono, correo, fecha_contratacion, tarifa_comision, turno, estado) 
            VALUES ('$nom', '$esp', '$tel', '$cor', '$f_con', '$com', '$tur', 'activo')";
    
    if ($conexion->query($sql)) {
        echo "<script>window.location='entrenadores.php';</script>";
    } else {
        echo "<div class='alert alert-danger'>Error: " . $conexion->error . "</div>";
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
                        <label class="form-label">Correo</label>
                        <input type="email" name="correo" class="form-control">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Fecha de Contratación</label>
                        <input type="date" name="fecha_contratacion" class="form-control" value="<?php echo date('Y-m-d'); ?>">
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Turno</label>
                        <select name="turno" class="form-select">
                            <option value="Matutino">Matutino</option>
                            <option value="Vespertino">Vespertino</option>
                            <option value="Completo">Completo</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Comisión por Socio ($)</label>
                        <input type="number" step="0.01" name="tarifa_comision" class="form-control" placeholder="0.00">
                    </div>
                </div>
            </div>
           <div class="card-footer text-end bg-light">
            <a href="entrenadores.php" class="btn btn-link text-purple">
                Cancelar
            </a>
            <button type="submit" class="btn btn-purple text-white shadow-sm">
                <i class="ti ti-user-plus me-2"></i>Dar de Alta Entrenador
            </button>
        </div>
    </div>
</div>