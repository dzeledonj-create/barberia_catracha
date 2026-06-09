<?php
// Notificaciones automáticas de nuevas reservas, por dos canales simultáneos:
// un Bot de Telegram y notificaciones Web Push vía OneSignal. Se incluye y se llama
// directamente desde el flujo de creación de reservas (api/vistas/reservas.php),
// justo después de guardar la reserva con éxito.
//
// Los tokens y claves de API viven en config_notificaciones.php (fuera de git, ver
// .gitignore) y no en este archivo, para no exponer credenciales en el repositorio.
// Si ese archivo no existe o las constantes están vacías, cada función simplemente
// no envía nada y devuelve false: un fallo de notificación nunca debe romper el
// flujo de reservas.

$configuracionNotificaciones = __DIR__ . '/config_notificaciones.php';
if (is_file($configuracionNotificaciones)) {
    require_once $configuracionNotificaciones;
}

$_autoloader = __DIR__ . '/../vendor/autoload.php';
if (is_file($_autoloader)) {
    require_once $_autoloader;
}

// Envía el aviso de nueva reserva al chat de Telegram configurado, usando cURL
// contra la API HTTP de Telegram (sendMessage). $datosReserva debe traer las claves
// 'cliente', 'servicio', 'barbero' y 'fecha_hora' ya formateadas como texto.
function enviarNotificacionTelegram(array $datosReserva): bool {
    if (!defined('TELEGRAM_BOT_TOKEN') || !defined('TELEGRAM_CHAT_ID')
        || TELEGRAM_BOT_TOKEN === '' || TELEGRAM_CHAT_ID === '') {
        return false;
    }

    $mensaje = "🔔 Nueva reserva confirmada\n"
        . "Cliente: {$datosReserva['cliente']}\n"
        . "Servicio: {$datosReserva['servicio']}\n"
        . "Barbero: {$datosReserva['barbero']}\n"
        . "Fecha y hora: {$datosReserva['fecha_hora']}";

    $url = 'https://api.telegram.org/bot' . TELEGRAM_BOT_TOKEN . '/sendMessage';
    $cuerpo = http_build_query([
        'chat_id' => TELEGRAM_CHAT_ID,
        'text' => $mensaje,
    ]);

    $curl = curl_init($url);
    curl_setopt_array($curl, [
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => $cuerpo,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CONNECTTIMEOUT => 5,
        CURLOPT_TIMEOUT => 8,
    ]);
    $respuesta = curl_exec($curl);
    $codigo = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    curl_close($curl);

    return ($respuesta !== false && $codigo === 200);
}

// Envía una notificación Web Push a través de la API REST de OneSignal a todos los
// dispositivos suscritos (los barberos/administradores que activaron el push desde
// el panel). $datosReserva con las mismas claves que enviarNotificacionTelegram().
function enviarWebPushOneSignal(array $datosReserva): bool {
    if (!defined('ONESIGNAL_APP_ID') || !defined('ONESIGNAL_REST_API_KEY')
        || ONESIGNAL_APP_ID === '' || ONESIGNAL_REST_API_KEY === '') {
        return false;
    }

    $cuerpo = json_encode([
        'app_id' => ONESIGNAL_APP_ID,
        'included_segments' => ['Subscribed Users'],
        'headings' => ['es' => 'Nueva reserva en Barbería Catracha'],
        'contents' => ['es' => "{$datosReserva['cliente']} reservó \"{$datosReserva['servicio']}\" con {$datosReserva['barbero']} el {$datosReserva['fecha_hora']}."],
    ]);

    $curl = curl_init('https://api.onesignal.com/notifications');
    curl_setopt_array($curl, [
        CURLOPT_POST => true,
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json; charset=utf-8',
            'Authorization: Key ' . ONESIGNAL_REST_API_KEY,
        ],
        CURLOPT_POSTFIELDS => $cuerpo,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CONNECTTIMEOUT => 5,
        CURLOPT_TIMEOUT => 8,
    ]);
    $respuesta = curl_exec($curl);
    $codigo = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    curl_close($curl);

    return ($respuesta !== false && $codigo === 200);
}

