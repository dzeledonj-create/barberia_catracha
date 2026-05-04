<?php
require_once '../clases/BD.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ubicación - Barbería Catracha</title>
    <link rel="stylesheet" href="../assets/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-dark">

<?php include_once '../includes/header.php'; ?>

<main class="ubicacion-page">
    <section class="equipo-header">
        <p class="subtitle">ENCUÉNTRANOS</p>
        <h1 class="titulo-principal">NUESTRA <span class="text-gold">UBICACIÓN</span></h1>
        <section class="underline"></section>
        <p class="descripcion-header">
            Visítanos en el sector de San José, Zaragoza. Un espacio diseñado para ofrecerte el mejor servicio de barbería.
        </p>
    </section>

    <section class="ubicacion-grid container">
        
        <article class="ubicacion-info">
            <section class="info-item">
                <header class="info-header">
                    <i class="fas fa-map-marker-alt"></i>
                    <h2>DIRECCIÓN</h2>
                </header>
                <p>C. de Fray Julián Garcés, 3-5</p>
                <p>50007 Zaragoza, España</p>
            </section>

            <section class="info-item">
                <header class="info-header">
                    <i class="fas fa-clock"></i>
                    <h2>HORARIOS</h2>
                </header>
                <ul class="horario-lista">
                    <li><span class="dia">Lunes</span> <span class="horas">10:00 – 19:30</span></li>
                    <li><span class="dia">Martes - Viernes</span> <span class="horas">09:30 – 20:30</span></li>
                    <li><span class="dia">Sábado</span> <span class="horas">09:30 – 20:30</span></li>
                    <li><span class="dia">Domingo</span> <span class="horas">10:00 – 13:30</span></li>
                </ul>
            </section>

            <section class="info-item">
                <header class="info-header">
                    <i class="fas fa-phone-alt"></i>
                    <h2>CONTACTO</h2>
                </header>
                <p>Teléfono: <span>+34 600 000 000</span></p>
                <p>Email: <span>info@barberiacatracha.com</span></p>
            </section>
        </article>

        <article class="ubicacion-mapa">
            <iframe 
                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2981.163351284568!2d-0.8887469!3d41.6306!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0xd5914e6c764e7c3%3A0x7c7c7c7c7c7c7c7c!2sC.%20de%20Fray%20Juli%C3%A1n%20Garc%C3%A9s%2C%203-5%2C%2050007%20Zaragoza!5e0!3m2!1ses!2ses!4v1714810000000" 
                allowfullscreen="" 
                loading="lazy">
            </iframe>
        </article>
    </section>

    <section class="equipo-cta">
        <a href="reservas.php" class="btn-reservar-equipo">SOLICITAR CITA AHORA</a>
    </section>
</main>

<?php include_once '../includes/footer.php'; ?>

</body>
</html>