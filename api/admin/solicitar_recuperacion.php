<?php
ini_set('session.save_path', sys_get_temp_dir());
session_start();
require_once __DIR__ . '/../Clases/BD.php';
require_once __DIR__ . '/clases_admin/GestorUsuarios.php';
require_once __DIR__ . '/../Notificaciones.php';

// Si ya hay sesión activa, ir al panel
$usuarioSesion = GestorUsuarios::obtenerDesdeSesion();
if ($usuarioSesion) {
    header('Location: GestionesAdmin/panel.php');
    exit;
}

$mensaje = null;
$esError = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');

    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $mensaje = 'Introduce un correo electrónico válido.';
        $esError = true;
    } else {
        $db   = BD::obtenerConexion();
        $stmt = $db->prepare("SELECT usuario_id FROM usuarios WHERE email = ? AND activo = TRUE LIMIT 1");
        $stmt->execute([$email]);
        $fila = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($fila) {
            $token   = bin2hex(random_bytes(32));
            $expires = (new DateTime())->modify('+1 hour')->format('Y-m-d H:i:s');

            $stmtGuardar = $db->prepare(
                "UPDATE usuarios SET reset_token = ?, token_expires = ? WHERE usuario_id = ?"
            );
            $stmtGuardar->execute([$token, $expires, $fila['usuario_id']]);

            enviarCorreoRecuperacion($email, $token);
        }

        // Mensaje neutro siempre (no revelamos si el email existe)
        $mensaje = 'Si ese correo está registrado, recibirás el enlace en breve. Revisa también la carpeta de spam.';
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Recuperar contraseña · Barbería Catracha</title>
    <link rel="stylesheet" href="../../assets/style.css?v=<?= filemtime(__DIR__ . '/../../assets/style.css') ?>">
</head>
<body>

<section class="login-page">

    <section class="login-icon">
        <img src="../../assets/img/logo.png" alt="Logo Barbería Catracha">
    </section>

    <section class="login-box">
        <h2>RECUPERAR CONTRASEÑA</h2>
        <p class="login-subtitle">Escribe tu correo y te enviaremos un enlace para crear una nueva contraseña.</p>

        <?php if ($mensaje !== null): ?>
            <p class="<?= $esError ? 'login-error' : 'login-msg' ?>"><?= htmlspecialchars($mensaje) ?></p>
        <?php endif; ?>

        <?php if ($mensaje === null || $esError): ?>
        <form method="POST">
            <label>Correo electrónico</label>
            <input type="email" name="email" placeholder="tu@correo.com" required
                   value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
            <button type="submit">ENVIAR ENLACE</button>
        </form>
        <?php endif; ?>
    </section>

    <a href="../login.php" class="volver-web">← Volver al inicio de sesión</a>

</section>

</body>
</html>