// Convierte la fecha/hora guardada (formato "Y-m-d H:i:s") a un texto legible en español,
// listo para mostrarse en los mensajes de Telegram y OneSignal.
function formatearFechaHoraNotificacion(string $fechaHora): string {
    $dias = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'];
    $meses = [
        'January' => 'enero', 'February' => 'febrero', 'March' => 'marzo', 'April' => 'abril',
        'May' => 'mayo', 'June' => 'junio', 'July' => 'julio', 'August' => 'agosto',
        'September' => 'septiembre', 'October' => 'octubre', 'November' => 'noviembre', 'December' => 'diciembre',
    ];
    $fecha = DateTime::createFromFormat('Y-m-d H:i:s', $fechaHora);
    if (!$fecha) {
        return $fechaHora;
    }
    $diaSemana = $dias[$fecha->format('N') - 1];
    $mes = $meses[$fecha->format('F')];
    return sprintf('%s %d de %s de %s a las %s', $diaSemana, (int)$fecha->format('j'), $mes, $fecha->format('Y'), $fecha->format('H:i'));
}

// Envía el correo de recuperación de contraseña con el enlace del token.
function enviarCorreoRecuperacion(string $email, string $token): bool {
    if (!defined('MAIL_HOST') || MAIL_HOST === ''
        || !defined('MAIL_USERNAME') || MAIL_USERNAME === ''
        || !defined('MAIL_PASSWORD') || MAIL_PASSWORD === '') {
        return false;
    }
    if (!class_exists('PHPMailer\PHPMailer\PHPMailer')) {
        return false;
    }

    $protocolo = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host      = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $enlace    = $protocolo . '://' . $host . '/barberia_catracha/api/admin/recuperar_password.php?token=' . $token;

    $nombre    = defined('MAIL_FROM_NAME') ? MAIL_FROM_NAME : 'Barbería Catracha';
    $cuerpoHtml = <<<HTML
<!DOCTYPE html>
<html lang="es">
<head><meta charset="UTF-8"></head>
<body style="margin:0;padding:0;background:#f0f0f0;font-family:Arial,Helvetica,sans-serif;">
  <table width="100%" cellpadding="0" cellspacing="0" style="background:#f0f0f0;padding:30px 0;">
    <tr><td align="center">
      <table width="600" cellpadding="0" cellspacing="0" style="max-width:600px;width:100%;background:#111111;border-radius:10px;overflow:hidden;">
        <tr>
          <td style="background:#d4a017;padding:26px 40px;text-align:center;">
            <p style="margin:0;color:#000;font-size:11px;letter-spacing:4px;text-transform:uppercase;font-weight:700;">Barbería</p>
            <h1 style="margin:4px 0 0;color:#000;font-size:26px;font-weight:900;letter-spacing:3px;">CATRACHA</h1>
          </td>
        </tr>
        <tr>
          <td style="padding:36px 40px;">
            <h2 style="margin:0 0 10px;color:#ffffff;font-size:20px;font-weight:800;">Recuperación de contraseña</h2>
            <p style="margin:0 0 28px;color:#999;font-size:15px;line-height:1.6;">
              Hemos recibido una solicitud para restablecer tu contraseña.<br>
              Haz clic en el botón de abajo para elegir una nueva contraseña.<br>
              <strong style="color:#d4a017;">El enlace expira en 1 hora.</strong>
            </p>
            <table cellpadding="0" cellspacing="0" style="margin:0 auto;">
              <tr>
                <td style="background:#d4a017;border-radius:6px;padding:14px 32px;text-align:center;">
                  <a href="{$enlace}" style="color:#000;text-decoration:none;font-weight:900;font-size:14px;letter-spacing:1px;">
                    RESTABLECER CONTRASEÑA
                  </a>
                </td>
              </tr>
            </table>
            <p style="margin:26px 0 0;color:#555;font-size:12px;text-align:center;line-height:1.6;">
              Si no solicitaste esto, ignora este correo.<br>Tu contraseña no cambiará.
            </p>
          </td>
        </tr>
        <tr>
          <td style="background:#0a0a0a;padding:16px 40px;text-align:center;border-top:1px solid #1e1e1e;">
            <p style="margin:0;color:#444;font-size:12px;">© Barbería Catracha · Todos los derechos reservados</p>
          </td>
        </tr>
      </table>
    </td></tr>
  </table>
</body>
</html>
HTML;

    try {
        $mail = new PHPMailer\PHPMailer\PHPMailer(true);
        $mail->isSMTP();
        $mail->Host       = MAIL_HOST;
        $mail->SMTPAuth   = true;
        $mail->Username   = MAIL_USERNAME;
        $mail->Password   = MAIL_PASSWORD;
        $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = defined('MAIL_PORT') ? (int)MAIL_PORT : 587;
        $mail->CharSet    = 'UTF-8';
        $mail->setFrom(MAIL_USERNAME, $nombre);
        $mail->addAddress($email);
        $mail->isHTML(true);
        $mail->Subject = 'Recuperación de contraseña - Barbería Catracha';
        $mail->Body    = $cuerpoHtml;
        $mail->AltBody = "Para restablecer tu contraseña visita: $enlace (válido 1 hora)";
        $mail->send();
        return true;
    } catch (Throwable $e) {
        return false;
    }
}

