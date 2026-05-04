<?php
require_once '../clases/BlogPost.php';
require_once '../clases/Barbero.php';

$posts = BlogPost::obtenerTodos();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blog - Barbería Catracha</title>
    <link rel="stylesheet" href="../assets/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-dark">

<?php include_once '../includes/header.php'; ?>

<main class="blog-page">
    <header class="blog-hero">
        <section class="container-texto">
            <p class="subtitle">TENDENCIAS Y CONSEJOS</p>
            <h1 class="titulo-principal">NUESTRO <span class="text-gold">BLOG</span></h1>
            <span class="underline"></span>
            <p class="descripcion-header">Los mejores consejos de cuidado personal y estilos en Zaragoza.</p>
        </section>
    </header>

    <section class="blog-container container">
        <?php if (empty($posts)): ?>
            <section class="no-data-msg">
                <i class="fas fa-feather-alt"></i>
                <p class="alerta-visible">Próximamente publicaremos contenido interesante para ti.</p>
            </section>
        <?php else: ?>
            <section class="blog-grid">
                <?php foreach ($posts as $post): 
                    $blogObj = new BlogPost($post['titulo'], $post['contenido'], $post['autor_id'], $post['imagen_url'], $post['etiquetas'], $post['post_id'], $post['fecha_publicacion']);
                ?>
                    <article class="blog-card">
                        <figure class="blog-img">
                            <img src="<?= htmlspecialchars($post['imagen_url']) ?>" alt="<?= htmlspecialchars($post['titulo']) ?>">
                            <time class="blog-date">
                                <i class="far fa-calendar-alt"></i> <?= date('d M', strtotime($post['fecha_publicacion'])) ?>
                            </time>
                        </figure>
                        
                        <section class="blog-content">
                            <header class="blog-meta">
                                <span class="author"><i class="fas fa-user-edit"></i> <?= htmlspecialchars($post['nombre_autor'] ?? 'Barbero') ?></span>
                                <nav class="tags-container">
                                    <?php foreach ($blogObj->obtenerEtiquetas() as $tag): ?>
                                        <span class="tag">#<?= htmlspecialchars($tag) ?></span>
                                    <?php endforeach; ?>
                                </nav>
                            </header>
                            
                            <h3><?= htmlspecialchars($post['titulo']) ?></h3>
                            <p><?= htmlspecialchars($blogObj->resumen(100)) ?></p>
                            
                            <footer class="blog-footer">
                                <a href="post_detalle.php?id=<?= $post['post_id'] ?>" class="btn-leer-mas">
                                    LEER MÁS <i class="fas fa-arrow-right"></i>
                                </a>
                            </footer>
                        </section>
                    </article>
                <?php endforeach; ?>
            </section>
        <?php endif; ?>
    </section>
</main>

<?php include_once '../includes/footer.php'; ?>
</body>
</html>