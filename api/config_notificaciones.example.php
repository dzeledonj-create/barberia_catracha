<?php
// Plantilla de configuración de notificaciones automáticas.
// ─────────────────────────────────────────────────────────
// 1. Copia este archivo y renómbralo como:  config_notificaciones.php
// 2. Rellena los valores con tus credenciales reales.
// 3. El archivo real (sin ".example") está en .gitignore y NUNCA
//    debe subirse al repositorio para no exponer contraseñas.

// ── Telegram Bot ─────────────────────────────────────────
// Token que te da @BotFather al crear el bot.
// Formato: 1234567890:ABCDEFxxxxxxxxxxxxxxxxxxxxxxxxxx
define('TELEGRAM_BOT_TOKEN', '');
// Chat ID del grupo o chat donde el bot enviará los avisos.
// Para grupos el valor es negativo, ej. -1001234567890
define('TELEGRAM_CHAT_ID', '');

// ── OneSignal (Web Push) ──────────────────────────────────
// App ID público (se usa también en el navegador, no es secreto).
define('ONESIGNAL_APP_ID', '');
// REST API Key privada (solo backend, nunca exponerla en el navegador).
define('ONESIGNAL_REST_API_KEY', '');

// ── SMTP (correo de confirmación al cliente) ──────────────
// Host SMTP de tu proveedor, p.ej:
//   Gmail    → smtp.gmail.com       (App Password recomendado)
//   Hostinger → smtp.hostinger.com
define('MAIL_HOST',      '');
// Puerto: 587 = STARTTLS  |  465 = SSL/TLS
define('MAIL_PORT',      587);
// Cuenta de correo que envía los mensajes.
define('MAIL_USERNAME',  '');
// Contraseña SMTP o App Password de Gmail.
define('MAIL_PASSWORD',  '');
// Nombre visible en el campo "De:" del correo.
define('MAIL_FROM_NAME', 'Barbería Catracha');
