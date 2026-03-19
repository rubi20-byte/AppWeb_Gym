<?php
session_start();
// si no es administrador, lo mandamos al login
if (!isset($_SESSION['rol']) || $_SESSION['rol'] != 'Administrador') {
    header("Location: login.php");
    exit();
}
// este archivo es el filtro para que 
// solo el jefe pueda entrar a las secciones de administracion