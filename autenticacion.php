<?php
session_start();
include 'config.php';

if ($_POST) {
    $user = mysqli_real_escape_string($conexion, $_POST['usuario']);
    $pass = $_POST['password'];

    // Buscamos al usuario por nombre y que este activo
    $query = "SELECT * FROM usuarios WHERE usuario = '$user' AND estado = 'activo'";
    $res = $conexion->query($query);

    if ($res && $res->num_rows > 0) {
        $u = $res->fetch_assoc();
        
        // Validación de contraseña
        if ($pass == $u['password']) {
            // Guardamos los datos en la sesion
            $_SESSION['id_usuario'] = $u['id_usuario'];
            $_SESSION['nombre'] = $u['nombre_completo'];
            $_SESSION['rol'] = $u['rol']; 
            $_SESSION['id_socio'] = $u['id_socio']; // Lo guardamos directo

            // Limpiamos espacios y pasamos a minúsculas para comparar seguro
            $rol_verificar = strtolower(trim($u['rol'])); 

            if ($rol_verificar == 'socio') {
                header("Location: dashboard_socio.php");
                exit(); 
            } else {
                // Administrador, Recepcionista, etc.
                header("Location: index.php");
                exit();
            }

        } else {
            echo "<script>alert('Contraseña incorrecta'); window.location='login.php';</script>";
        }
    } else {
        echo "<script>alert('Usuario no encontrado o inactivo'); window.location='login.php';</script>";
    }
}
?>