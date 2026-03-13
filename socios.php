<?php 
include 'config.php';
include 'header.php'; 
?>
<div class="page-wrapper">
    <div class="container-xl mt-4">
        <div class="row align-items-center mb-4">
            <div class="col"><h2 class="page-title text-blue">Gestión de Socios</h2></div>
            <div class="col-auto"><a href="nuevo_socio.php" class="btn btn-primary"><i class="ti ti-plus me-2"></i> Nuevo Socio</a></div>
        </div>

        <div class="card shadow-sm">
            <div class="table-responsive">
                <table class="table card-table table-vcenter">
                    <thead>
                        <tr>
                            <th class="w-1">Foto</th>
                            <th>Socio / Contacto</th> 
                            <th>Plan</th>
                            <th>Estatus</th> 
                            <th>Vencimiento</th>
                            <th class="w-1">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $hoy = strtotime(date('Y-m-d'));
                        
                        // Traemos el nombre del titular si existe (Auto-Join)
                        $sql = "SELECT s.*, m.nombre as plan, t.nombre as nombre_titular, t.apellido as apellido_titular 
                                FROM socios s 
                                JOIN membresias m ON s.id_membresia = m.id_membresia 
                                LEFT JOIN socios t ON s.id_titular = t.id_socio 
                                ORDER BY s.id_socio DESC";
                        
                        $res = $conexion->query($sql);
                        
                        while($row = $res->fetch_assoc()):
                            $vence = strtotime($row['fecha_vencimiento']);
                            $bloqueado = ($vence < $hoy || $row['estado'] != 'activo');
                            $status_color = $bloqueado ? 'red' : 'green';
                            $status_texto = $bloqueado ? 'BLOQUEADO' : 'ACTIVO';
                            
                            $foto_final = (!empty($row['foto']) && file_exists("uploads/fotos/".$row['foto'])) 
                                          ? "uploads/fotos/".$row['foto'] 
                                          : "https://cdn-icons-png.flaticon.com/512/3135/3135715.png";
                        ?>
                        <tr>
                            <td><span class="avatar avatar-md rounded-circle shadow-sm" style="background-image: url(<?php echo $foto_final; ?>)"></span></td>
                            <td>
                                <div class="font-weight-medium text-dark">
                                    <?php echo $row['nombre']." ".$row['apellido']; ?>
                                    <?php if($row['id_titular']): ?>
                                        <span class="badge bg-purple-lt ms-1" title="Titular: <?php echo $row['nombre_titular']; ?>">Familiar</span>
                                    <?php endif; ?>
                                </div>
                                <div class="text-muted small"><?php echo $row['telefono']; ?></div>
                                <div class="text-muted small"><?php echo $row['correo']; ?></div> 
                                <?php if($row['id_titular']): ?>
                                    <div class="text-purple small" style="font-size: 0.7rem;">Depende de: <?php echo $row['nombre_titular']; ?></div>
                                <?php endif; ?>
                            </td>
                            <td><span class="badge bg-blue-lt"><?php echo $row['plan']; ?></span></td>
                            <td><span class="badge bg-<?php echo $status_color; ?>-lt"><?php echo $status_texto; ?></span></td>
                            <td>
                                <div class="text-<?php echo $status_color; ?> font-weight-bold"><?php echo date('d/m/Y', $vence); ?></div>
                                <small class="text-muted">
                                    <?php 
                                        if($row['id_titular']) echo "Sujeto al titular";
                                        else echo ($row['estado'] != 'activo' && $vence >= $hoy) ? 'Inactivo Manual' : 'Fecha de pago'; 
                                    ?>
                                </small>
                            </td>
                            <td>
                                <div class="btn-list flex-nowrap">
                                    <?php 
                                    $es_moroso = (strtotime($row['fecha_vencimiento']) < strtotime($hoy));
                                    ?>
                                    <a href="renovar_socio.php?id=<?php echo $row['id_socio']; ?>" 
                                        class="btn <?php echo $es_moroso ? 'btn-danger' : 'btn-white'; ?> btn-icon" 
                                        onclick="return confirm('<?php echo $es_moroso ? '¡AVISO: Socio vencido! Se aplicará multa. ' : ''; ?>¿Confirmar renovación?')" 
                                        title="Renovación">
                                        <i class="ti ti-refresh text-cyan"></i>
                                    </a>
                                    <a href="historial_socio.php?id=<?php echo $row['id_socio']; ?>" class="btn btn-white btn-icon" title="Ver Historial">
                                        <i class="ti ti-history text-green"></i>
                                    </a>
                                    <a href="editar_socio.php?id=<?php echo $row['id_socio']; ?>" class="btn btn-white btn-icon"><i class="ti ti-edit text-blue"></i></a>
                                    <a href="eliminar_socio.php?id=<?php echo $row['id_socio']; ?>" class="btn btn-white btn-icon" onclick="return confirm('¿Segura?');"><i class="ti ti-trash text-red"></i></a>
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