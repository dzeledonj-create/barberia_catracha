<?php
// Plantilla de configuración del correo saliente (SMTP).
// Copia este archivo como "config_email.php" (sin ".example") en la misma carpeta
// y rellena tus datos reales. Ese archivo NO se sube a git (ver .gitignore),
// para que las credenciales del correo no queden expuestas en el repositorio.

define('EMAIL_SMTP_HOST', 'smtp.gmail.com');
define('EMAIL_SMTP_PUERTO', 587);
define('EMAIL_SMTP_SEGURIDAD', 'tls'); // 'tls' o 'ssl'
define('EMAIL_SMTP_USUARIO', 'tu_correo@gmail.com');
define('EMAIL_SMTP_CONTRASENA', 'tu_contraseña_de_aplicación');

define('EMAIL_REMITENTE_DIRECCION', 'tu_correo@gmail.com');
define('EMAIL_REMITENTE_NOMBRE', 'Barbería Catracha');
