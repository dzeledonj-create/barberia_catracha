<?php
require_once '../../clases/Barbero.php';
$barberos = Barbero::obtenerTodos(); // Traemos todos para gestionar activos e inactivos

// Lógica simple para detectar si estamos editando
$editandoId = $_GET['editar'] ?? null;
$barberoAEditar = $editandoId ? Barbero::obtenerPorId($editandoId) : null;
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel Admin - Gestión de Equipo</title>
    <link rel="stylesheet" href="../../assets/style.css"> </head>
<body class="admin-panel">
    <?php include_once '../includes/admin_sidebar.php'; ?>

    <main class="content">
        <header>
            <h1>Gestión del Equipo</h1>
            <p>Edita la información de cada barbero</p>
        </header>

        <section class="equipo-grid">
    <?php foreach ($barberos as $b_data): 
        // Obtenemos el objeto completo con los nuevos campos
        $b = Barbero::obtenerPorId($b_data['barbero_id']);
        $esEste = ($editandoId == $b->barberoId);
    ?>
        <section class="barbero-card">
            <img src="../../<?= htmlspecialchars($b->fotoUrl) ?>" alt="<?= $b->nombre ?>" >

            <?php if ($esEste): ?>
                <form action="procesar_barbero.php" method="POST" class="edit-form">
                    <input type="hidden" name="barbero_id" value="<?= $b->barberoId ?>">
                    
                    <input type="text" name="nombre" value="<?= htmlspecialchars($b->nombre) ?>">
                    <input type="text" name="especialidad" value="<?= htmlspecialchars($b->especialidad) ?>">
                    
                    <textarea name="descripcion" rows="3"><?= htmlspecialchars($b->descripcion) ?></textarea>
                    
                    <input type="text" name="etiquetas" value="<?= htmlspecialchars($b->etiquetas) ?>">
                    <input type="text" name="foto_url" value="<?= htmlspecialchars($b->fotoUrl) ?>">

                    <section class="form-buttons">
                        <button type="submit" class="btn-save">GUARDAR</button>
                        <a href="GestionEquipo.php" class="btn-cancel">CANCELAR</a>
                    </section>
                </form>
            <?php else: ?>
                <section class="info">
                    <h3><?= htmlspecialchars($b->nombre) ?></h3>
                    <p class="rank"><?= htmlspecialchars($b->especialidad) ?></p>
                    <a href="?editar=<?= $b->barberoId ?>" class="btn-edit">EDITAR</a>
                </section>
            <?php endif; ?>
        </section>
    <?php endforeach; ?>
</section>
    </main>
</body>
</html>