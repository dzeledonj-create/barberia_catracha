<?php
require_once __DIR__ . '/../../Clases/MuralSugerencia.php';
require_once __DIR__ . '/../clases_admin/GestorUsuarios.php';
ini_set('session.save_path', sys_get_temp_dir());
session_start();

$usuario = GestorUsuarios::obtenerDesdeSesion();
if (!$usuario) {
    header("Location: ../login.php");
    exit;
}


/* CREAR */
if (isset($_POST['crear'])) {

    $imagenUrl = "";

    // Si se ha proporcionado una imagen, procesarla y obtener la URL para guardarla en la base de datos
    if (!empty($_FILES['imagen']['name'])) {
        // Generar un nombre único para la imagen para evitar conflictos
        $nombreImagen = time() . "_" . $_FILES['imagen']['name'];
        // Mover la imagen al directorio de galería y obtener la URL para guardarla en la base de datos
        $rutaDestino = "../../../assets/img/mural/" . $nombreImagen;
        // Mover el archivo subido a la ubicación deseada
        move_uploaded_file(
            $_FILES['imagen']['tmp_name'],
            $rutaDestino
        );
        // Guardar la URL relativa de la imagen para almacenarla en la base de datos
        $imagenUrl = "assets/img/mural/" . $nombreImagen;
    }

    // Crear una nueva instancia de MuralSugerencia con los datos del formulario y la URL de la imagen procesada
    $sugerencia = new MuralSugerencia(
        $imagenUrl,
        $_POST['nombre_corte'],
        $_POST['descripcion'],
        $_POST['estilo'],
        isset($_POST['activo'])
    );

    $sugerencia->guardar();

    header("Location: GestionGaleria.php");
    exit;
}

/* EDITAR */
if (isset($_POST['editar'])) {

    $imagenUrl = $_POST['imagen_actual'];

    if (!empty($_FILES['imagen']['name'])) {

        $nombreImagen = time() . "_" . $_FILES['imagen']['name'];

        $rutaDestino = "../../../assets/img/mural/" . $nombreImagen;

        move_uploaded_file(
            $_FILES['imagen']['tmp_name'],
            $rutaDestino
        );

        $imagenUrl = "assets/img/mural/" . $nombreImagen;
    }

    $sugerencia = new MuralSugerencia(
        $imagenUrl,
        $_POST['nombre_corte'],
        $_POST['descripcion'],
        $_POST['estilo'],
        isset($_POST['activo']),
        $_POST['sugerencia_id']
    );

    $sugerencia->guardar();

    header("Location: GestionGaleria.php");
    exit;
}

/* ELIMINAR */
if (isset($_GET['eliminar'])) {

    $sugerencia = MuralSugerencia::obtenerPorId($_GET['eliminar']);

    if ($sugerencia) {
        $sugerencia->eliminar();
    }

    header("Location: GestionGaleria.php");
    exit;
}

/* EDITAR */
$sugerenciaEditar = null;

if (isset($_GET['editar'])) {
    $sugerenciaEditar = MuralSugerencia::obtenerPorId($_GET['editar']);
}

/* OBTENER TODAS */
$sugerencias = MuralSugerencia::obtenerTodos();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Gestión Galería</title>

    <link rel="stylesheet" href="../../../assets/style.css">
</head>

<body>

<section class="admin-layout">

    <?php include_once __DIR__ . '/../includes/admin_sidebar.php'; ?>

    <main class="admin-main">

        <p class="admin-small-title">GALERÍA</p>

        <h1>Galería de Trabajos</h1>

        <p class="admin-subtitle">
            <?= count($sugerencias) ?> imágenes publicadas
        </p>

        <section class="admin-panel-box">

            <form method="POST"
                  enctype="multipart/form-data"
                  class="admin-form-galeria">

                <?php if ($sugerenciaEditar): ?>

                    <input type="hidden"
                           name="sugerencia_id"
                           value="<?= $sugerenciaEditar->getSugerenciaId() ?>">

                    <input type="hidden"
                           name="imagen_actual"
                           value="<?= htmlspecialchars($sugerenciaEditar->getImagenUrl()) ?>">

                <?php endif; ?>

                <input type="text"
                       name="nombre_corte"
                       placeholder="Título del corte"
                       value="<?= $sugerenciaEditar ? htmlspecialchars($sugerenciaEditar->getNombreCorte()) : '' ?>"
                       required>

                <input type="text"
                       name="descripcion"
                       placeholder="Descripción"
                       value="<?= $sugerenciaEditar ? htmlspecialchars($sugerenciaEditar->getDescripcion()) : '' ?>">

                <select name="estilo">

                    <option value="Fade"
                        <?= ($sugerenciaEditar && $sugerenciaEditar->getEstilo() === 'Fade') ? 'selected' : '' ?>>
                        Fades
                    </option>

                    <option value="Barba"
                        <?= ($sugerenciaEditar && $sugerenciaEditar->getEstilo() === 'Barba') ? 'selected' : '' ?>>
                        Barba
                    </option>

                    <option value="Diseño"
                        <?= ($sugerenciaEditar && $sugerenciaEditar->getEstilo() === 'Diseño') ? 'selected' : '' ?>>
                        Diseños
                    </option>

                    <option value="Tinte"
                        <?= ($sugerenciaEditar && $sugerenciaEditar->getEstilo() === 'Tinte') ? 'selected' : '' ?>>
                        Tinte
                    </option>

                </select>

                <input type="file" name="imagen">

                <label class="admin-check">

                    <input type="checkbox"
                           name="activo"
                        <?= (!$sugerenciaEditar || $sugerenciaEditar->getActivo()) ? 'checked' : '' ?>>

                    Visible

                </label>

                <button type="submit"
                        name="<?= $sugerenciaEditar ? 'editar' : 'crear' ?>">

                    <?= $sugerenciaEditar ? 'Guardar cambios' : 'Añadir imagen' ?>

                </button>

            </form>

        </section>

        <section class="galeria-admin-grid">

            <?php foreach ($sugerencias as $sugerencia): ?>

                <article class="galeria-admin-card">

                    <img src="<?= htmlspecialchars($sugerencia->getImagenUrl()) ?>" alt="<?= htmlspecialchars($sugerencia->getNombreCorte()) ?>">

                    <section class="galeria-admin-info">

                        <h3>
                            <?= htmlspecialchars($sugerencia->getNombreCorte()) ?>
                        </h3>

                        <p>
                            <?= htmlspecialchars($sugerencia->getEstilo()) ?>
                        </p>

                        <section class="admin-actions-mini">

                            <a href="?editar=<?= $sugerencia->getSugerenciaId() ?>">
                                Editar
                            </a>

                            <a href="?eliminar=<?= $sugerencia->getSugerenciaId() ?>"
                               onclick="return confirm('¿Eliminar imagen?')">

                                Eliminar

                            </a>

                        </section>

                    </section>

                </article>

            <?php endforeach; ?>

        </section>

    </main>

</section>

</body>
</html>