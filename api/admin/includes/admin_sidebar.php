<?php
    $script = $_SERVER['SCRIPT_NAME'] ?? '';
    if (strpos($script, '/api/admin') !== false) {
        $root = substr($script, 0, strpos($script, '/api')) ?: '';
        $adminBase = $root . '/api/admin';
    } elseif (strpos($script, '/admin') !== false) {
        $root = substr($script, 0, strpos($script, '/admin')) ?: '';
        $adminBase = $root . '/admin';
    } else {
        $root = '';
        $adminBase = '/admin';
    }
    $assetsBase = $root . '/assets';
    $publicRoot = $root ?: '/';
?>

<button id="admin-hamburger-btn" class="admin-hamburger" aria-label="Abrir menú admin" aria-expanded="false">☰</button>

<aside class="admin-sidebar">
    <section class="admin-logo">
        <strong>CATRACHA</strong>
        <span>Panel Admin</span>
    </section>

    <nav class="admin-menu">
        <a href="<?= $adminBase ?>/panel.php">Dashboard</a>
        <a href="<?= $adminBase ?>/GestionesAdmin/GestionReservas.php">Reservas</a>
        <a href="<?= $adminBase ?>/GestionesAdmin/GestionServicios.php">Servicios</a>
        <a href="<?= $adminBase ?>/GestionesAdmin/GestionEquipo.php">Equipo</a>
        <a href="<?= $adminBase ?>/GestionesAdmin/GestionGaleria.php">Galería</a>
        <a href="<?= $adminBase ?>/GestionesAdmin/GestionBlog.php">Blog</a>
        <a href="<?= $adminBase ?>/GestionesAdmin/GestionUbicacion.php">Ubicación</a>
    </nav>

    <section class="admin-bottom">
        <a href="<?= $publicRoot ?>">Ver Web Pública</a>
        <a href="<?= $publicRoot ?>/includes/logout.php">Cerrar Sesión</a>
    </section>
</aside>
    <script src="<?= $assetsBase ?>/script.js"></script>