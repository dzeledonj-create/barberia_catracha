<?php
require_once __DIR__ . '/../../Clases/BlogPost.php';
require_once __DIR__ . '/../clases_admin/GestorUsuarios.php';
ini_set('session.save_path', sys_get_temp_dir());
session_start();

$usuario = GestorUsuarios::obtenerDesdeSesion();
if (!$usuario) {
    header("Location: ../login.php");
    exit;
}
if (!($usuario instanceof Administrador)) {
    header("Location: GestionReservas.php");
    exit;
}

/* CREAR */
if (isset($_POST['crear'])) {

    $embed = "";

    // Si se ha proporcionado una URL de Instagram, generar el código de inserción correspondiente
    if (!empty($_POST['instagram_url'])) {
        $url = trim($_POST['instagram_url']);

        // Verificar si la URL es de un reel o una publicación normal para generar el código de inserción adecuado
        
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

    // Crear una nueva instancia de BlogPost con los datos del formulario y el código de inserción generado
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

    header("Location: " . BASE_PATH . "/api/admin/GestionesAdmin/GestionBlog.php");
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

    header("Location: " . BASE_PATH . "/api/admin/GestionesAdmin/GestionBlog.php");
    exit;
}

/* ELIMINAR */
if (isset($_GET['eliminar'])) {
    $post = BlogPost::obtenerPorId($_GET['eliminar']);

    if ($post) {
        $post->eliminar();
    }

    header("Location: " . BASE_PATH . "/api/admin/GestionesAdmin/GestionBlog.php");
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
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Gestión Blog</title>
    <link rel="stylesheet" href="../../../assets/style.css">
</head>
<body>

<section class="admin-layout">

    <?php include_once __DIR__ . '/../includes/admin_sidebar.php'; ?>

    <main class="admin-main">

        <p class="admin-small-title">BLOG</p>
        <h1>Gestión del Blog</h1>
        <p class="admin-subtitle">Añade, edita o elimina publicaciones y reels de Instagram.</p>

        <section class="admin-panel-box">

            <h2>Nueva publicación</h2>

            <form method="POST" class="admin-form-blog">
                <input type="text" name="titulo" placeholder="Título" required>
                <input type="text" name="etiquetas" placeholder="Etiqueta">
                <input type="text" name="instagram_url" placeholder="URL del reel de Instagram">
                <textarea name="contenido" placeholder="Contenido" required></textarea>
                <button type="submit" name="crear">Añadir reel</button>
            </form>

        </section>

        <?php if ($postEditar): ?>
        <div class="modal-overlay editar-modal-overlay">
            <div class="modal-box">
                <div class="modal-header">
                    <h2>Editar publicación</h2>
                    <a href="<?= BASE_PATH ?>/api/admin/GestionesAdmin/GestionBlog.php" class="modal-close" aria-label="Cerrar">&times;</a>
                </div>
                <form method="POST" class="edit-form">
                    <input type="hidden" name="post_id" value="<?= $postEditar->getPostId() ?>">
                    <input type="hidden" name="instagram_embed_actual" value="<?= htmlspecialchars($postEditar->getInstagramEmbed()) ?>">

                    <label>Título</label>
                    <input type="text" name="titulo" value="<?= htmlspecialchars($postEditar->getTitulo()) ?>" required>

                    <label>Etiqueta</label>
                    <input type="text" name="etiquetas" value="<?= htmlspecialchars($postEditar->getEtiquetas()) ?>">

                    <label>Nueva URL de reel (opcional, reemplaza el actual)</label>
                    <input type="text" name="instagram_url" placeholder="URL del reel de Instagram">

                    <label>Contenido</label>
                    <textarea name="contenido" required><?= htmlspecialchars($postEditar->getContenido()) ?></textarea>

                    <section class="form-buttons">
                        <button type="submit" name="editar" class="btn-save">GUARDAR</button>
                        <a href="<?= BASE_PATH ?>/api/admin/GestionesAdmin/GestionBlog.php" class="btn-cancel">CANCELAR</a>
                    </section>
                </form>
            </div>
        </div>
        <?php endif; ?>

        <section class="blog-admin-grid">

            <?php foreach ($posts as $post): ?>

                <article class="blog-admin-card">

                    <section class="blog-admin-media">

                    <?php if (!empty($post->getInstagramEmbed())): ?>

                        <div class="instagram-admin-preview">
                            <?= $post->getInstagramEmbed() ?>
                        </div>

                    <?php else: ?>

                        <section class="blog-admin-placeholder">
                            Sin reel
                        </section>

                    <?php endif; ?>

                </section>

                    <section class="blog-admin-info">

                        <span><?= htmlspecialchars($post->getEtiquetas() ?? 'BLOG') ?></span>

                        <h3><?= htmlspecialchars($post->getTitulo()) ?></h3>

                        <p><?= htmlspecialchars(substr($post->getContenido(), 0, 100)) ?>...</p>

                        <section class="admin-actions-mini">
                            <a href="?editar=<?= $post->getPostId() ?>">Editar</a>

                            <a href="?eliminar=<?= $post->getPostId() ?>"
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