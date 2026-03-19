<?php
// iniciamos la sesion
session_start();

// borramos todas las variables de la sesio
session_unset();

// destruimos la sesion
session_destroy();

// mandamos al usuario de regreso al login 
header("Location: login.php");
exit();

// este archivo es el que saca a la gente del sistema y limpia la memoria para que quede seguro
?>