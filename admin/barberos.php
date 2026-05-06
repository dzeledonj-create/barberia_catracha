<?php
session_start();

if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

require_once '../clases/Barbero.php';

$barberos = Barbero::obtenerTodos();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Barberos</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>

<section class="admin-layout">

    <?php include_once 'includes/admin_sidebar.php'; ?>

    <main class="admin-main">

        <section class="admin-header-flex">
            <section>
                <p class="admin-small-title">EQUIPO</p>
                <h1>Gestión de Barberos</h1>
            </section>

            <a href="crear_barbero.php" class="admin-btn">
                + Añadir Barbero
            </a>
        </section>

        <section class="admin-panel-box">

            <table class="admin-table">

                <thead>
                    <tr>
                        <th>Foto</th>
                        <th>Nombre</th>
                        <th>Especialidad</th>
                        <th>Acciones</th>
                    </tr>
                </thead>

                <tbody>

                    <?php foreach ($barberos as $barbero): ?>

                        <tr>

                            <td>
                                <img src="../<?= htmlspecialchars($barbero['foto_url']) ?>" 
                                     class="admin-avatar">
                            </td>

                            <td><?= htmlspecialchars($barbero['nombre']) ?></td>

                            <td><?= htmlspecialchars($barbero['especialidad']) ?></td>

                            <td>
                                <a href="editar_barbero.php?id=<?= $barbero['barbero_id'] ?>">
                                    Editar
                                </a>

                                <a href="eliminar_barbero.php?id=<?= $barbero['barbero_id'] ?>"
                                   onclick="return confirm('¿Eliminar barbero?')">
                                    Eliminar
                                </a>
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