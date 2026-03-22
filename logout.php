<?php
session_start();

// Destruimos todas las variables de sesión (id_admin, id_socio, rol, etc.)
$_SESSION = array();

// Si se desea destruir la sesión completamente, borramos también la cookie de sesión.
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Finalmente, destruimos la sesión.
session_destroy();

// Redirigimos al login con un mensaje de éxito (opcional)
header("Location: login.php?logout=success");
exit();
?>