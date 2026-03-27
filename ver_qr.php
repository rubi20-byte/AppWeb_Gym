<?php
include 'config.php';
include 'header.php'; // Para que mantenga el estilo de tu sistema

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    
    // Buscamos los datos del socio
    $query = "SELECT nombre, apellido, correo, qr_codigo FROM socios WHERE id_socio = '$id'";
    $res = $conexion->query($query);
    $socio = $res->fetch_assoc();

    if ($socio) {
        $nombre_completo = $socio['nombre'] . " " . $socio['apellido'];
        $datos_qr = $socio['qr_codigo']; // Este es el texto que se convierte en QR
        
        // Generamos la URL de la API de Google Charts para el QR
        // Escala: 300x300 px
        $url_qr = "https://chart.googleapis.com/chart?chs=300x300&cht=qr&chl=" . urlencode($datos_qr) . "&choe=UTF-8";
    } else {
        die("Socio no encontrado.");
    }
} else {
    die("ID no proporcionado.");
}
?>

<div class="page-wrapper">
    <div class="container-xl mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-4">
                <div class="card shadow-lg text-center">
                    <div class="card-status-top bg-danger"></div> <div class="card-body">
                        <h2 class="mb-3">Credencial Digital</h2>
                        <div class="mb-3">
                            <span class="avatar avatar-xl rounded-circle bg-red-lt"><?php echo substr($socio['nombre'], 0, 1); ?></span>
                        </div>
                        <h3 class="m-0"><?php echo $nombre_completo; ?></h3>
                        <p class="text-muted"><?php echo $socio['correo']; ?></p>
                        
                        <div class="my-4">
                            <img src="<?php echo $url_qr; ?>" alt="Código QR de Acceso" class="img-fluid border p-2 bg-white">
                        </div>

                        <div class="mt-3">
                            <label class="form-label text-muted">Código de Acceso:</label>
                            <h1 class="display-5 fw-bold text-dark"><?php echo $datos_qr; ?></h1>
                        </div>
                    </div>
                    <div class="card-footer bg-light">
                        <div class="row align-items-center">
                            <div class="col">
                                <a href="acceso_qr.php" class="btn btn-link link-secondary">Volver al listado</a>
                            </div>
                            <div class="col-auto">
                                <button onclick="window.print();" class="btn btn-primary">
                                    <i class="ti ti-printer me-2"></i> Imprimir
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>