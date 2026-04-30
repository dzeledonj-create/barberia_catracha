<?php
    $isInVistas = strpos($_SERVER['PHP_SELF'], '/vistas/') !== false;
    $urlPrefix = $isInVistas ? '../' : '';
?>
<header class="main-header">
    <section class="container">
        <section class="icon">
            <img src="<?php echo $urlPrefix; ?>assets/img/logo.png" alt="logo barberia">
        </section>
        <section class="logo">
            <span class="text-white">BARBERÍA</span> 
            <span class="text-gold">CATRACHA</span>
        </section>

        <nav class="nav-menu">
            <ul>
                <li><a href="<?php echo $urlPrefix; ?>vistas/servicios.php">SERVICIOS</a></li>
                <li><a href="<?php echo $urlPrefix; ?>vistas/equipo.php">EQUIPO</a></li>
                <li><a href="<?php echo $urlPrefix; ?>vistas/galeria.php">GALERÍA</a></li>
                <li><a href="<?php echo $urlPrefix; ?>vistas/reseñas.php">RESEÑAS</a></li>
                <li><a href="<?php echo $urlPrefix; ?>vistas/ubicacion.php">UBICACIÓN</a></li>
                <li><a href="<?php echo $urlPrefix; ?>vistas/blog.php">BLOG</a></li>
            </ul>
        </nav>

        <section class="cta">
            <a href="<?php echo $urlPrefix; ?>vistas/reservas.php" class="btn-reserve">RESERVAR</a>
        </section>
    </section>
</header>