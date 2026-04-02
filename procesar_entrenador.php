<?php
include 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 1. Recibimos los datos y los limpiamos
    $nombre       = mysqli_real_escape_string($conexion, $_POST['nombre']);
    $especialidad = mysqli_real_escape_string($conexion, $_POST['especialidad']);
    $telefono     = mysqli_real_escape_string($conexion, $_POST['telefono']);
    $correo       = mysqli_real_escape_string($conexion, $_POST['correo']);
    $turno        = $_POST['turno'];
    $comision     = $_POST['comision'];
    $fecha        = date('Y-m-d');
    
    // 2. Procesamos la contraseña (esto es lo que faltaba)
    $pass_plana   = $_POST['password'];

    // 3. El INSERT corregido con todas las columnas
    $sql = "INSERT INTO entrenadores (nombre, especialidad, telefono, correo, password, fecha_contratacion, tarifa_comision, turno, estado) 
            VALUES ('$nombre', '$especialidad', '$telefono', '$correo', '$pass_plana', '$fecha', '$comision', '$turno', 'activo')";

    if ($conexion->query($sql)) {
        // Si funciona, te manda a la lista
        header("Location: entrenadores.php?msj=ok");
        exit();
    } else {
        // Si falla, aquí te va a decir por qué (ejemplo: si falta una columna)
        echo "Error en la base de datos: " . $conexion->error;
    }
} else {
    echo "No se recibieron datos.";
}
?>