// Construye el HTML del correo de confirmación.
// El CSS va inline porque los clientes de correo (Gmail, Outlook, etc.)
// no soportan hojas de estilo externas — es el estándar obligatorio en emails.
function construirEmailReserva(array $d): string {
    $nombre    = htmlspecialchars($d['cliente'],    ENT_QUOTES);
    $servicio  = htmlspecialchars($d['servicio'],   ENT_QUOTES);
    $barbero   = htmlspecialchars($d['barbero'],    ENT_QUOTES);
    $fechaHora = htmlspecialchars($d['fecha_hora'], ENT_QUOTES);

    return <<<HTML
<!DOCTYPE html>
<html lang="es">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1"></head>
<body style="margin:0;padding:0;background:#f0f0f0;font-family:Arial,Helvetica,sans-serif;">
  <table width="100%" cellpadding="0" cellspacing="0" style="background:#f0f0f0;padding:30px 0;">
    <tr><td align="center">
      <table width="600" cellpadding="0" cellspacing="0" style="max-width:600px;width:100%;background:#111111;border-radius:10px;overflow:hidden;">

        <tr>
          <td style="background:#d4a017;padding:28px 40px;text-align:center;">
            <p style="margin:0;color:#000;font-size:11px;letter-spacing:4px;text-transform:uppercase;font-weight:700;">Barbería</p>
            <h1 style="margin:4px 0 0;color:#000;font-size:28px;font-weight:900;letter-spacing:3px;">CATRACHA</h1>
          </td>
        </tr>

        <tr>
          <td style="padding:36px 40px;">
            <h2 style="margin:0 0 6px;color:#d4a017;font-size:22px;font-weight:800;">¡Reserva Confirmada!</h2>
            <p style="margin:0 0 28px;color:#999;font-size:15px;">Hola <strong style="color:#fff;">{$nombre}</strong>, tu cita está confirmada.</p>

            <table width="100%" cellpadding="0" cellspacing="0" style="background:#1a1a1a;border-radius:8px;border:1px solid #2b2b2b;overflow:hidden;">
              <tr>
                <td style="padding:18px 24px;border-bottom:1px solid #2b2b2b;">
                  <p style="margin:0;color:#888;font-size:10px;text-transform:uppercase;letter-spacing:1.2px;">Servicio</p>
                  <p style="margin:5px 0 0;color:#fff;font-size:17px;font-weight:700;">{$servicio}</p>
                </td>
              </tr>
              <tr>
                <td style="padding:18px 24px;border-bottom:1px solid #2b2b2b;">
                  <p style="margin:0;color:#888;font-size:10px;text-transform:uppercase;letter-spacing:1.2px;">Barbero</p>
                  <p style="margin:5px 0 0;color:#fff;font-size:17px;font-weight:700;">{$barbero}</p>
                </td>
              </tr>
              <tr>
                <td style="padding:18px 24px;">
                  <p style="margin:0;color:#888;font-size:10px;text-transform:uppercase;letter-spacing:1.2px;">Fecha y hora</p>
                  <p style="margin:5px 0 0;color:#d4a017;font-size:19px;font-weight:900;">{$fechaHora}</p>
                </td>
              </tr>
            </table>

            <p style="margin:24px 0 0;color:#555;font-size:13px;text-align:center;line-height:1.7;">
              Si necesitas cancelar o cambiar tu cita, no dudes en contactarnos.<br>¡Te esperamos!
            </p>
          </td>
        </tr>

        <tr>
          <td style="background:#0a0a0a;padding:18px 40px;text-align:center;border-top:1px solid #1e1e1e;">
            <p style="margin:0;color:#444;font-size:12px;">© Barbería Catracha · Todos los derechos reservados</p>
          </td>
        </tr>

      </table>
    </td></tr>
  </table>
</body>
</html>
HTML;
}

