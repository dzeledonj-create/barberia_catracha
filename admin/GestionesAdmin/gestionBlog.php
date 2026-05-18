<?php
session_start();

if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
    header("Location: ../../login.php");
    exit;
}

require_once '../../Clases/BlogPost.php';

/* CREAR */
if (isset($_POST['crear'])) {

    $embed = "";

    if (!empty($_POST['instagram_url'])) {
        $url = trim($_POST['instagram_url']);

        if (strpos($url, '/reel/') !== false) {
            $embed = '
                <blockquote class="instagram-media"
                data-instgrm-permalink="' . $url . '"
                data-instgrm-version="14">
                </blockquote>
                ';
        } else {
            $embed = '
                <blockquote class="instagram-media"
                data-instgrm-permalink="' . $url . '"
                data-instgrm-version="14">
                </blockquote>
                ';
        }
    }

    $post = new BlogPost(
        $_POST['titulo'],
        $_POST['contenido'],
        1,
        null,
        $_POST['etiquetas'],
        null,
        null,
        $embed
    );

    $post->guardar();

    header("Location: GestionBlog.php");
    exit;
}

/* EDITAR */
if (isset($_POST['editar'])) {

    $embed = $_POST['instagram_embed_actual'];

    if (!empty($_POST['instagram_url'])) {
        $url = trim($_POST['instagram_url']);
        $embed = '
        <blockquote class="instagram-media"
        data-instgrm-permalink="' . $url . '"
        data-instgrm-version="14">
        </blockquote>
        ';
    }

    $post = new BlogPost(
        $_POST['titulo'],
        $_POST['contenido'],
        1,
        null,
        $_POST['etiquetas'],
        $_POST['post_id'],
        null,
        $embed
    );

    $post->guardar();

    header("Location: GestionBlog.php");
    exit;
}

/* ELIMINAR */
if (isset($_GET['eliminar'])) {
    $post = BlogPost::obtenerPorId($_GET['eliminar']);

    if ($post) {
        $post->eliminar();
    }

    header("Location: GestionBlog.php");
    exit;
}

/* EDITAR */
$postEditar = null;

if (isset($_GET['editar'])) {
    $postEditar = BlogPost::obtenerPorId($_GET['editar']);
}

$posts = BlogPost::obtenerTodos();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión Blog</title>
    <link rel="stylesheet" href="../../assets/style.css">
</head>
<body>

<section class="admin-layout">

    <?php include_once '../includes/admin_sidebar.php'; ?>

    <main class="admin-main">

        <p class="admin-small-title">BLOG</p>
        <h1>Gestión del Blog</h1>
        <p class="admin-subtitle">Añade, edita o elimina publicaciones y reels de Instagram.</p>

        <section class="admin-panel-box">

            <h2><?= $postEditar ? 'Editar publicación' : 'Nueva publicación' ?></h2>

            <form method="POST" class="admin-form-blog">

                <?php if ($postEditar): ?>
                    <input type="hidden" name="post_id" value="<?= $postEditar->postId ?>">
                    <input type="hidden" name="instagram_embed_actual" value="<?= htmlspecialchars($postEditar->instagramEmbed) ?>">
                <?php endif; ?>

                <input type="text"
                       name="titulo"
                       placeholder="Título"
                       value="<?= $postEditar ? htmlspecialchars($postEditar->titulo) : '' ?>"
                       required>

                <input type="text"
                       name="etiquetas"
                       placeholder="Etiqueta"
                       value="<?= $postEditar ? htmlspecialchars($postEditar->etiquetas) : '' ?>">

                <input type="text"
                       name="instagram_url"
                       placeholder="URL del reel de Instagram">

                <textarea name="contenido"
                          placeholder="Contenido"
                          required><?= $postEditar ? htmlspecialchars($postEditar->contenido) : '' ?></textarea>

                <button type="submit" name="<?= $postEditar ? 'editar' : 'crear' ?>">
                    <?= $postEditar ? 'Guardar cambios' : 'Añadir reel' ?>
                </button>

                <?php if ($postEditar): ?>
                    <a href="GestionBlog.php" class="admin-btn-cancelar">Cancelar</a>
                <?php endif; ?>

            </form>

        </section>

        <section class="blog-admin-grid">

            <?php foreach ($posts as $post): ?>

                <article class="blog-admin-card">

                    <section class="blog-admin-media">

                    <?php if (!empty($post['instagram_embed'])): ?>

                        <div class="instagram-admin-preview">
                            <?= $post['instagram_embed'] ?>
                        </div>

                    <?php else: ?>

                        <section class="blog-admin-placeholder">
                            Sin reel
                        </section>

                    <?php endif; ?>

                </section>

                    <section class="blog-admin-info">

                        <span><?= htmlspecialchars($post['etiquetas'] ?? 'BLOG') ?></span>

                        <h3><?= htmlspecialchars($post['titulo']) ?></h3>

                        <p><?= htmlspecialchars(substr($post['contenido'], 0, 100)) ?>...</p>

                        <section class="admin-actions-mini">
                            <a href="?editar=<?= $post['post_id'] ?>">Editar</a>

                            <a href="?eliminar=<?= $post['post_id'] ?>"
                               onclick="return confirm('¿Eliminar publicación?')">
                                Eliminar
                            </a>
                        </section>

                    </section>

                </article>

            <?php endforeach; ?>

        </section>

    </main>

</section>
<script async src="//www.instagram.com/embed.js"></script>
</body>
</html>