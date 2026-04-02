<?php
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_socio = intval($_POST['id_socio']);
    $nuevo_objetivo = $_POST['objetivo'];

    // Validamos que el objetivo sea uno de los permitidos
    $permitidos = ['Perdida de Peso', 'Ganancia Muscular', 'Resistencia'];
    
    if (in_array($nuevo_objetivo, $permitidos)) {
        $stmt = $conexion->prepare("UPDATE socios SET objetivo = ? WHERE id_socio = ?");
        $stmt->bind_param("si", $nuevo_objetivo, $id_socio);
        
        if ($stmt->execute()) {
            header("Location: asignar_plan.php?id=$id_socio&success=1");
        } else {
            echo "Error al actualizar";
        }
    }
}
?>