<?php
include 'validar_admin.php';
include 'config.php';
include 'header.php';

// 1. Lógica para guardar la clase
if (isset($_POST['guardar_clase'])) {
    $nombre = mysqli_real_escape_string($conexion, $_POST['nombre']);
    $desc = mysqli_real_escape_string($conexion, $_POST['descripcion'] ?? '');
    $id_entrenador = $_POST['id_entrenador'];
    $fecha_clase = $_POST['fecha_clase']; // Recogemos la fecha del formulario
    $h_inicio = $_POST['hora_inicio'];
    $h_fin = $_POST['hora_fin'];
    $cap = $_POST['capacidad'];
    
    // Corregido: Se incluye fecha_clase en el INSERT
    $sql = "INSERT INTO clases (nombre_clase, descripcion, id_entrenador, fecha_clase, hora_inicio, hora_fin, capacidad, estado) 
            VALUES ('$nombre', '$desc', '$id_entrenador', '$fecha_clase', '$h_inicio', '$h_fin', '$cap', 'activo')";
    
    if($conexion->query($sql)) {
        echo "<script>alert('¡Clase publicada con éxito!'); window.location.href='admin_clases.php';</script>";
    } else {
        echo "<div class='alert alert-danger'>Error de Base de Datos: " . $conexion->error . "</div>";
    }
}
?>

<div class="page-wrapper">
    <div class="container-xl mt-4">
        <div class="card mb-4 border-0 shadow-sm">
            <div class="card-header bg-white">
                <h3 class="card-title text-azure">Configurar Nueva Clase Grupal</h3>
            </div>
            <div class="card-body">
                <form method="POST">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <label class="form-label font-weight-bold">Nombre de la Clase</label>
                            <input type="text" name="nombre" class="form-control" placeholder="Ej. Zumba" required>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label font-weight-bold">Entrenador</label>
                            <select name="id_entrenador" class="form-select" required>
                                <option value="">Seleccionar Entrenador...</option>
                                <?php
                                $query_ent = $conexion->query("SELECT id_entrenador, nombre FROM entrenadores WHERE estado = 'Activo'");
                                while($ent = $query_ent->fetch_assoc()):
                                    echo "<option value='{$ent['id_entrenador']}'>{$ent['nombre']}</option>";
                                endwhile;
                                ?>
                            </select>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label font-weight-bold">Día de la Clase</label>
                            <input type="date" name="fecha_clase" class="form-control" min="<?php echo date('Y-m-d'); ?>" required>
                        </div>
                        <div class="col-md-1 mb-3">
                            <label class="form-label font-weight-bold">Desde</label>
                            <input type="time" name="hora_inicio" class="form-control" required>
                        </div>
                        <div class="col-md-1 mb-3">
                            <label class="form-label font-weight-bold">Hasta</label>
                            <input type="time" name="hora_fin" class="form-control" required>
                        </div>
                        <div class="col-md-1 mb-3">
                            <label class="form-label font-weight-bold">Cupo</label>
                            <input type="number" name="capacidad" class="form-control" value="20" required>
                        </div>
                    </div>
                    <div class="text-end">
                        <button type="submit" name="guardar_clase" class="btn btn-azure px-4">
                            <i class="ti ti-plus"></i> Publicar Clase
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="table-responsive">
                <table class="table table-vcenter card-table">
                    <thead>
                        <tr>
                            <th>CLASE</th>
                            <th>ENTRENADOR</th>
                            <th>DÍA / FECHA</th>
                            <th>HORARIO</th>
                            <th>CUPO</th>
                            <th>ESTADO</th>
                            <th class="w-1">ACCIONES</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $fecha_hoy = date('Y-m-d');
                        $hora_actual = date('H:i:s');

                        $res = $conexion->query("SELECT c.*, e.nombre as entrenador,
                                                (SELECT COUNT(*) FROM reservas_clases r WHERE r.id_clase = c.id_clase AND r.fecha_clase = c.fecha_clase) as inscritos
                                                FROM clases c
                                                LEFT JOIN entrenadores e ON c.id_entrenador = e.id_entrenador
                                                ORDER BY c.fecha_clase DESC, c.hora_inicio ASC");

                        $dias_esp = ["Sunday" => "Domingo", "Monday" => "Lunes", "Tuesday" => "Martes", "Wednesday" => "Miércoles", "Thursday" => "Jueves", "Friday" => "Viernes", "Saturday" => "Sábado"];

                        while($c = $res->fetch_assoc()):
                            $total_inscritos = $c['inscritos'];
                            
                            if (!empty($c['fecha_clase']) && $c['fecha_clase'] != '0000-00-00') {
                                $time = strtotime($c['fecha_clase']);
                                $dia_nombre = $dias_esp[date('l', $time)];
                                $fecha_formateada = date('d/m/Y', $time);
                                
                                $clase_terminada = ($c['fecha_clase'] == $fecha_hoy && $c['hora_fin'] < $hora_actual) || ($c['fecha_clase'] < $fecha_hoy);
                            } else {
                                $dia_nombre = "No definida";
                                $fecha_formateada = "--/--/----";
                                $clase_terminada = false;
                            }

                            if ($clase_terminada) {
                                $status_label = "Terminada";
                                $status_color = "bg-secondary";
                            } elseif ($total_inscritos >= $c['capacidad']) {
                                $status_label = "Lleno";
                                $status_color = "bg-danger";
                            } else {
                                $status_label = "Abierta";
                                $status_color = "bg-success";
                            }
                        ?>
                        <tr>
                            <td class="fw-bold"><?php echo $c['nombre_clase']; ?></td>
                            <td class="text-muted"><?php echo $c['entrenador'] ?? 'Sin asignar'; ?></td>
                            <td>
                                <div class="text-capitalize small fw-bold"><?php echo $dia_nombre; ?></div>
                                <div class="text-muted mt-n1" style="font-size: 0.75rem;"><?php echo $fecha_formateada; ?></div>
                            </td>
                            <td>
                                <span class="badge bg-blue-lt">
                                    <?php echo date('h:i A', strtotime($c['hora_inicio'])); ?> - <?php echo date('h:i A', strtotime($c['hora_fin'])); ?>
                                </span>
                            </td>
                            <td class="text-muted"><?php echo $total_inscritos; ?> / <?php echo $c['capacidad']; ?></td>
                            <td>
                                <span class="badge <?php echo $status_color; ?> text-white" style="font-size: 0.65rem;">
                                    <?php echo strtoupper($status_label); ?>
                                </span>
                            </td>
                            <td>
                                <div class="btn-list flex-nowrap">
                                    <a href="editar_clase.php?id=<?php echo $c['id_clase']; ?>" class="btn btn-white btn-icon btn-sm"><i class="ti ti-edit"></i></a>
                                    <a href="eliminar_clase.php?id=<?php echo $c['id_clase']; ?>" class="btn btn-danger btn-icon btn-sm" onclick="return confirm('¿Seguro?')"><i class="ti ti-trash"></i></a>
                                </div>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>