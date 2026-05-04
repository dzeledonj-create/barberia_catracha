<?php
require_once '../clases/MuralSugerencia.php';

// 1. Obtener las categorías reales desde la base de datos
$categorias_db = MuralSugerencia::obtenerCategoriasRecientes();

// 2. Capturar la categoría seleccionada
$categoria_actual = isset($_GET['categoria']) ? $_GET['categoria'] : 'todos';

// 3. Filtrar los cortes
if ($categoria_actual === 'todos') {
    $sugerencias = MuralSugerencia::obtenerActivas();
} else {
    $sugerencias = MuralSugerencia::obtenerPorEstilo($categoria_actual);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Galería Dinámica - Barbería Catracha</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>

<?php include_once '../includes/header.php'; ?>

<main>
    <section class="galeria-hero">
        <section class="container-texto">
            <p class="subtitle">INSPIRACIÓN</p>
            <h1 class="titulo-galeria">Mural de <span class="text-gold">Estilos</span></h1>
            <p class="descripcion-galeria">
                Explora las últimas tendencias y elige tu próximo cambio de look. 
                Nuestros profesionales dominan desde los clásicos hasta los degradados más modernos.
            </p>
        </section>

        <nav class="categorias-nav">
            <ul class="categorias-lista">
                <li>
                    <a href="galeria.php?categoria=todos" 
                       class="<?= $categoria_actual === 'todos' ? 'active' : '' ?>">
                       TODOS
                    </a>
                </li>

                <?php foreach ($categorias_db as $cat): ?>
                    <li>
                        <a href="galeria.php?categoria=<?= urlencode(strtolower($cat)) ?>" 
                           class="<?= strtolower($categoria_actual) === strtolower($cat) ? 'active' : '' ?>">
                            <?= strtoupper(htmlspecialchars($cat)) ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </nav>
    </section>

    <section class="galeria-grid-container">
        <section class="galeria-grid">
            <?php if (empty($sugerencias)): ?>
                <section class="no-data-msg">
                    <p class="no-data">No hay cortes disponibles en esta categoría.</p>
                </section>
            <?php else: ?>
                <?php foreach ($sugerencias as $corte): ?>
                    <section class="galeria-item">
                        <section class="image-wrapper">
                            <img src="../<?= htmlspecialchars($corte['imagen_url']) ?>" alt="<?= htmlspecialchars($corte['nombre_corte']) ?>">
                            <section class="overlay">
                                <span class="tag-estilo"><?= htmlspecialchars($corte['estilo']) ?></span>
                            </section>
                        </section>
                        <section class="item-info">
                            <h3><?= htmlspecialchars($corte['nombre_corte']) ?></h3>
                            <p><?= htmlspecialchars($corte['descripcion']) ?></p>
                        </section>
                    </section>
                <?php endforeach; ?>
            <?php endif; ?>
        </section>
    </section>

    <section class="galeria-cta">
        <section class="cta-box">
            <h2>¿Te gusta alguno de estos estilos?</h2>
            <a href="reservas.php" class="btn-reservas">PEDIR CITA AHORA</a>
        </section>
    </section>
</main>

<?php include_once '../includes/footer.php'; ?>

</body>
</html>