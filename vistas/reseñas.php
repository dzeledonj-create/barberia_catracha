<?php
include_once '../includes/header.php';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reseñas - Barbería Catracha</title>
    <link rel="stylesheet" href="/barberia_catracha/assets/style.css">
</head>

<body>

<section class="resenas-page">

    <section class="resenas-header">
        <p class="subtitle">LO QUE DICEN NUESTROS CLIENTES</p>
        <h1 class="titulo-principal">Reseñas</h1>
        <section class="underline"></section>
    </section>

    <section class="widget-container">
        <script src="https://elfsightcdn.com/platform.js" async></script>
        <div class="elfsight-app-fadea039-06dc-4982-ba29-5e89930fabc6" data-elfsight-app-lazy></div>
    </section>

    <section class="resenas-cta">
        <p>¿Te ha gustado tu corte? ¡Cuéntanos tu experiencia!</p>
        <div class="resenas-botones">
            <a href="https://maps.google.com/?q=Barbería+Catracha" 
               target="_blank" 
               class="btn-google">
                Escribir reseña en Google
            </a>
            <a href="reservas.php" class="btn-resenas-reserva">Reservar Cita</a>
        </div>
    </section>

</section>

<?php include_once '../includes/footer.php'; ?>

</body>
</html>