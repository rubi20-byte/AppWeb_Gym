<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover"/>
    <title>Gym System | Dashboard</title>
    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/core@1.0.0-beta17/dist/css/tabler.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">
    
    <style>
        @import url('https://rsms.me/inter/inter.css');
        :root { --tblr-font-sans-serif: 'Inter Var', sans-serif; }
    </style>
</head>
<body>
<div class="page">
    <header class="navbar navbar-expand-md navbar-light d-print-none">
        <div class="container-xl">
            <h1 class="navbar-brand navbar-brand-autodark d-none-initial-sm pe-0 pe-md-3">
                <a href="dashboard_socio.php">
                    <img src="assets/img/logo2.png" alt="Gym Rubi" height="36" class="navbar-brand-image me-2">
                    <span class="d-none d-sm-inline">GYM RUBÍ</span>
                </a>
            </h1>

            <div class="navbar-nav flex-row order-md-last">
                <div class="nav-item d-flex me-3">
                    <div class="btn-list">
                        <span class="badge bg-blue-lt d-none d-md-inline-block">Usuario: Socio</span>
                        <a href="logout.php" class="btn btn-outline-danger btn-sm">
                            <i class="ti ti-logout me-1"></i> Salir
                        </a>
                    </div>
                </div>
            </div>

            <div class="collapse navbar-collapse" id="navbar-menu">
                <div class="d-flex flex-column flex-md-row flex-fill align-items-stretch align-items-md-center">
                    <ul class="navbar-nav">
                        <li class="nav-item">
                            <a class="nav-link" href="dashboard_socio.php">
                                <span class="nav-link-icon d-md-none d-lg-inline-block"><i class="ti ti-home"></i></span>
                                <span class="nav-link-title">Inicio</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="socio_nutricion.php">
                                <span class="nav-link-icon d-md-none d-lg-inline-block"><i class="ti ti-clipboard-text"></i></span>
                                <span class="nav-link-title">Mis Planes</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="mis_rutinas.php">
                                <span class="nav-link-icon d-md-none d-lg-inline-block"><i class="ti ti-stretching"></i></span>
                                <span class="nav-link-title">Mi Rutina</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="clases_agenda.php">
                                <span class="nav-link-icon d-md-none d-lg-inline-block"><i class="ti ti-yoga"></i></span>
                                <span class="nav-link-title">Catálogo de Clases</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="mis_clases.php">
                                <span class="nav-link-icon d-md-none d-lg-inline-block"><i class="ti ti-calendar-event"></i></span>
                                <span class="nav-link-title">Mis Clases</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="tienda_socio.php">
                                <span class="nav-link-icon d-md-none d-lg-inline-block"><i class="ti ti-building-store"></i></span>
                                <span class="nav-link-title">Tienda</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </header>