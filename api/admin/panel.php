<?php
require_once __DIR__ . '/../Clases/BD.php';
require_once __DIR__ . '/../Clases/Reserva.php';
require_once __DIR__ . '/clases_admin/GestorUsuarios.php';

$totalReservas = Reserva::contarPorEstado('pendiente') + Reserva::contarPorEstado('confirmada') + Reserva::contarPorEstado('cancelada');
$pendientes = Reserva::contarPorEstado('pendiente');
$confirmadas = Reserva::contarPorEstado('confirmada');
$canceladas = Reserva::contarPorEstado('cancelada');
$recientesReservas = Reserva::obtenerRecientes(5);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Panel Admin</title>
    <link rel="stylesheet" href="/assets/style.css">
</head>
<body>

<section class="admin-layout">

    <?php include_once __DIR__ . '/includes/admin_sidebar.php'; ?>

    <main class="admin-main">
        <p class="admin-small-title">DASHBOARD</p>

        <h1>Bienvenido al Panel</h1>
        <p class="admin-subtitle">Gestiona toda la barbería desde aquí.</p>

        <?php if ($pendientes > 0): ?>
            <section class="admin-alert">
                <strong>Notificación:</strong> Tienes <?= htmlspecialchars($pendientes) ?> reserva<?= $pendientes === 1 ? '' : 's' ?> pendiente<?= $pendientes === 1 ? '' : 's' ?>.
                <a href="/barberia_catracha/api/admin/GestionesAdmin/GestionReservas.php">Ver reservas</a>
            </section>
        <?php endif; ?>

        <section class="admin-stats">
            <section class="admin-card">
                <span>Total reservas</span>
                <strong><?= htmlspecialchars($totalReservas) ?></strong>
            </section>

            <section class="admin-card">
                <span>Pendientes</span>
                <strong><?= htmlspecialchars($pendientes) ?></strong>
            </section>

            <section class="admin-card">
                <span>Confirmadas</span>
                <strong><?= htmlspecialchars($confirmadas) ?></strong>
            </section>

            <section class="admin-card">
                <span>Canceladas</span>
                <strong><?= htmlspecialchars($canceladas) ?></strong>
            </section>
        </section>

        <section class="admin-panel-box">
            <h2>Reservas recientes</h2>
            <?php if (empty($recientesReservas)): ?>
                <p>No hay reservas todavía.</p>
            <?php else: ?>
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Cliente</th>
                            <th>Barbero</th>
                            <th>Servicio</th>
                            <th>Fecha</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recientesReservas as $reserva): ?>
                            <tr>
                                <td><?= htmlspecialchars($reserva['reserva_id']) ?></td>
                                <td><?= htmlspecialchars($reserva['cliente']) ?></td>
                                <td><?= htmlspecialchars($reserva['barbero']) ?></td>
                                <td><?= htmlspecialchars($reserva['servicio']) ?></td>
                                <td><?= htmlspecialchars($reserva['fecha_hora']) ?></td>
                                <td><span class="estado estado-<?= htmlspecialchars($reserva['estado']) ?>"><?= ucfirst(htmlspecialchars($reserva['estado'])) ?></span></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </section>

        <section class="admin-actions">
            <button id="admin-hamburger-btn" class="admin-hamburger" aria-label="Abrir menú admin" aria-expanded="false">☰</button>

<aside class="admin-sidebar">
    <section class="admin-logo">
        <strong>CATRACHA</strong>
        <span>Panel Admin</span>
    </section>

    <nav class="admin-menu">
        <a href="/admin/panel.php">Dashboard</a>
        <a href="/admin/GestionesAdmin/GestionReservas.php">Reservas</a>
        <a href="/admin/GestionesAdmin/GestionServicios.php">Servicios</a>
        <a href="/admin/GestionesAdmin/GestionEquipo.php">Equipo</a>
        <a href="/admin/GestionesAdmin/GestionGaleria.php">Galería</a>
        <a href="/admin/GestionesAdmin/GestionBlog.php">Blog</a>
        <a href="/admin/GestionesAdmin/GestionUbicacion.php">Ubicación</a>
    </nav>

    <section class="admin-bottom">
        <a href="/">Ver Web Pública</a>
        <a href="/includes/logout.php">Cerrar Sesión</a>
    </section>
</aside>
<script src="/assets/script.js"></script>
        </section>
    </main>

</section>

</body>
</html>
