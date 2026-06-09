<?php
require_once __DIR__ . '/../../Clases/Servicio.php';
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



/* CREAR SERVICIO */
if (isset($_POST['crear'])) {
    $servicio = new Servicio(
        $_POST['nombre'],
        $_POST['descripcion'],
        $_POST['precio'],
        $_POST['duracion_minutos'],
        null,
        $_POST['categoria']
    );

    $servicio->guardar();

    header("Location: " . BASE_PATH . "/api/admin/GestionesAdmin/GestionServicios.php");
    exit;
}

/* EDITAR SERVICIO */
if (isset($_POST['editar'])) {
    $servicio = new Servicio(
        $_POST['nombre'],
        $_POST['descripcion'],
        $_POST['precio'],
        $_POST['duracion_minutos'],
        $_POST['servicio_id'],
       $_POST['categoria']
    );

    $servicio->guardar();

    header("Location: " . BASE_PATH . "/api/admin/GestionesAdmin/GestionServicios.php");
    exit;
}

/* ELIMINAR SERVICIO */
if (isset($_GET['eliminar'])) {
    $servicio = Servicio::obtenerPorId($_GET['eliminar']);

    if ($servicio) {
        $servicio->eliminar();
    }

    header("Location: " . BASE_PATH . "/api/admin/GestionesAdmin/GestionServicios.php");
    exit;
}

/* OBTENER SERVICIOS */
$servicios = Servicio::obtenerTodos();

/* SI SE VA A EDITAR */
$servicioEditar = null;

// Si se ha pasado un ID de servicio para editar, obtener ese servicio de la base de datos
if (isset($_GET['editar'])) {
    $servicioEditar = Servicio::obtenerPorId($_GET['editar']);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Gestión de Servicios</title>
    <link rel="stylesheet" href="../../../assets/style.css">
</head>
<body>

<section class="admin-layout">

    <?php include_once __DIR__ . '/../includes/admin_sidebar.php'; ?>

    <main class="admin-main">

        <p class="admin-small-title">SERVICIOS</p>
        <h1>Gestión de Servicios</h1>
        <p class="admin-subtitle">Añade, edita o elimina servicios de la barbería.</p>

        <section class="admin-panel-box">

            <h2>Añadir servicio</h2>

            <form method="POST" class="admin-form-servicios">

                <input type="text" name="nombre" placeholder="Nombre del servicio" required>
                <input type="text" name="descripcion" placeholder="Descripción">
                <input type="number" step="0.01" name="precio" placeholder="Precio" required>
                <input type="number" name="duracion_minutos" placeholder="Duración en minutos" required>
                <input type="text" name="categoria" placeholder="Categoría" required>
                <button type="submit" name="crear">Añadir servicio</button>

            </form>

        </section>

        <?php if ($servicioEditar): ?>
        <div class="modal-overlay editar-modal-overlay">
            <div class="modal-box">
                <div class="modal-header">
                    <h2>Editar servicio</h2>
                    <a href="<?= BASE_PATH ?>/api/admin/GestionesAdmin/GestionServicios.php" class="modal-close" aria-label="Cerrar">&times;</a>
                </div>
                <form method="POST" class="edit-form">
                    <input type="hidden" name="servicio_id" value="<?= $servicioEditar->getServicioId() ?>">

                    <label>Nombre</label>
                    <input type="text" name="nombre" value="<?= htmlspecialchars($servicioEditar->getNombre()) ?>" required>

                    <label>Descripción</label>
                    <input type="text" name="descripcion" value="<?= htmlspecialchars($servicioEditar->getDescripcion()) ?>">

                    <label>Precio (€)</label>
                    <input type="number" step="0.01" name="precio" value="<?= htmlspecialchars($servicioEditar->getPrecio()) ?>" required>

                    <label>Duración (minutos)</label>
                    <input type="number" name="duracion_minutos" value="<?= htmlspecialchars($servicioEditar->getDuracionMinutos()) ?>" required>

                    <label>Categoría</label>
                    <input type="text" name="categoria" value="<?= htmlspecialchars($servicioEditar->getCategoria()) ?>" required>

                    <section class="form-buttons">
                        <button type="submit" name="editar" class="btn-save">GUARDAR</button>
                        <a href="<?= BASE_PATH ?>/api/admin/GestionesAdmin/GestionServicios.php" class="btn-cancel">CANCELAR</a>
                    </section>
                </form>
            </div>
        </div>
        <?php endif; ?>

        <section class="admin-panel-box">

            <h2>Servicios registrados</h2>

            <div class="table-responsive">
            <table class="admin-table-servicios" style="width: 100%; table-layout: auto;">
                <thead>
                    <tr>
                        <th style="width: 15%;">Nombre</th>
                        <th style="width: 30%;">Descripción</th>
                        <th style="width: 10%;">Precio</th>
                        <th style="width: 12%;">Duración</th>
                        <th style="width: 15%;">Categoría</th>
                        <th style="width: 18%;">Acciones</th>
                    </tr>
                </thead>

                <tbody>
                    <?php foreach ($servicios as $servicio): ?>
                        <tr>
                            <td style="word-break: break-word;"><?= htmlspecialchars($servicio->getNombre()) ?></td>
                            <td style="word-break: break-word; max-width: 300px;"><?= htmlspecialchars($servicio->getDescripcion()) ?></td>
                            <td style="text-align: center;"><?= htmlspecialchars($servicio->getPrecio()) ?> €</td>
                            <td style="text-align: center;"><?= htmlspecialchars($servicio->getDuracionMinutos()) ?> min</td>
                            <td style="text-align: center;"><?= htmlspecialchars($servicio->getCategoria()) ?></td>
                            <td class="admin-actions-mini" style="text-align: center;">
                                <a href="?editar=<?= $servicio->getServicioId() ?>">✎</a>
                                <a href="?eliminar=<?= $servicio->getServicioId() ?>" onclick="return confirm('¿Eliminar servicio?')">🗑</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                </tbody>
            </table>
            </div>

        </section>

    </main>

</section>

</body>
</html>