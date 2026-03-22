<?php
session_start();
// Si no hay rol o el rol no es admin, mandarlo al login
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
    header("Location: login.php");
    exit();
}
?>