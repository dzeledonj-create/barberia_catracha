<?php
session_start();

if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

require_once '../clases/Reserva.php';

Reserva::eliminar($_GET['id']);

header("Location: reservas.php");
exit;