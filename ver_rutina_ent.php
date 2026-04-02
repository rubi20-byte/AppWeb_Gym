<?php
include 'config.php';
include 'validar_entrenador.php';
include 'header_entrenador.php';

$id_rutina = $_GET['id'];

// Consultamos nombre de la rutina
$rutina = $conexion->query("SELECT nombre_rutina FROM rutinas WHERE id_rutina = '$id_rutina'")->fetch_assoc();

// Consultamos los ejercicios asignados
// REVISA: Si tu tabla de unión se llama diferente a 'rutina_ejercicios', cámbiala aquí
$sql = "SELECT e.nombre_ejercicio, re.series, re.repeticiones, re.id_rutina_ejercicio 
        FROM rutina_ejercicios re
        JOIN ejercicios e ON re.id_ejercicio = e.id_ejercicio
        WHERE re.id_rutina = '$id_rutina'";
$res_ejercicios = $conexion->query($sql);
?>

<div class="page-wrapper">
    <div class="container-xl mt-4">
        <div class="page-header mb-4">
            <div class="row align-items-center">
                <div class="col">
                    <h2 class="page-title text-purple fw-bold">Ejercicios: <?php echo $rutina['nombre_rutina']; ?></h2>
                </div>
                <div class="col-auto">
                    <a href="rutinas_entrenador.php" class="btn btn-outline-secondary">Volver a la lista</a>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm" style="border-radius: 15px;">
            <div class="table-responsive">
                <table class="table table-vcenter card-table">
                    <thead>
                        <tr class="bg-light">
                            <th>Ejercicio</th>
                            <th class="text-center">Series</th>
                            <th class="text-center">Repeticiones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if($res_ejercicios->num_rows > 0): ?>
                            <?php while($ej = $res_ejercicios->fetch_assoc()): ?>
                            <tr>
                                <td class="fw-bold text-purple"><?php echo $ej['nombre_ejercicio']; ?></td>
                                <td class="text-center"><?php echo $ej['series']; ?></td>
                                <td class="text-center"><?php echo $ej['repeticiones']; ?></td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="3" class="text-center text-muted py-4">No hay ejercicios en esta rutina.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>