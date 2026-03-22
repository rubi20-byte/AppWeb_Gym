<?php 

include 'config.php';
include 'header.php'; 

// Recibimos el id del socio de POST y sanitizamos
$id = mysqli_real_escape_string($conexion, $_POST['id_socio']);

// Buscamos socio por id en tabla socios
$sql = "SELECT * FROM socios WHERE id_socio = '$id'";
$res = $conexion->query($sql);
?>

<div class="page-wrapper">
    <div class="container-xl mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8 text-center">
                <?php if ($res && $res->num_rows > 0): 
                    $s = $res->fetch_assoc();
                    // checamos si su estado es activo
                    if ($s['estado'] == 'Activo'): ?>
                        <div class="display-1 text-success mb-3"><i class="ti ti-circle-check"></i></div>
                        <h1 class="display-4 text-success">ACCESO CONCEDIDO</h1>
                        <h2 class="mt-3">Bienvenido, <?php echo $s['nombre']; ?></h2>
                        <p class="text-muted">Membresía vigente hasta: <?php echo date('d/m/Y', strtotime($s['vencimiento'])); ?></p>
                    <?php else: ?>
                        <div class="display-1 text-danger mb-3"><i class="ti ti-circle-x"></i></div>
                        <h1 class="display-4 text-danger">ACCESO DENEGADO</h1>
                        <h2 class="mt-3"><?php echo $s['nombre']; ?></h2>
                        <div class="alert alert-danger mt-3">TU MENSUALIDAD ESTÁ VENCIDA</div>
                    <?php endif; ?>
                <?php else: ?>
                    <div class="display-1 text-warning mb-3"><i class="ti ti-alert-triangle"></i></div>
                    <h1 class="display-4 text-warning">NO REGISTRADO</h1>
                    <p class="h2">Ese código de socio no existe en el sistema.</p>
                <?php endif; ?>
                
                <div class="mt-5">
                    <a href="acceso_qr.php" class="btn btn-outline-secondary">Volver a Escanear</a>
                </div>
            </div>
        </div>
    </div>
</div>
// en acceso_qr.php se puso un autofocus para que el cursor ya este listo para escribir
// usamos una consulta simple para ver si el id existe y si esta al corriente con su pago
// si el estado no es activo, el sistema le bota el acceso en rojo para que el staff lo vea
<?php include 'footer.php'; ?>