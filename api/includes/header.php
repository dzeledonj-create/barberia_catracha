<?php
require_once __DIR__ . '/../Clases/BD.php';
?>

<header class="main-header">
    <section class="container">
        <a href="<?= BASE_PATH ?>/api/vistas/index.php" class="icon">
            <section class="icon">
                <img src="../../assets/img/logo.png" alt="logo barberia">
            </section>
            <section class="logo">
                <span class="text-white">BARBERÍA</span>
                <span class="text-gold">CATRACHA</span>
            </section>
        </a>

        <nav class="nav-menu">
            <ul>
                <li><a href="<?= BASE_PATH ?>/api/vistas/index.php">INICIO</a></li>
                <li><a href="<?= BASE_PATH ?>/api/vistas/servicios.php">SERVICIOS</a></li>
                <li><a href="<?= BASE_PATH ?>/api/vistas/equipo.php">EQUIPO</a></li>
                <li><a href="<?= BASE_PATH ?>/api/vistas/galeria.php">GALERÍA</a></li>
                <li><a href="<?= BASE_PATH ?>/api/vistas/reseñas.php">RESEÑAS</a></li>
                <li><a href="<?= BASE_PATH ?>/api/vistas/ubicacion.php">UBICACIÓN</a></li>
                <li><a href="<?= BASE_PATH ?>/api/vistas/blog.php">BLOG</a></li>
            </ul>
        </nav>

        <section class="cta">
            <a href="<?= BASE_PATH ?>/api/vistas/reservas.php" class="btn-reserve">RESERVAR</a>
        </section>
        
        <!-- Hamburger button for mobile menu -->
        <button id="hamburger-btn" class="hamburger-btn" aria-label="Abrir menú" aria-expanded="false">
            <span class="hamburger-line"></span>
            <span class="hamburger-line"></span>
            <span class="hamburger-line"></span>
        </button>

        <nav id="hamburger-menu" class="hamburger-menu" aria-hidden="true">
            <ul>
                <li><a href="<?= BASE_PATH ?>/api/vistas/index.php">INICIO</a></li>
                <li><a href="<?= BASE_PATH ?>/api/vistas/servicios.php">SERVICIOS</a></li>
                <li><a href="<?= BASE_PATH ?>/api/vistas/equipo.php">EQUIPO</a></li>
                <li><a href="<?= BASE_PATH ?>/api/vistas/galeria.php">GALERÍA</a></li>
                <li><a href="<?= BASE_PATH ?>/api/vistas/reseñas.php">RESEÑAS</a></li>
                <li><a href="<?= BASE_PATH ?>/api/vistas/ubicacion.php">UBICACIÓN</a></li>
                <li><a href="<?= BASE_PATH ?>/api/vistas/blog.php">BLOG</a></li>
            </ul>
        </nav>
    </section>
</header>