<?php
ini_set('session.save_path', sys_get_temp_dir());
session_start();
require_once __DIR__ . '/../Clases/BD.php';
require_once __DIR__ . '/clases_admin/GestorUsuarios.php';

$token      = trim($_GET['token'] ?? '');
$mensajeOk  = null;
$mensajeErr = null;
$tokenValido = false;
$usuarioId   = null;

// Validar token
if ($token !== '') {
    $db   = BD::obtenerConexion();
    $stmt = $db->prepare(
        "SELECT usuario_id FROM usuarios
         WHERE reset_token = ? AND token_expires > NOW()
         LIMIT 1"
    );
    $stmt->execute([$token]);
    $fila = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($fila) {
        $tokenValido = true;
        $usuarioId   = (int)$fila['usuario_id'];
    } else {
        $mensajeErr = 'Este enlace no es válido o ha caducado. Solicita uno nuevo.';
    }
} else {
    $mensajeErr = 'Enlace incorrecto. Solicita uno nuevo.';
}

// Procesar nueva contraseña
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $tokenValido) {
    $nueva     = $_POST['password_nueva']     ?? '';
    $confirmar = $_POST['password_confirmar'] ?? '';
    $tokenPost = $_POST['token']              ?? '';

    // Re-verificar token en el POST
    $db   = BD::obtenerConexion();
    $stmt = $db->prepare(
        "SELECT usuario_id FROM usuarios
         WHERE reset_token = ? AND token_expires > NOW()
         LIMIT 1"
    );
    $stmt->execute([$tokenPost]);
    $filaPost = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$filaPost) {
        $mensajeErr  = 'El enlace ha expirado. Solicita uno nuevo.';
        $tokenValido = false;
    } elseif (strlen($nueva) < 4) {
        $mensajeErr = 'La nueva contraseña debe tener al menos 4 caracteres.';
    } elseif ($nueva !== $confirmar) {
        $mensajeErr = 'Las contraseñas no coinciden.';
    } else {
        $hash = password_hash($nueva, PASSWORD_DEFAULT);
        $stmtUpdate = $db->prepare(
            "UPDATE usuarios
             SET password = ?, reset_token = NULL, token_expires = NULL
             WHERE usuario_id = ?"
        );
        $stmtUpdate->execute([$hash, (int)$filaPost['usuario_id']]);

        $mensajeOk   = 'Contraseña restablecida correctamente. Ya puedes iniciar sesión.';
        $tokenValido = false; // ocultar el formulario
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Nueva contraseña · Barbería Catracha</title>
    <link rel="stylesheet" href="../../assets/style.css?v=<?= filemtime(__DIR__ . '/../../assets/style.css') ?>">
</head>
<body>

<section class="login-page">

    <section class="login-icon">
        <img src="../../assets/img/logo.png" alt="Logo Barbería Catracha">
    </section>

    <section class="login-box">
        <h2>NUEVA CONTRASEÑA</h2>

        <?php if ($mensajeOk !== null): ?>
            <p class="login-msg"><?= htmlspecialchars($mensajeOk) ?></p>
            <a href="../login.php" class="login-btn-link">↪ IR AL INICIO DE SESIÓN</a>

        <?php elseif ($mensajeErr !== null && !$tokenValido): ?>
            <p class="login-error"><?= htmlspecialchars($mensajeErr) ?></p>
            <a href="solicitar_recuperacion.php" class="login-btn-link">Solicitar nuevo enlace</a>

        <?php else: ?>
            <?php if ($mensajeErr !== null): ?>
                <p class="login-error"><?= htmlspecialchars($mensajeErr) ?></p>
            <?php endif; ?>

            <p class="login-subtitle">Elige una contraseña segura de al menos 4 caracteres.</p>

            <form method="POST" id="form-recuperar">
                <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">

                <label>Nueva contraseña</label>
                <input type="password" id="rp-nueva" name="password_nueva"
                       placeholder="Mínimo 4 caracteres" required>

                <label>Confirmar contraseña</label>
                <input type="password" id="rp-confirmar" name="password_confirmar"
                       placeholder="Repite la contraseña" required>

                <p class="login-error" id="rp-error-js" style="display:none;"></p>

                <button type="submit">GUARDAR CONTRASEÑA</button>
            </form>
        <?php endif; ?>
    </section>

    <a href="../login.php" class="volver-web">← Volver al inicio de sesión</a>

</section>

<script src="../../assets/script.js?v=<?= filemtime(__DIR__ . '/../../assets/script.js') ?>"></script>
</body>
</html>
