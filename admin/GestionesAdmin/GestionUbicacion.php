<?php

require_once '../../Clases/DatosUbicacion.php';
require_once '../../Clases/Horario.php';
require_once '../clases_admin/GestorUsuarios.php';

$usuario = GestorUsuarios::obtenerDesdeSesion();
if (!$usuario instanceof Administrador) {
    header("Location: ../../login.php");
    exit;
}

if (isset($_POST['guardar'])) {

    DatosUbicacion::actualizar(
        $_POST['direccion'],
        $_POST['telefono'],
        $_POST['whatsapp'],
        $_POST['mapa_embed']
    );

    foreach ($_POST['horarios'] as $horarioId => $datos) {
    Horario::actualizar(
        $horarioId,
        $datos['hora_apertura'] ?: null,
        $datos['hora_cierre'] ?: null,
        isset($datos['cerrado'])
    );
}

    header("Location: GestionUbicacion.php");
    exit;
}

$datosUbicacion = DatosUbicacion::obtener();
$horarios = Horario::obtenerTodos();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión Ubicación</title>
    <link rel="stylesheet" href="../../assets/style.css">
</head>
<body>

<section class="admin-layout">

    <?php include_once '../includes/admin_sidebar.php'; ?>

    <main class="admin-main">

        <p class="admin-small-title">UBICACIÓN</p>
        <h1>Horario & Ubicación</h1>
        <p class="admin-subtitle">Actualiza los datos de contacto y horario.</p>

        <form method="POST">

            <section class="admin-panel-box gestion-ubicacion-box">

                <h2>Información de contacto</h2>

                <label>Dirección</label>
                <input type="text" name="direccion" value="<?= htmlspecialchars($datosUbicacion['direccion']) ?>" required>

                <section class="ubicacion-form-grid">
                    <section>
                        <label>Teléfono</label>
                        <input type="text" name="telefono" value="<?= htmlspecialchars($datosUbicacion['telefono']) ?>">
                    </section>

                    <section>
                        <label>WhatsApp</label>
                        <input type="text" name="whatsapp" value="<?= htmlspecialchars($datosUbicacion['whatsapp']) ?>">
                    </section>
                </section>

                <label>URL Google Maps Embed</label>
                <input type="text" name="mapa_embed" value="<?= htmlspecialchars($datosUbicacion['mapa_embed']) ?>">

            </section>

            <section class="admin-panel-box gestion-ubicacion-box">

                <h2>Horario semanal</h2>

                <?php foreach ($horarios as $horario): ?>
                    <section class="horario-admin-row">

                        <span><?= htmlspecialchars($horario['dia_semana']) ?></span>

                        <input 
                            type="time" 
                            name="horarios[<?= $horario['horario_id'] ?>][hora_apertura]"
                            value="<?= htmlspecialchars($horario['hora_apertura']) ?>"
                        >

                        <input 
                            type="time" 
                            name="horarios[<?= $horario['horario_id'] ?>][hora_cierre]"
                            value="<?= htmlspecialchars($horario['hora_cierre']) ?>"
                        >

                        <label class="admin-check">
                            <input 
                                type="checkbox" 
                                name="horarios[<?= $horario['horario_id'] ?>][cerrado]"
                                <?= !empty($horario['cerrado']) ? 'checked' : '' ?>
                            >
                            Cerrado
                        </label>

                    </section>
                <?php endforeach; ?>

            </section>

            <button type="submit" name="guardar" class="admin-btn">
                Guardar cambios
            </button>

        </form>

    </main>

</section>

</body>
</html>