<?php
session_start();

if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
    header("Location: ../../login.php");
    exit;
}

require_once '../../Clases/Reserva.php';

if (isset($_GET['accion'], $_GET['id'])) {
    $id = $_GET['id'];
    $accion = $_GET['accion'];

    if ($accion === 'aceptar') {
        Reserva::cambiarEstado($id, 'confirmada');
    }

    if ($accion === 'cancelar') {
        Reserva::cambiarEstado($id, 'cancelada');
    }

    if ($accion === 'eliminar') {
        Reserva::eliminar($id);
    }

    header("Location: gestionreservas.php");
    exit;
}

$reservas = Reserva::obtenerTodasConDetalles();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Reservas</title>
    <link rel="stylesheet" href="../../assets/style.css">
</head>
<body>

<section class="admin-layout">

    <?php include_once '../includes/admin_sidebar.php'; ?>

    <main class="admin-main">
        <p class="admin-small-title">RESERVAS</p>
        <h1>Gestión de Reservas</h1>

        <section class="admin-panel-box">

            <?php if (empty($reservas)): ?>
                <p>No hay reservas registradas.</p>
            <?php else: ?>

                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Cliente</th>
                            <th>Barbero</th>
                            <th>Servicio</th>
                            <th>Fecha</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php foreach ($reservas as $reserva): ?>
                            <tr>
                                <td><?= htmlspecialchars($reserva['cliente']) ?></td>
                                <td><?= htmlspecialchars($reserva['barbero']) ?></td>
                                <td><?= htmlspecialchars($reserva['servicio']) ?></td>
                                <td><?= htmlspecialchars($reserva['fecha_hora']) ?></td>
                                <td><?= htmlspecialchars($reserva['estado']) ?></td>
                                <td>
                                    <a href="?accion=aceptar&id=<?= $reserva['reserva_id'] ?>">Aceptar</a>
                                    <a href="?accion=cancelar&id=<?= $reserva['reserva_id'] ?>">Cancelar</a>
                                    <a href="?accion=eliminar&id=<?= $reserva['reserva_id'] ?>" onclick="return confirm('¿Eliminar reserva?')">Eliminar</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

            <?php endif; ?>

        </section>
    </main>

</section>

</body>
</html>