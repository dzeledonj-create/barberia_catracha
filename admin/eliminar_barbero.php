<?php
session_start();

if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

require_once '../clases/Barbero.php';

Barbero::eliminar($_GET['id']);

header("Location: barberos.php");
exit;