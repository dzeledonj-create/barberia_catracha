<?php
require_once __DIR__ . '/../Clases/BD.php';
require_once __DIR__ . '/../Clases/Barbero.php';
$barberos = Barbero::obtenerActivos();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Equipo - Barbería Catracha</title>
    <link rel="stylesheet" href="../../assets/style.css">
    <link rel="icon" href="../../assets/img/logo.png" type="image/png">
</head>
<body>

<?php include_once __DIR__ . '/../includes/header.php'; ?>

<main>
    <section class="equipo-section">
        <header class="equipo-header">
            <p class="subtitle">PROFESIONALES DE CONFIANZA</p>
            <h1 class="titulo-principal">Nuestro Equipo</h1>
            <span class="underline"></span> <p class="descripcion-header">
                Tres barberos con pasión, técnica y dedicación para darte el mejor resultado.
            </p>
        </header>

        <section class="equipo-grid">

        <?php foreach ($barberos as $barbero): ?>

        <section class="barbero-card">

        <section class="barbero-img">
            <img src="/barberia_catracha/assets/img/<?= htmlspecialchars($barbero['foto_url']) ?>" 
                 alt="<?= htmlspecialchars($barbero['nombre']) ?>">
        </section>

        <section class="barbero-info">
            <h2><?= htmlspecialchars($barbero['nombre']) ?></h2>

            <p class="rango">
                <?= htmlspecialchars($barbero['especialidad'] ?? 'Barbero') ?>
            </p>

            <p class="bio">
                <?= htmlspecialchars($barbero['descripcion'] ?? 'Profesional de barbería.') ?>
            </p>

            <section class="tags">
                <span>FADE</span>
                <span>CORTE</span>
                <span>BARBA</span>
            </section>
        </section>

    </section>
        <?php endforeach; ?>
    </section>
    <section class="equipo-cta">
            <a href="/barberia_catracha/api/vistas/reservas.php" class="btn-reservar-equipo">ELIGE TU BARBERO Y RESERVA</a>
        </section>
    </section>
    </section>
</main>

</body>
<?php include_once __DIR__ . '/../includes/footer.php'; ?>
</html>