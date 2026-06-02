<?php
// Archivo para manejar el cierre de sesión de los usuarios
ini_set('session.save_path', sys_get_temp_dir());
session_start();

session_unset();
session_destroy();

header("Location: /login.php");
exit;