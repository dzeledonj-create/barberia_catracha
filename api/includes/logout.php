<?php
// Archivo para manejar el cierre de sesión de los usuarios

// Configuramos la ruta de almacenamiento de sesiones para evitar problemas con permisos en diferentes entornos
ini_set('session.save_path', sys_get_temp_dir());
// Iniciamos la sesión para poder manipularla
session_start();

// Limpiamos todas las variables de sesión y destruimos la sesión actual para cerrar la sesión del usuario
session_unset();
session_destroy();

header("Location: ../login.php");
exit;