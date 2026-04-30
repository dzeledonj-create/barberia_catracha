<?php
require_once '../clases/BD.php';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Equipo - Barbería Catracha</title>

    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>

<?php include_once '../includes/header.php'; ?>

<main>

<section class="equipo-section">
    <section class="equipo-section">
    <div class="equipo-header">
        <p class="subtitle">PROFESIONALES DE CONFIANZA</p>
        <h1 class="titulo-principal">Nuestro Equipo</h1>
        <div class="underline"></div>
        <p class="descripcion-header">
            Tres barberos con pasión, técnica y dedicación para darte el mejor resultado.
        </p>
    </div>

    <div class="equipo-grid">
        <div class="barbero-card">
            <div class="barbero-img">
                <img src="img/ross.jpg" alt="Ross - Barbero Senior">
            </div>
            <div class="barbero-info">
                <h2>Ross</h2>
                <p class="rango">BARBERO SENIOR</p>
                <p class="bio">Especialista en degradados y diseños de línea. Más de 8 años de experiencia perfeccionando cada detalle.</p>
                <div class="tags">
                    <span>FADES</span>
                    <span>DISEÑOS</span>
                    <span>BARBA</span>
                </div>
            </div>
        </div>

        <div class="barbero-card">
            <div class="barbero-img">
                <img src="img/luis.jpg" alt="Luis - Barbero Especialista">
            </div>
            <div class="barbero-info">
                <h2>Luis</h2>
                <p class="rango">BARBERO ESPECIALISTA</p>
                <p class="bio">Experto en cortes clásicos y modernos. Su precisión con la navaja y tijeras es insuperable.</p>
                <div class="tags">
                    <span>CLÁSICOS</span>
                    <span>POMPADOUR</span>
                    <span>TINTE</span>
                </div>
            </div>
        </div>

        <div class="barbero-card">
            <div class="barbero-img">
                <img src="img/rolando.jpg" alt="Rolando - Barbero Estilista">
            </div>
            <div class="barbero-info">
                <h2>Rolando</h2>
                <p class="rango">BARBERO ESTILISTA</p>
                <p class="bio">Maestro en estilos urbanos y tendencias actuales. Referente en Zaragoza para looks vanguardistas.</p>
                <div class="tags">
                    <span>URBANO</span>
                    <span>CROP</span>
                    <span>TENDENCIAS</span>
                </div>
            </div>
        </div>
    </div>

    <div class="equipo-cta">
        <a href="reservas.php" class="btn-reservar-equipo">ELIGE TU BARBERO Y RESERVA</a>
    </div>
</section>
</section>

</main>

</body>
</html>