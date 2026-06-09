<?php
/**
 * Vista del equipo de barberos
 * Muestra los barberos activos de la barbería con su información y foto
 */

// Carga la clase de conexión a la base de datos
require_once __DIR__ . '/../Clases/BD.php';
// Carga la clase Barbero con sus métodos y propiedades
require_once __DIR__ . '/../Clases/Barbero.php';

// Obtiene todos los barberos con estado "activo" desde la base de datos
$barberos = Barbero::obtenerActivos();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Equipo - Barbería Catracha</title>
    <!-- Hoja de estilos principal de la aplicación -->
    <link rel="stylesheet" href="../../assets/style.css">
    <!-- Ícono de la pestaña del navegador -->
    <link rel="icon" href="../../assets/img/logo.png" type="image/png">
</head>
<body>

<!-- Cabecera común reutilizable (navegación, logo, etc.) -->
<?php include_once __DIR__ . '/../includes/header.php'; ?>

<main>
    <!-- Sección principal que envuelve todo el contenido del equipo -->
    <section class="equipo-section">

        <!-- Encabezado descriptivo de la sección -->
        <header class="equipo-header">
            <p class="subtitle">PROFESIONALES DE CONFIANZA</p>
            <h1 class="titulo-principal">Nuestro Equipo</h1>
            <span class="underline"></span>
            <p class="descripcion-header">
                Tres barberos con pasión, técnica y dedicación para darte el mejor resultado.
            </p>
        </header>

        <!-- Grid que contiene las tarjetas de cada barbero -->
        <section class="equipo-grid">

            <!-- Itera sobre cada barbero activo obtenido de la base de datos -->
            <?php foreach ($barberos as $barbero): ?>

            <!-- Tarjeta individual de cada barbero -->
            <section class="barbero-card">

                <!-- Contenedor de la foto del barbero -->
                <section class="barbero-img">
                    <!--
                        htmlspecialchars() convierte caracteres especiales a entidades HTML
                        para evitar inyección de código (XSS)
                    -->
                    <img src="<?= htmlspecialchars($barbero->getFotoUrl()) ?>" 
                         alt="<?= htmlspecialchars($barbero->getNombre()) ?>">
                </section>

                <!-- Información textual del barbero -->
                <section class="barbero-info">
                    <!-- Nombre del barbero -->
                    <h2><?= htmlspecialchars($barbero->getNombre()) ?></h2>

                    <!-- Especialidad o rango del barbero (ej: Senior Barber) -->
                    <p class="rango">
                        <?= htmlspecialchars($barbero->getEspecialidad()) ?>
                    </p>

                    <!-- Descripción breve o biografía del barbero -->
                    <p class="bio">
                        <?= htmlspecialchars($barbero->getDescripcion()) ?>
                    </p>

                    <!-- Etiquetas de los servicios que ofrece el barbero -->
                    <section class="tags">
                        <span>FADE</span>
                        <span>CORTE</span>
                        <span>BARBA</span>
                    </section>
                </section>

            </section>
            <?php endforeach; ?> <!-- Fin del bucle de barberos -->

        </section> <!-- Fin del equipo-grid -->

        <!-- Llamada a la acción: botón para ir a la página de reservas -->
        <section class="equipo-cta">
            <a href="<?= BASE_PATH ?>/api/vistas/reservas.php" class="btn-reservar-equipo">
                ELIGE TU BARBERO Y RESERVA
            </a>
        </section>

    </section> <!-- Fin de equipo-section -->
</main>

</body>
<!-- Pie de página común reutilizable (redes sociales, contacto, etc.) -->
<?php include_once __DIR__ . '/../includes/footer.php'; ?>
</html>