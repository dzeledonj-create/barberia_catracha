<?php
session_start();

if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel Admin</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<h1>Panel de Administración</h1>

<p>Bienvenido, <?= $_SESSION['nombre'] ?></p>

<ul>
    <li>Gestionar barberos</li>
    <li>Gestionar reservas</li>
    <li>Gestionar reseñas</li>
</ul>

<a href="../logout.php">Cerrar sesión</a>

</body>
</html>