<?php
session_start();

if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

require_once '../clases/Reserva.php';

$id = $_GET['id'];
$estado = $_GET['estado'];

Reserva::cambiarEstado($id, $estado);

header("Location: reservas.php");
exit;