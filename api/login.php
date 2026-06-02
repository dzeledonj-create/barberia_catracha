<?php
// Configura la ruta de guardado de sesiones a un directorio temporal del sistema
ini_set('session.save_path', sys_get_temp_dir());
session_start();
require_once __DIR__ . '/admin/clases_admin/GestorUsuarios.php';

// Verificar si el usuario ya ha iniciado sesión y redirigirlo al panel de administración si es un administrador o barbero
$usuario = GestorUsuarios::obtenerDesdeSesion();
if ($usuario instanceof Administrador || $usuario instanceof UsuarioBarbero) {
    header("Location: /admin/panel.php");
    exit;
}

$error = "";

// Procesar el formulario de inicio de sesión cuando se envíe
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = GestorUsuarios::autenticar($_POST['email'], $_POST['password']);

    if ($usuario) {
        $datosSesion = GestorUsuarios::obtenerDatosSesion($usuario);

        $_SESSION['usuario_id'] = $datosSesion['usuario_id'];
        $_SESSION['nombre'] = $datosSesion['nombre'];
        $_SESSION['email'] = $datosSesion['email'];
        $_SESSION['rol'] = $datosSesion['rol'];

        // Si el usuario tiene un barbero_id, guardarlo en la sesión para facilitar su acceso en el panel
        if (isset($datosSesion['barbero_id'])) {
            $_SESSION['barbero_id'] = $datosSesion['barbero_id'];
        }

        // Redirigir al panel de administración si el usuario es un administrador o barbero
        if ($_SESSION['rol'] === 'admin' || $_SESSION['rol'] === 'barbero') {
            header("Location: /admin/panel.php");
            exit;
        }
    } else {
        $error = "Email o contraseña incorrectos";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - Barbería Catracha</title>
    <link rel="stylesheet" href="/barberia_catracha/assets/style.css">
</head>
<body>

<section class="login-page">

    <section class="login-icon">
    <img src="/barberia_catracha/assets/img/logo.png" alt="Logo Barbería Catracha">
</section>

    <section class="login-box">
        <h2>INICIAR SESIÓN</h2>

        <?php if ($error): ?>
            <p class="login-error"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>

        <form method="POST">
            <label>Correo electrónico</label>
            <input type="email" name="email" placeholder="tu@correo.com" required>

            <label>Contraseña</label>
            <input type="password" name="password" placeholder="********" required>

            <button type="submit">↪ ENTRAR AL PANEL</button>
        </form>
    </section>

    <a href="index.php" class="volver-web">← Volver a la web</a>

</section>

</body>
</html>