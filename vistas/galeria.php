<?php
require_once '../clases/MuralSugerencia.php';

// Capturamos la categoría de la URL. Si no hay ninguna, por defecto es 'todos'.
$categoria_actual = isset($_GET['categoria']) ? $_GET['categoria'] : 'todos';

// Lógica para filtrar los resultados de la base de datos
if ($categoria_actual === 'todos') {
    $sugerencias = MuralSugerencia::obtenerActivas();
} else {
    // Usamos el método que ya tienes en la clase para buscar por nombre de estilo
    $sugerencias = MuralSugerencia::obtenerPorEstilo($categoria_actual);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Galería de Estilos - Barbería Catracha</title>
    <link rel="stylesheet" href="../assets/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
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
                <li><a href="galeria.php?categoria=todos" class="<?php echo $categoria_actual === 'todos' ? 'active' : ''; ?>">TODOS</a></li>
                <li><a href="galeria.php?categoria=fades" class="<?php echo $categoria_actual === 'fades' ? 'active' : ''; ?>">FADES</a></li>
                <li><a href="galeria.php?categoria=clasicos" class="<?php echo $categoria_actual === 'clasicos' ? 'active' : ''; ?>">CLÁSICOS</a></li>
                <li><a href="galeria.php?categoria=diseños" class="<?php echo $categoria_actual === 'diseños' ? 'active' : ''; ?>">DISEÑOS</a></li>
                <li><a href="galeria.php?categoria=modernos" class="<?php echo $categoria_actual === 'modernos' ? 'active' : ''; ?>">MODERNOS</a></li>
                <li><a href="galeria.php?categoria=cortos" class="<?php echo $categoria_actual === 'cortos' ? 'active' : ''; ?>">CORTOS</a></li>
                <li><a href="galeria.php?categoria=tendencia" class="<?php echo $categoria_actual === 'tendencia' ? 'active' : ''; ?>">TENDENCIA</a></li>
            </ul>
        </nav>
    </section>

    <section class="galeria-grid-container">
        <section class="galeria-grid">
            <?php if (empty($sugerencias)): ?>
                <section class="no-data-msg">
                    <p class="no-data">No se han encontrado estilos en la categoría "<?php echo htmlspecialchars($categoria_actual); ?>".</p>
                </section>
            <?php else: ?>
                <?php foreach ($sugerencias as $corte): ?>
                    <section class="galeria-item">
                        <section class="image-wrapper">
                            <img src="../<?php echo htmlspecialchars($corte['imagen_url']); ?>" alt="<?php echo htmlspecialchars($corte['nombre_corte']); ?>">
                            <section class="overlay">
                                <span class="tag-estilo"><?php echo htmlspecialchars($corte['estilo']); ?></span>
                            </section>
                        </section>
                        <section class="item-info">
                            <h3><?php echo htmlspecialchars($corte['nombre_corte']); ?></h3>
                            <p><?php echo htmlspecialchars($corte['descripcion']); ?></p>
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