<?php
include 'validar_socio.php';
include 'config.php';
include 'header_socio.php';

// ID del socio logueado
$id_usuario = $_SESSION['id_socio'] ?? $_SESSION['id_usuario'];

// 1. Buscamos qué rutina tiene asignada el socio en la tabla socios
$query_socio = $conexion->query("SELECT s.id_rutina, r.nombre_rutina 
                                FROM socios s 
                                LEFT JOIN rutinas r ON s.id_rutina = r.id_rutina 
                                WHERE s.id_socio = '$id_usuario'");
$datos_socio = $query_socio->fetch_assoc();
$id_r = $datos_socio['id_rutina'] ?? null;
?>

<div class="page-wrapper">
    <div class="container-xl mt-4">
        <div class="page-header mb-4">
            <h2 class="page-title text-azure">
                <i class="ti ti-stretching me-2"></i> Mi Rutina Personalizada
            </h2>
        </div>

        <div class="row row-cards">
            <?php if ($id_r): ?>
                <div class="col-12">
                    <div class="card shadow-sm border-0">
                        <div class="card-status-start bg-azure"></div>
                        <div class="card-header bg-white">
                            <h3 class="card-title fw-bold text-uppercase">
                                <?php echo $datos_socio['nombre_rutina']; ?>
                            </h3>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-vcenter card-table table-striped">
                                <thead>
                                    <tr>
                                        <th class="text-azure">EJERCICIO</th>
                                        <th>SERIES</th>
                                        <th>REPS</th>
                                        <th>DESCANSO</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    // 2. Usamos el nombre exacto de tu tabla: rutina_ejercicio
                                    $sql_ej = "SELECT * FROM rutina_ejercicio WHERE id_rutina = '$id_r' ORDER BY orden ASC";
                                    $res_ej = $conexion->query($sql_ej);
                                    
                                    if($res_ej && $res_ej->num_rows > 0):
                                        while($ej = $res_ej->fetch_assoc()):
                                    ?>
                                    <tr>
                                        <td class="fw-bold"><?php echo $ej['nombre_ejercicio']; ?></td>
                                        <td>
                                            <span class="badge bg-azure-lt text-azure">
                                                <?php echo $ej['series']; ?>
                                            </span>
                                        </td>
                                        <td><?php echo $ej['repeticiones']; ?></td>
                                        <td class="text-muted">
                                            <i class="ti ti-clock-pause me-1"></i>
                                            <?php echo !empty($ej['descanso']) ? $ej['descanso'] : '1 min'; ?>
                                        </td>
                                    </tr>
                                    <?php 
                                        endwhile;
                                    else:
                                    ?>
                                    <tr>
                                        <td colspan="5" class="text-center py-4 text-muted">
                                            No se encontraron ejercicios registrados para esta rutina.
                                        </td>
                                    </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <div class="col-12 text-center py-5">
                    <div class="empty border shadow-sm" style="background: #fff; border-radius: 12px;">
                        <div class="empty-icon text-muted"><i class="ti ti-clipboard-off display-3"></i></div>
                        <p class="empty-title">¡Aún no tienes una rutina!</p>
                        <p class="empty-subtitle text-muted">Consulta a tu entrenador para que te asigne un plan.</p>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>