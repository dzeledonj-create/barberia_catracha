<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Barberia Catracha</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <?php include_once 'includes/header.php'; ?>
    <main>
        <section class="container-contenido">
            <section class="contenido">
                <p class="direccion">ZARAGOZA · C. DE FRAY JULIÁN GARCÉS, 3-5</p>
                <h1 class="contenido-titulo">Tu Estilo, <span class="texto-dorado">Nuestra Pasión</span></h1>
                <p class="contenido-description">
                    Expertos en cortes modernos, degradados y arreglo de barba. 
                    La experiencia premium que mereces en el corazón de Zaragoza.
                </p>
                <section class="buttons">
                    <a href="vistas/reservas.php" class="btn-reservas">RESERVA TU CITA AHORA</a>
                    <a href="vistas/servicios.php" class="btn-servicios">VER SERVICIOS</a>
                </section>
            </section>
        </section>
    </main>

    <section class="explora">
    <div class="container-small">
        <p class="section-subtitle">EXPLORA</p>
        <h2 class="section-title">Todo lo que necesitas saber</h2>
        <div class="title-underline"></div>

        <div class="explore-grid">
            <a href="vistas/servicios.php" class="explore-card">
                <div class="card-icon">✂</div>
                <div class="card-info">
                    <h3>SERVICIOS & PRECIOS</h3>
                    <p>Cortes, barba y combos</p>
                </div>
                <div class="card-arrow">›</div>
            </a>

            <a href="vistas/equipo.php" class="explore-card">
                <div class="card-icon">👥</div>
                <div class="card-info">
                    <h3>NUESTRO EQUIPO</h3>
                    <p>Ross, Luis y Rolando</p>
                </div>
                <div class="card-arrow">›</div>
            </a>

            <a href="vistas/galeria.php" class="explore-card">
                <div class="card-icon">🖼</div>
                <div class="card-info">
                    <h3>GALERÍA</h3>
                    <p>Tendencias y estilos</p>
                </div>
                <div class="card-arrow">›</div>
            </a>

            <a href="vistas/resenas.php" class="explore-card">
                <div class="card-icon">⭐</div>
                <div class="card-info">
                    <h3>RESEÑAS</h3>
                    <p>5 estrellas en Google</p>
                </div>
                <div class="card-arrow">›</div>
            </a>

            <a href="vistas/ubicacion.php" class="explore-card">
                <div class="card-icon">📍</div>
                <div class="card-info">
                    <h3>UBICACIÓN</h3>
                    <p>Cómo llegar</p>
                </div>
                <div class="card-arrow">›</div>
            </a>
        </div>
    </div>

    <div class="bottom-cta-bar">
        <a href="vistas/reservas.php">✂ RESERVAR CITA — EMPIEZA AQUÍ</a>
    </div>
</section>

    

    <script src="assets/script.js"></script>
</body>
</html>