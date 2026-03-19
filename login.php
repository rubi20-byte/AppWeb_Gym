<?php
session_start();
if (isset($_SESSION['id_usuario'])) {
    header("Location: index.php");
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
        /* 1. Imagen de fondo del lado izquierdo */
        .bg-gym-image {
            background-image: url('assets/img/gym.jpg'); 
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
        }
        /* Capa oscura sobre la imagen */
        .bg-overlay {
            background: rgba(0, 0, 0, 0.6); /* 60% de opacidad negra */
        }

        /* 2. CSS personalizado para el panel oscuro del formulario */
        .bg-dark-panel {
            background-color: #1a2234 !important; /* Un tono azul oscuro/negro muy elegante de Tabler */
            color: #ffffff !important; /* Texto blanco para que resalte */
        }
        /* Ajuste para los labels y textos mudos dentro del panel oscuro */
        .bg-dark-panel .form-label,
        .bg-dark-panel .text-muted {
            color: rgba(255, 255, 255, 0.7) !important; /* Blanco semitransparente */
        }
        /* Ajuste para los inputs para que no se vean raros en fondo oscuro */
        .bg-dark-panel .form-control {
            background-color: rgba(255, 255, 255, 0.05); /* Fondo muy sutil para el input */
            border-color: rgba(255, 255, 255, 0.1);
            color: #ffffff;
        }
        .bg-dark-panel .form-control:focus {
            background-color: rgba(255, 255, 255, 0.1);
            border-color: #206bc4; /* Color primario de Tabler al enfocar */
        }
    </style>
</head>
<body class="d-flex flex-column g-bg-none">
    <div class="row g-0 flex-fill">
        <div class="col-12 col-lg-6 col-xl-8 d-none d-lg-block bg-gym-image">
            <div class="bg-overlay h-100"></div>
        </div>
        
        <div class="col-12 col-lg-6 col-xl-4 border-top-wide border-primary d-flex flex-column justify-content-center bg-dark-panel">
            <div class="container container-tight my-5 px-lg-5">
                <h1 class="h2 text-center mb-3 text-red">GYM RUBÍ</h2>
                
                <h2 class="h2 text-center mb-3 text-white">Acceso</h2>
                <p class="text-muted text-center mb-5">Ingresa tus credenciales para continuar</p>
                
                <form action="autenticacion.php" method="post" autocomplete="off">
                    <div class="mb-4">
                        <label class="form-label font-weight-bold text-uppercase fs-7">Usuario</label>
                        <div class="input-group input-group-flat">
                            <input type="text" name="usuario" class="form-control" placeholder="Escribe tu usuario" required autofocus>
                        </div>
                    </div>
                    <div class="mb-5">
                        <label class="form-label font-weight-bold text-uppercase fs-7">Contraseña</label>
                        <input type="password" name="password" class="form-control" placeholder="••••••••" required>
                    </div>
                    <div class="form-footer">
                        <button type="submit" class="btn btn-red w-100 shadow">
                            <i class="ti ti-login me-2"></i> Iniciar Sesión
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>