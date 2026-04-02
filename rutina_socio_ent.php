<?php
include 'config.php';
include 'validar_entrenador.php';
include 'header_entrenador.php';

// Limpiamos el ID para evitar inyecciones básicos
$id_socio = isset($_GET['id']) ? mysqli_real_escape_string($conexion, $_GET['id']) : '';

// 1. Procesar la actualización PRIMERO (Para que el cambio se vea reflejado abajo)
if (isset($_POST['asignar_rutina'])) {
    $id_rutina_nueva = $_POST['id_rutina'];
    
    $sql_update = "UPDATE socios SET id_rutina = '$id_rutina_nueva' WHERE id_socio = '$id_socio'";
    
    if ($conexion->query($sql_update)) {
        // En lugar de redireccionar de golpe, dejamos que el script siga 
        // o recargamos para que el socio['id_rutina'] se actualice.
        echo "<script>window.location='rutina_socio_ent.php?id=$id_socio&msj=ok';</script>";
        exit();
    } else {
        echo "<div class='alert alert-danger'>Error: " . $conexion->error . "</div>";
    }
}

// 2. Consultar datos del socio (Nombre e ID de rutina actual)
$res_socio = $conexion->query("SELECT nombre, apellido, id_rutina FROM socios WHERE id_socio = '$id_socio'");
$socio = $res_socio->fetch_assoc();

// 3. Consultar todas las rutinas disponibles
$rutinas = $conexion->query("SELECT * FROM rutinas");
?>

<div class="page-wrapper">
    <div class="container-xl mt-4">
        <div class="page-header mb-4">
            <div class="row align-items-center">
                <div class="col">
                    <h2 class="page-title text-pink fw-bold">Asignar Rutina</h2>
                    <div class="text-muted">Socio: <strong><?php echo $socio['nombre'] . " " . $socio['apellido']; ?></strong></div>
                </div>
                <div class="col-auto">
                    <a href="mis_socios.php" class="btn btn-secondary">Volver</a>
                </div>
            </div>
        </div>

        <div class="row row-cards">
            <?php while ($rut = $rutinas->fetch_assoc()): 
                // Aquí se hace la magia: Comparamos el ID de la tarjeta con el del socio
                $es_actual = ($rut['id_rutina'] == $socio['id_rutina']);
            ?>
            <div class="col-md-4 col-lg-3">
                <div class="card shadow-sm <?php echo $es_actual ? 'border-pink border-3' : 'border-0'; ?>" style="border-radius: 12px; transition: 0.3s;">
                    <div class="card-body p-3 text-center">
                        <div class="avatar avatar-lg bg-pink-lt mb-3 rounded">
                            <i class="ti ti-barbell" style="font-size: 1.5rem;"></i>
                        </div>
                        <h4 class="fw-bold mb-1"><?php echo $rut['nombre_rutina']; ?></h4>
                        <p class="text-muted small mb-3">
                            <?php echo !empty($rut['descripcion']) ? $rut['descripcion'] : 'Plan de entrenamiento personalizado.'; ?>
                        </p>
                        
                        <?php if ($es_actual): ?>
                            <button class="btn btn-pink w-100 disabled rounded-pill">
                                <i class="ti ti-check me-1"></i> Asignada
                            </button>
                        <?php else: ?>
                            <form method="POST">
                                <input type="hidden" name="id_rutina" value="<?php echo $rut['id_rutina']; ?>">
                                <button type="submit" name="asignar_rutina" class="btn btn-outline-pink w-100 rounded-pill">
                                    Seleccionar
                                </button>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
    </div>
</div>

<style>
    /* Estilos personalizados para que se vea súper bien */
    .border-pink { border-color: #f66d9b !important; }
    .border-3 { border-width: 3px !important; }
    .bg-pink-lt { background-color: #fdf2f5; color: #f66d9b; }
    .btn-pink { background-color: #f66d9b; color: white; border: none; }
    .btn-pink:hover { background-color: #e05e8a; color: white; }
    .btn-outline-pink { color: #f66d9b; border-color: #f66d9b; }
    .btn-outline-pink:hover { background-color: #f66d9b; color: white; }
</style>

<?php include 'footer.php'; ?>