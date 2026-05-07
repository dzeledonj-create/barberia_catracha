<?php
session_start();

if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
    header("Location: ../../login.php");
    exit;
}

require_once '../../Clases/Servicio.php';

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

    header("Location: gestionservicios.php");
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

    header("Location: gestionservicios.php");
    exit;
}

/* ELIMINAR SERVICIO */
if (isset($_GET['eliminar'])) {
    $servicio = Servicio::obtenerPorId($_GET['eliminar']);

    if ($servicio) {
        $servicio->eliminar();
    }

    header("Location: gestionservicios.php");
    exit;
}

/* OBTENER SERVICIOS */
$servicios = Servicio::obtenerTodos();

/* SI SE VA A EDITAR */
$servicioEditar = null;

if (isset($_GET['editar'])) {
    $servicioEditar = Servicio::obtenerPorId($_GET['editar']);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Servicios</title>
    <link rel="stylesheet" href="../../assets/style.css">
</head>
<body>

<section class="admin-layout">

    <?php include_once '../includes/admin_sidebar.php'; ?>

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
                    <input type="hidden" name="servicio_id" value="<?= $servicioEditar->servicioId ?>">
                <?php endif; ?>

                <input 
                    type="text" 
                    name="nombre" 
                    placeholder="Nombre del servicio"
                    value="<?= $servicioEditar ? htmlspecialchars($servicioEditar->nombre) : '' ?>"
                    required
                >

                <input 
                    type="text" 
                    name="descripcion" 
                    placeholder="Descripción"
                    value="<?= $servicioEditar ? htmlspecialchars($servicioEditar->descripcion) : '' ?>"
                >

                <input 
                    type="number" 
                    step="0.01" 
                    name="precio" 
                    placeholder="Precio"
                    value="<?= $servicioEditar ? htmlspecialchars($servicioEditar->precio) : '' ?>"
                    required
                >

                <input 
                    type="number" 
                    name="duracion_minutos" 
                    placeholder="Duración en minutos"
                    value="<?= $servicioEditar ? htmlspecialchars($servicioEditar->duracionMinutos) : '' ?>"
                    required
                >

                <input 
                    type="text" 
                    name="categoria" 
                    placeholder="Categoría"
                    value="<?= $servicioEditar ? htmlspecialchars($servicioEditar->categoria) : '' ?>"
                    required
                >

                <?php if ($servicioEditar): ?>
                    <button type="submit" name="editar">Guardar cambios</button>
                    <a href="gestionservicios.php" class="admin-btn-cancelar">Cancelar</a>
                <?php else: ?>
                    <button type="submit" name="crear">Añadir servicio</button>
                <?php endif; ?>

            </form>

        </section>

        <section class="admin-panel-box">

            <h2>Servicios registrados</h2>

            <table class="admin-table-servicios">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Descripción</th>
                        <th>Precio</th>
                        <th>Duración</th>
                        <th>Categoría</th>
                        <th>Acciones</th>
                    </tr>
                </thead>

                <tbody>
                    <?php foreach ($servicios as $servicio): ?>
                        <tr>
                            <td><?= htmlspecialchars($servicio['nombre']) ?></td>
                            <td><?= htmlspecialchars($servicio['descripcion']) ?></td>
                            <td><?= htmlspecialchars($servicio['precio']) ?> €</td>
                            <td><?= htmlspecialchars($servicio['duracion_minutos']) ?> min</td>
                            <td><?= htmlspecialchars($servicio['categoria']) ?></td>
                            <td>
                                <td class="admin-actions-mini">
    <a href="?editar=<?= $servicio['servicio_id'] ?>">✎</a>
    <a href="?eliminar=<?= $servicio['servicio_id'] ?>" onclick="return confirm('¿Eliminar servicio?')">🗑</a>
</td>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                </tbody>
            </table>

        </section>

    </main>

</section>

</body>
</html>