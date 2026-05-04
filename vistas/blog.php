<?php
require_once '../clases/BlogPost.php';
require_once '../clases/Barbero.php';

// Obtener todos los posts de la base de datos
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
    <section class="blog-hero">
        <div class="container-texto">
            <p class="subtitle">TENDENCIAS Y CONSEJOS</p>
            <h1 class="titulo-principal">NUESTRO <span class="text-gold">BLOG</span></h1>
            <div class="underline"></div>
            <p class="descripcion-header">
                Descubre los mejores consejos de cuidado personal, tendencias en cortes y noticias de Barbería Catracha.
            </p>
        </div>
    </section>

    <section class="blog-container container">
        <?php if (empty($posts)): ?>
            <div class="no-data-msg">
                <p>Próximamente publicaremos contenido interesante para ti.</p>
            </div>
        <?php else: ?>
            <div class="blog-grid">
                <?php foreach ($posts as $post): 
                    // Creamos una instancia para usar los métodos auxiliares como resumen()
                    $blogObj = new BlogPost(
                        $post['titulo'], 
                        $post['contenido'], 
                        $post['autor_id'], 
                        $post['imagen_url'], 
                        $post['etiquetas'], 
                        $post['post_id'], 
                        $post['fecha_publicacion']
                    );
                ?>
                    <article class="blog-card">
                        <div class="blog-img">
                            <?php if ($blogObj->tieneImagen()): ?>
                                <img src="../<?= htmlspecialchars($post['imagen_url']) ?>" alt="<?= htmlspecialchars($post['titulo']) ?>">
                            <?php else: ?>
                                <img src="../assets/img/default-blog.jpg" alt="Barbería Catracha">
                            <?php endif; ?>
                            
                            <div class="blog-date">
                                <i class="far fa-calendar-alt"></i> 
                                <?= date('d M, Y', strtotime($post['fecha_publicacion'])) ?>
                            </div>
                        </div>
                        
                        <div class="blog-content">
                            <div class="blog-meta">
                                <span class="author"><i class="fas fa-user-edit"></i> <?= htmlspecialchars($post['nombre_autor'] ?? 'Barbero') ?></span>
                                <div class="tags-container">
                                    <?php 
                                    $tags = $blogObj->obtenerEtiquetas();
                                    foreach ($tags as $tag): ?>
                                        <span class="tag">#<?= htmlspecialchars($tag) ?></span>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                            
                            <h3><?= htmlspecialchars($post['titulo']) ?></h3>
                            <p><?= htmlspecialchars($blogObj->resumen(120)) ?></p>
                            
                            <a href="post_detalle.php?id=<?= $post['post_id'] ?>" class="btn-leer-mas">
                                LEER ARTÍCULO <i class="fas fa-arrow-right"></i>
                            </a>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </section>
</main>

<?php include_once '../includes/footer.php'; ?>

</body>
</html>