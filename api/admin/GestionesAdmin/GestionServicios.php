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

    header("Location: /barberia_catracha/api/admin/GestionesAdmin/GestionServicios.php");
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

    header("Location: /barberia_catracha/api/admin/GestionesAdmin/GestionServicios.php");
    exit;
}

/* ELIMINAR SERVICIO */
if (isset($_GET['eliminar'])) {
    $servicio = Servicio::obtenerPorId($_GET['eliminar']);

    if ($servicio) {
        $servicio->eliminar();
    }

    header("Location: /barberia_catracha/api/admin/GestionesAdmin/GestionServicios.php");
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

            <h2>
                <?= $servicioEditar ? 'Editar servicio' : 'Añadir servicio' ?>
            </h2>

            <form method="POST" class="admin-form-servicios">

                <?php if ($servicioEditar): ?>
                    <input type="hidden" name="servicio_id" value="<?= $servicioEditar->getServicioId() ?>">
                <?php endif; ?>

                <input 
                    type="text" 
                    name="nombre" 
                    placeholder="Nombre del servicio"
                    value="<?= $servicioEditar ? htmlspecialchars($servicioEditar->getNombre()) : '' ?>"
                    required
                >

                <input 
                    type="text" 
                    name="descripcion" 
                    placeholder="Descripción"
                    value="<?= $servicioEditar ? htmlspecialchars($servicioEditar->getDescripcion()) : '' ?>"
                >

                <input 
                    type="number" 
                    step="0.01" 
                    name="precio" 
                    placeholder="Precio"
                    value="<?= $servicioEditar ? htmlspecialchars($servicioEditar->getPrecio()) : '' ?>"
                    required
                >

                <input 
                    type="number" 
                    name="duracion_minutos" 
                    placeholder="Duración en minutos"
                    value="<?= $servicioEditar ? htmlspecialchars($servicioEditar->getDuracionMinutos()) : '' ?>"
                    required
                >

                <input 
                    type="text" 
                    name="categoria" 
                    placeholder="Categoría"
                    value="<?= $servicioEditar ? htmlspecialchars($servicioEditar->getCategoria()) : '' ?>"
                    required
                >

                <?php if ($servicioEditar): ?>
                    <button type="submit" name="editar">Guardar cambios</button>
                    <a href="/barberia_catracha/api/admin/GestionesAdmin/GestionServicios.php" class="admin-btn-cancelar">Cancelar</a>
                <?php else: ?>
                    <button type="submit" name="crear">Añadir servicio</button>
                <?php endif; ?>

            </form>

        </section>

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