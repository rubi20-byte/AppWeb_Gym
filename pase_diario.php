<?php 
include 'config.php';
include 'validar.php'; // este archivo valida que seas admin para entrar a esta pagina
include 'header.php'; 


if ($_POST) {
    $nombre_visita = mysqli_real_escape_string($conexion, $_POST['nombre_cliente']);
    $monto = $_POST['monto'];
    $fecha = date('Y-m-d');
    $referencia = "Pase Diario: " . $nombre_visita;

    // Agregamos 'referencia' a las columnas y al VALUES
    $sql = "INSERT INTO pagos (monto, fecha_pago, referencia, concepto) 
            VALUES ('$monto', '$fecha', '$referencia', 'Pase Diario')";

    if ($conexion->query($sql)) {
        echo "<script>alert('Pase registrado con éxito'); window.location='index.php';</script>";
    } else {
        // Esto te dirá si falta la columna 'concepto'
        echo "Error: " . $conexion->error; 
    }
}
?>

<div class="page-wrapper">
    <div class="container-xl mt-4">
        <div class="card col-md-6 mx-auto shadow">
            <div class="card-header bg-green-lt">
                <h3 class="card-title"><i class="ti ti-ticket me-2"></i> Registrar Pase Diario</h3>
            </div>
            <div class="card-body">
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">Nombre del Cliente</label>
                        <input type="text" name="nombre_cliente" class="form-control" placeholder="Ej. Juan Pérez (Visita)" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Costo del Pase ($)</label>
                        <?php 
                        $visita = $conexion->query("SELECT precio FROM membresias WHERE nombre LIKE '%Visita%' OR nombre LIKE '%Diario%' LIMIT 1")->fetch_assoc();
                        $precio_sugerido = ($visita) ? $visita['precio'] : "50.00";
                        ?>
                        <input type="number" name="monto" class="form-control" value="<?php echo $precio_sugerido; ?>" step="0.01">
                    </div>
                    <button type="submit" class="btn btn-success w-100">Cobrar y Dar Acceso</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>