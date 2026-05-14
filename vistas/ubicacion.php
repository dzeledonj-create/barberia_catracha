<?php
require_once '../clases/DatosUbicacion.php';
require_once '../clases/Horario.php';

$datosUbicacion = DatosUbicacion::obtener();
$horarios = Horario::obtenerTodos();
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Ubicación - Barbería Catracha</title>

    <link rel="stylesheet" href="../assets/style.css">

    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <link rel="icon"
          href="/barberia_catracha/assets/img/logo.png"
          type="image/png">
</head>

<body class="bg-dark">

<?php include_once '../includes/header.php'; ?>

<main class="ubicacion-page">

    <section class="ubicacion-header">
        <p class="subtitle">ENCUÉNTRANOS</p>

        <h1 class="titulo-principal">
            NUESTRA <span class="text-gold">UBICACIÓN</span>
        </h1>

        <section class="underline"></section>

        <p class="descripcion-header">
            Visítanos en el sector de San José, Zaragoza.
            Un espacio diseñado para ofrecerte el mejor servicio de barbería.
        </p>
    </section>

    <section class="ubicacion-grid container">

        <article class="ubicacion-info">

            <!-- DIRECCIÓN -->
            <section class="info-item">

                <header class="info-header">
                    <i class="fas fa-map-marker-alt"></i>
                    <h2>DIRECCIÓN</h2>
                </header>

                <p>
                    <?= htmlspecialchars($datosUbicacion['direccion']) ?>
                </p>

            </section>

            <!-- HORARIOS -->
            <section class="info-item">

                <header class="info-header">
                    <i class="fas fa-clock"></i>
                    <h2>HORARIOS</h2>
                </header>

                <ul class="horario-lista">

                    <?php foreach ($horarios as $horario): ?>

                        <li>

                            <span class="dia">
                                <?= htmlspecialchars($horario['dia_semana']) ?>
                            </span>

                            <span class="horas">

                                <?php if (!empty($horario['cerrado'])): ?>

                                    Cerrado

                                <?php else: ?>

                                    <?= substr($horario['hora_apertura'], 0, 5) ?>
                                    –
                                    <?= substr($horario['hora_cierre'], 0, 5) ?>

                                <?php endif; ?>

                            </span>

                        </li>

                    <?php endforeach; ?>

                </ul>

            </section>

            <!-- CONTACTO -->
            <section class="info-item">

                <header class="info-header">
                    <i class="fas fa-phone-alt"></i>
                    <h2>CONTACTO</h2>
                </header>

                <p>
                    Teléfono:
                    <span>
                        <?= htmlspecialchars($datosUbicacion['telefono']) ?>
                    </span>
                </p>

                <p>
                    WhatsApp:
                    <span>
                        <?= htmlspecialchars($datosUbicacion['whatsapp']) ?>
                    </span>
                </p>

            </section>

        </article>

        <!-- MAPA -->
        <article class="ubicacion-mapa">

            <iframe
                src="<?= htmlspecialchars($datosUbicacion['mapa_embed']) ?>"
                allowfullscreen=""
                loading="lazy">
            </iframe>

        </article>

    </section>

    <section class="equipo-cta">
        <a href="reservas.php" class="btn-reservar-equipo">
            SOLICITAR CITA AHORA
        </a>
    </section>

</main>

<?php include_once '../includes/footer.php'; ?>

</body>
</html>