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
    <div class="container-equipo">

        <p class="equipo-subtitle">PROFESIONALES</p>
        <h2 class="equipo-title">Nuestro Equipo</h2>

        <div class="equipo-grid">

            <div class="equipo-card">
                <img src="../img/barbero1.jpg" alt="Barbero Juan">
                <div class="equipo-info">
                    <h3>Juan Pérez</h3>
                    <p>Especialista en degradados</p>
                </div>
            </div>

            <div class="equipo-card">
                <img src="../img/barbero2.jpg" alt="Barbero Carlos">
                <div class="equipo-info">
                    <h3>Carlos López</h3>
                    <p>Experto en barba</p>
                </div>
            </div>

            <div class="equipo-card">
                <img src="../img/barbero3.jpg" alt="Barbero Miguel">
                <div class="equipo-info">
                    <h3>Miguel Torres</h3>
                    <p>Cortes clásicos</p>
                </div>
            </div>

        </div>

    </div>
</section>

</main>

</body>
</html>