// Envía un correo de confirmación al cliente con los detalles de su reserva.
// Requiere PHPMailer (vendor/autoload.php) y las constantes MAIL_* en config_notificaciones.php.
function enviarCorreoCliente(array $datosReserva): bool {
    if (!defined('MAIL_HOST') || MAIL_HOST === ''
        || !defined('MAIL_USERNAME') || MAIL_USERNAME === ''
        || !defined('MAIL_PASSWORD') || MAIL_PASSWORD === '') {
        return false;
    }
    if (empty($datosReserva['email'])) {
        return false;
    }
    if (!class_exists('PHPMailer\PHPMailer\PHPMailer')) {
        return false;
    }

    try {
        $mail = new PHPMailer\PHPMailer\PHPMailer(true);
        $mail->isSMTP();
        $mail->Host       = MAIL_HOST;
        $mail->SMTPAuth   = true;
        $mail->Username   = MAIL_USERNAME;
        $mail->Password   = MAIL_PASSWORD;
        $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = defined('MAIL_PORT') ? (int)MAIL_PORT : 587;
        $mail->CharSet    = 'UTF-8';
        $mail->setFrom(MAIL_USERNAME, defined('MAIL_FROM_NAME') ? MAIL_FROM_NAME : 'Barbería Catracha');
        $mail->addAddress($datosReserva['email'], $datosReserva['cliente']);
        $mail->isHTML(true);
        $mail->Subject = '✅ Reserva confirmada en Barbería Catracha';
        $mail->Body    = construirEmailReserva($datosReserva);
        $mail->AltBody = "Reserva confirmada: {$datosReserva['servicio']} con {$datosReserva['barbero']} el {$datosReserva['fecha_hora']}.";
        $mail->send();
        return true;
    } catch (Throwable $e) {
        return false;
    }
}

// Punto de entrada: dispara los tres canales para una reserva recién guardada. Cada uno
// se intenta de forma independiente —un fallo en uno no detiene al otro— y ningún
// fallo de notificación debe impedir que la reserva ya guardada siga su curso normal.
function enviarNotificacionesReserva(array $datosReserva): array {
    return [
        'telegram'  => enviarNotificacionTelegram($datosReserva),
        'onesignal' => enviarWebPushOneSignal($datosReserva),
        'email'     => enviarCorreoCliente($datosReserva),
    ];
}
