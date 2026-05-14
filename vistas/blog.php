<?php
require_once '../Clases/BlogPost.php';

$posts = BlogPost::obtenerTodos();
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Blog | Barbería Catracha</title>

    <link rel="stylesheet" href="../assets/style.css">

    <link rel="icon"
          href="/barberia_catracha/assets/img/logo.png"
          type="image/png">

    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>

<body class="bg-dark">

<?php include_once '../includes/header.php'; ?>

<main class="blog-page">

    <!-- HEADER -->
    <section class="blog-header">

        <p class="subtitle">NUESTRO BLOG</p>

        <h1 class="titulo-principal">
            La Barbería en <span class="text-gold">Imágenes</span>
        </h1>

        <section class="underline"></section>

        <p class="descripcion-header">
            Consejos, estilos, tendencias y contenido visual de Barbería Catracha.
        </p>

    </section>

    <!-- GRID BLOG -->
    <section class="blog-grid">

        <?php foreach ($posts as $post): ?>

            <article class="blog-card">

                <!-- MEDIA -->
                <section class="blog-media">

                    <?php if (!empty($post['instagram_embed'])): ?>

                        <section class="instagram-wrapper">
                            <?= $post['instagram_embed'] ?>
                        </section>

                    <?php elseif (!empty($post['imagen_url'])): ?>

                        <img src="../<?= htmlspecialchars($post['imagen_url']) ?>"
                             alt="<?= htmlspecialchars($post['titulo']) ?>">

                    <?php else: ?>

                        <img src="../assets/img/blog/default.jpg"
                             alt="Blog Barbería Catracha">

                    <?php endif; ?>

                </section>

                <!-- INFO -->
                <section class="blog-info">

                    <span class="blog-tag">

                        <?php
                        if (!empty($post['etiquetas'])) {
                            echo htmlspecialchars($post['etiquetas']);
                        } else {
                            echo 'BARBERÍA';
                        }
                        ?>

                    </span>

                    <h2>
                        <?= htmlspecialchars($post['titulo']) ?>
                    </h2>

                    <p>

                        <?php
                        $texto = strip_tags($post['contenido']);
                        echo htmlspecialchars(substr($texto, 0, 140));
                        ?>...

                    </p>

                    <a href="reservas.php" class="blog-btn">
                        RESERVA CITA
                    </a>

                </section>

            </article>

        <?php endforeach; ?>

    </section>

</main>

<?php include_once '../includes/footer.php'; ?>

<!-- INSTAGRAM EMBED -->
<script async src="//www.instagram.com/embed.js"></script>

</body>
</html>