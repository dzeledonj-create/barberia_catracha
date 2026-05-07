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
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>

<section class="admin-layout">

    <?php include_once 'includes/admin_sidebar.php'; ?>

    <main class="admin-main">
        <p class="admin-small-title">DASHBOARD</p>

        <h1>Bienvenido al Panel</h1>
        <p class="admin-subtitle">Gestiona toda la barbería desde aquí.</p>

        <section class="admin-stats">
            <section class="admin-card">
                <span>Total reservas</span>
                <strong>0</strong>
            </section>

            <section class="admin-card">
                <span>Pendientes</span>
                <strong>0</strong>
            </section>

            <section class="admin-card">
                <span>Confirmadas</span>
                <strong>0</strong>
            </section>

            <section class="admin-card">
                <span>Canceladas</span>
                <strong>0</strong>
            </section>
        </section>

        <section class="admin-panel-box">
            <h2>Reservas recientes</h2>
            <p>No hay reservas todavía.</p>
        </section>

        <section class="admin-actions">
            <a href="/barberia_catracha/Admin/GestionesAdmin/GestionServicios.php">Gestionar Servicios</a>
            <a href="/barberia_catracha/Admin/GestionesAdmin/GestionEquipo.php">Gestionar Equipo</a>
            <a href="/barberia_catracha/Admin/GestionesAdmin/GestionReservas.php">Ver Reservas</a>
            <a href="/barberia_catracha/Admin/GestionesAdmin/GestionGaleria.php">Gestionar Galería</a>
            <a href="/barberia_catracha/Admin/GestionesAdmin/GestionBlog.php">Editar Blog</a>
            <a href="/barberia_catracha/Admin/GestionesAdmin/GestionUbicacion.php">Actualizar Ubicación</a>
        </section>
    </main>

</section>

</body>
</html>