<?php
session_start();

if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'barbero') {
    header("Location: ../login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel Barbero</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<h1>Panel de Barbero</h1>

<p>Bienvenido, <?= $_SESSION['nombre'] ?></p>

<ul>
    <li>Ver mis reservas</li>
    <li>Aceptar reservas</li>
    <li>Cancelar reservas</li>
</ul>

<a href="../includes/logout.php">Cerrar sesión</a>

</body>
</html>