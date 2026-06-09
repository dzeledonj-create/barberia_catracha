<button id="admin-hamburger-btn" class="admin-hamburger" aria-label="Abrir menú admin" aria-expanded="false">☰</button>

<aside class="admin-sidebar">
    <section class="admin-logo">
        <strong>CATRACHA</strong>
        <span>Panel Admin</span>
    </section>

    <nav class="admin-menu">
        <?php if (($_SESSION['rol'] ?? '') !== 'barbero'): ?>
        <a href="panel.php">Dashboard</a>
        <?php endif; ?>
        <a href="GestionReservas.php">Reservas</a>
        <?php if (($_SESSION['rol'] ?? '') !== 'barbero'): ?>
        <a href="GestionServicios.php">Servicios</a>
        <a href="GestionEquipo.php">Equipo</a>
        <a href="GestionGaleria.php">Galería</a>
        <a href="GestionBlog.php">Blog</a>
        <a href="GestionUbicacion.php">Ubicación</a>
        <?php endif; ?>
        <a href="CambiarPassword.php">Contraseña</a>
    </nav>

    <section class="admin-bottom">
        <a href="../../vistas/index.php">Ver Web Pública</a>
        <a href="../../includes/logout.php">Cerrar Sesión</a>
    </section>
</aside>

<script src="../../../assets/script.js?v=<?= time() ?>"></script>