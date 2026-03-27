<?php
include 'validar_admin.php';
include 'config.php';
include 'header.php';
?>

<div class="page-wrapper">
    <div class="container-xl mt-4">
        <div class="page-header mb-4">
            <h2 class="page-title text-danger">Gestión de Entrenamiento y Nutrición</h2>
        </div>

        <div class="row row-cards">
            <div class="col-md-6">
                <a href="admin_rutinas.php" class="card card-link card-link-pop shadow-sm">
                    <div class="card-body text-center py-4">
                        <span class="avatar avatar-xl rounded mb-3 bg-pink-lt">
                            <i class="ti ti-treadmill" style="font-size: 2.5rem;"></i>
                        </span>
                        <h3 class="h2">Rutinas de Ejercicio</h3>
                        <p class="text-muted">Crear rutinas, asignar ejercicios y niveles.</p>
                    </div>
                </a>
            </div>

            <div class="col-md-6">
                <a href="admin_nutricion.php" class="card card-link card-link-pop shadow-sm">
                    <div class="card-body text-center py-4">
                        <span class="avatar avatar-xl rounded mb-3 bg-lime-lt">
                            <i class="ti ti-salad" style="font-size: 2.5rem;"></i>
                        </span>
                        <h3 class="h2">Planes Alimenticios</h3>
                        <p class="text-muted">Configuracion de dietas según objetivo.</p>
                    </div>
                </a>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>