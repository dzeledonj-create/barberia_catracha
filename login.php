<?php
session_start();
require_once 'clases/clases_admin/GestorUsuarios.php';

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
    <section class="login-box">
        <h1>Iniciar sesión</h1>

        <?php if ($error): ?>
            <p class="login-error"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>

        <form method="POST">
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Contraseña" required>
            <button type="submit">Entrar</button>
        </form>
    </section>
</section>

</body>
</html>