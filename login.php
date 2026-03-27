<?php
// 1. Configuración de errores y sesión
ini_set('display_errors', 1);
error_reporting(E_ALL);
include 'config.php';
session_start();

// 2. Si ya hay una sesión activa, redirigir
if (isset($_SESSION['rol'])) {
    if ($_SESSION['rol'] == 'admin') {
        header("Location: index.php");
    } else {
        header("Location: dashboard_socio.php");
    }
    exit();
}

$error_msg = "";

// 3. Procesar el formulario
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user = mysqli_real_escape_string($conexion, $_POST['usuario']);
    $pass = mysqli_real_escape_string($conexion, $_POST['password']);

    // --- INTENTO 1: ADMINISTRADORES ---
    $query_admin = "SELECT * FROM usuarios WHERE usuario = '$user' AND password = '$pass'";
    $res_admin = $conexion->query($query_admin);

    if ($res_admin && $res_admin->num_rows > 0) {
        $datos = $res_admin->fetch_assoc();
        $_SESSION['id_usuario'] = $datos['id_usuario'];
        $_SESSION['nombre'] = $datos['nombre'];
        $_SESSION['rol'] = 'admin';
        
        session_write_close();
        header("Location: index.php");
        exit();
    }

    // --- INTENTO 2: SOCIOS (Usando 'correo' y 'qr_codigo') ---
    // Según tu SQL: correo y qr_codigo son los nombres reales
    $query_socio = "SELECT id_socio, nombre, apellido, estado FROM socios WHERE correo = '$user' AND qr_codigo = '$pass'";
    $res_socio = $conexion->query($query_socio);

    if ($res_socio && $res_socio->num_rows > 0) {
        $datos_s = $res_socio->fetch_assoc();
        
        $_SESSION['id_socio'] = $datos_s['id_socio'];
        $_SESSION['nombre'] = $datos_s['nombre'] . " " . $datos_s['apellido'];
        $_SESSION['rol'] = 'socio';
        $_SESSION['estado'] = $datos_s['estado'];

        session_write_close();
        header("Location: dashboard_socio.php");
        exit();
    } else {
        $error_msg = "Credenciales incorrectas. Verifica tu correo o código QR.";
    }
}
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover"/>
    <title>Acceso | Gym Rubí</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/core@1.0.0-beta17/dist/css/tabler.min.css">
    <style>
        .bg-gym-image { background-image: url('assets/img/gym.jpg'); background-size: cover; background-position: center; }
        .bg-overlay { background: rgba(0, 0, 0, 0.7); }
        .bg-dark-panel { background-color: #1a2234 !important; color: #ffffff !important; }
        .bg-dark-panel .form-control { background-color: rgba(255, 255, 255, 0.05); border-color: rgba(255, 255, 255, 0.1); color: #fff; }
    </style>
</head>
<body class="d-flex flex-column">
    <div class="row g-0 flex-fill">
        <div class="col-12 col-lg-6 col-xl-8 d-none d-lg-block bg-gym-image">
            <div class="bg-overlay h-100"></div>
        </div>
        <div class="col-12 col-lg-6 col-xl-4 d-flex flex-column justify-content-center bg-dark-panel border-top-wide border-primary">
            <div class="container container-tight my-5 px-lg-5">
                <div class="text-center mb-4">
                    <h1 class="text-uppercase fw-bold" style="color: #ff4d4d;">GYM RUBÍ</h1>
                </div>
                
                <?php if($error_msg != ""): ?>
                <div class="alert alert-danger bg-danger-lt border-0 mb-4"><?php echo $error_msg; ?></div>
                <?php endif; ?>

                <form action="login.php" method="post" autocomplete="off">
                    <div class="mb-3">
                        <label class="form-label fs-7 fw-bold">Correo Electrónico/Usuario</label>
                        <input type="text" name="usuario" class="form-control form-control-lg" placeholder="ejemplo@correo.com" required autofocus>
                    </div>
                    <div class="mb-4">
                        <label class="form-label fs-7 fw-bold">Contraseña</label>
                        <input type="password" name="password" class="form-control form-control-lg" placeholder="Tu contraseña" required>
                    </div>
                    <div class="form-footer">
                        <button type="submit" class="btn btn-danger w-100 btn-lg shadow">INICIAR SESIÓN</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>