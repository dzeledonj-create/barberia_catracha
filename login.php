<?php
session_start();
require_once 'admin/clases_admin/GestorUsuarios.php';

$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = GestorUsuarios::autenticar($_POST['email'], $_POST['password']);

    if ($usuario) {
        $datosSesion = GestorUsuarios::obtenerDatosSesion($usuario);

        $_SESSION['usuario_id'] = $datosSesion['usuario_id'];
        $_SESSION['nombre'] = $datosSesion['nombre'];
        $_SESSION['email'] = $datosSesion['email'];
        $_SESSION['rol'] = $datosSesion['rol'];

        if (isset($datosSesion['barbero_id'])) {
            $_SESSION['barbero_id'] = $datosSesion['barbero_id'];
        }

        if ($_SESSION['rol'] === 'admin') {
            header("Location: admin/panel.php");
            exit;
        }

        if ($_SESSION['rol'] === 'barbero') {
            header("Location: barbero/panel.php");
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
    <title>Login - Barbería Catracha</title>
    <link rel="stylesheet" href="assets/style.css">
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