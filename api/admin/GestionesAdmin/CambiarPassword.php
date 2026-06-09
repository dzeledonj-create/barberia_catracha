<?php
require_once __DIR__ . '/../../Clases/BD.php';
require_once __DIR__ . '/../clases_admin/GestorUsuarios.php';
ini_set('session.save_path', sys_get_temp_dir());
session_start();

$usuario = GestorUsuarios::obtenerDesdeSesion();
if (!$usuario) {
    header("Location: ../login.php");
    exit;
}

$mensajeError = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $actual    = $_POST['password_actual']    ?? '';
    $nueva     = $_POST['password_nueva']     ?? '';
    $confirmar = $_POST['password_confirmar'] ?? '';

    if ($actual === '' || $nueva === '' || $confirmar === '') {
        $mensajeError = 'Completa todos los campos.';
    } elseif ($nueva !== $confirmar) {
        $mensajeError = 'La nueva contraseña y la confirmación no coinciden.';
    } elseif (strlen($nueva) < 4) {
        $mensajeError = 'La nueva contraseña debe tener al menos 4 caracteres.';
    } elseif ($usuario->cambiarPassword($actual, $nueva)) {
        $_SESSION['flash_message'] = 'Contraseña actualizada correctamente.';
        header("Location: CambiarPassword.php");
        exit;
    } else {
        $mensajeError = 'La contraseña actual no es correcta.';
    }
}

$flashMensaje = $_SESSION['flash_message'] ?? '';
unset($_SESSION['flash_message']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Cambiar Contraseña</title>
    <link rel="stylesheet" href="../../../assets/style.css?v=<?= filemtime(__DIR__ . '/../../../assets/style.css') ?>">
</head>
<body>

<section class="admin-layout">

    <?php include_once __DIR__ . '/../includes/admin_sidebar.php'; ?>

    <main class="admin-main">

        <p class="admin-small-title">MI CUENTA</p>
        <h1>Cambiar Contraseña</h1>
        <p class="admin-subtitle">Introduce tu contraseña actual y elige una nueva.</p>

        <div id="flash-message" data-message="<?= htmlspecialchars($flashMensaje, ENT_QUOTES, 'UTF-8') ?>"></div>

        <?php if ($mensajeError !== null): ?>
            <section class="admin-alert">
                <strong>Error:</strong> <?= htmlspecialchars($mensajeError) ?>
            </section>
        <?php endif; ?>

        <section class="admin-panel-box cp-box">
            <form method="POST" autocomplete="off" class="cp-form">

                <div class="cp-field">
                    <label for="cp-actual">Contraseña actual</label>
                    <input type="password" id="cp-actual" name="password_actual" required autocomplete="current-password" placeholder="Tu contraseña actual">
                </div>

                <div class="cp-divider"></div>

                <div class="cp-field">
                    <label for="cp-nueva">Nueva contraseña</label>
                    <input type="password" id="cp-nueva" name="password_nueva" required autocomplete="new-password" placeholder="Mínimo 4 caracteres">
                </div>

                <div class="cp-field">
                    <label for="cp-confirmar">Confirmar nueva contraseña</label>
                    <input type="password" id="cp-confirmar" name="password_confirmar" required autocomplete="new-password" placeholder="Repite la nueva contraseña">
                </div>

                <button type="submit" class="admin-btn cp-submit">Guardar contraseña</button>

            </form>
        </section>

    </main>

</section>

</body>
</html>
