<?php
require_once '../clases/BD.php';
require_once __DIR__ . '/../clases/Barbero.php';
$barberos = Barbero::obtenerActivos();
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
        <header class="equipo-header">
            <p class="subtitle">PROFESIONALES DE CONFIANZA</p>
            <h1 class="titulo-principal">Nuestro Equipo</h1>
            <span class="underline"></span> <p class="descripcion-header">
                Tres barberos con pasión, técnica y dedicación para darte el mejor resultado.
            </p>
        </header>

        <section class="equipo-grid">
            
            <section class="barbero-card">
                <section class="barbero-img">
                    <img src="img/ross.jpg" alt="Ross - Barbero Senior">
                </section>
                <aside class="barbero-info">
                    <h2>Ross</h2>
                    <p class="rango">BARBERO SENIOR</p>
                    <p class="bio">Especialista en degradados y diseños de línea. Más de 8 años de experiencia perfeccionando cada detalle.</p>
                    <nav class="tags">
                        <span>FADES</span>
                        <span>DISEÑOS</span>
                        <span>BARBA</span>
                    </nav>
                </aside>
            </section>

            <section class="barbero-card">
                <section class="barbero-img">
                    <img src="assets/img<?php echo htmlspecialchars($barberos['luis.jpeg']); ?>" alt="Luis - Barbero Especialista">
                </section>
                <aside class="barbero-info">
                    <h2>Luis</h2>
                    <p class="rango">BARBERO ESPECIALISTA</p>
                    <p class="bio">Experto en cortes clásicos y modernos. Su precisión con la navaja y tijeras es insuperable.</p>
                    <nav class="tags">
                        <span>CLÁSICOS</span>
                        <span>POMPADOUR</span>
                        <span>TINTE</span>
                    </nav>
                </aside>
            </section>

<?php endforeach; ?>

</section>

        </section>

        <footer class="equipo-cta">
            <a href="reservas.php" class="btn-reservar-equipo">ELIGE TU BARBERO Y RESERVA</a>
        </footer>
    </section>
</main>

</body>
</html>