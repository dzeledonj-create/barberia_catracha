<?php
// Envío de correos de notificación de reservas (cliente + barbero) usando PHPMailer + SMTP.
// Se incluye y se llama directamente desde el flujo de creación de reservas
// (api/vistas/reservas.php) justo después de guardar la reserva con éxito,
// para reutilizar los mismos objetos/datos ya validados y guardados en la BD.

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/Clases/Cliente.php';
require_once __DIR__ . '/Clases/Barbero.php';
require_once __DIR__ . '/Clases/Servicio.php';
require_once __DIR__ . '/Clases/Reserva.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception as PHPMailerException;

// Convierte la fecha/hora guardada (formato "Y-m-d H:i:s") a un texto legible en español.
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

// Crea y configura una instancia de PHPMailer lista para enviar por SMTP
// usando las credenciales del archivo de configuración (fuera de git).
function crearCorreoSMTP(): ?PHPMailer {
    $configuracion = __DIR__ . '/config_email.php';
    if (!is_file($configuracion)) {
        return null;
    }
    require_once $configuracion;

    $correo = new PHPMailer(true);
    $correo->isSMTP();
    $correo->Host = EMAIL_SMTP_HOST;
    $correo->SMTPAuth = true;
    $correo->Username = EMAIL_SMTP_USUARIO;
    $correo->Password = EMAIL_SMTP_CONTRASENA;
    $correo->SMTPSecure = EMAIL_SMTP_SEGURIDAD;
    $correo->Port = EMAIL_SMTP_PUERTO;
    $correo->CharSet = 'UTF-8';
    $correo->setFrom(EMAIL_REMITENTE_DIRECCION, EMAIL_REMITENTE_NOMBRE);

    return $correo;
}

// Envía al cliente el correo de confirmación de su reserva.
function enviarCorreoConfirmacionCliente(Cliente $cliente, Barbero $barbero, Servicio $servicio, Reserva $reserva): bool {
    $destino = $cliente->getEmail();
    if (!$destino) {
        return false;
    }

    $correo = crearCorreoSMTP();
    if (!$correo) {
        return false;
    }

    $fechaTexto = formatearFechaHoraNotificacion($reserva->getFechaHora());

    try {
        $correo->addAddress($destino, $cliente->getNombreCompleto());
        $correo->Subject = 'Tu reserva en Barbería Catracha está confirmada';
        $correo->isHTML(true);
        $correo->Body = '<p>Hola ' . htmlspecialchars($cliente->getNombre()) . ',</p>'
            . '<p>Tu reserva ha sido registrada con éxito. Estos son los detalles:</p>'
            . '<ul>'
            . '<li><strong>Servicio:</strong> ' . htmlspecialchars($servicio->getNombre()) . '</li>'
            . '<li><strong>Barbero:</strong> ' . htmlspecialchars($barbero->getNombre()) . '</li>'
            . '<li><strong>Fecha y hora:</strong> ' . htmlspecialchars($fechaTexto) . '</li>'
            . '</ul>'
            . '<p>¡Te esperamos en Barbería Catracha!</p>';
        $correo->AltBody = "Hola {$cliente->getNombre()},\n\n"
            . "Tu reserva ha sido registrada con éxito.\n"
            . "Servicio: {$servicio->getNombre()}\n"
            . "Barbero: {$barbero->getNombre()}\n"
            . "Fecha y hora: {$fechaTexto}\n\n"
            . "¡Te esperamos en Barbería Catracha!";
        $correo->send();
        return true;
    } catch (PHPMailerException $e) {
        return false;
    }
}

// Avisa al barbero asignado de que tiene una nueva reserva.
function enviarCorreoAvisoBarbero(Cliente $cliente, Barbero $barbero, Servicio $servicio, Reserva $reserva): bool {
    $destino = $barbero->getEmail();
    if (!$destino) {
        return false;
    }

    $correo = crearCorreoSMTP();
    if (!$correo) {
        return false;
    }

    $fechaTexto = formatearFechaHoraNotificacion($reserva->getFechaHora());

    try {
        $correo->addAddress($destino, $barbero->getNombre());
        $correo->Subject = 'Nueva reserva asignada en Barbería Catracha';
        $correo->isHTML(true);
        $correo->Body = '<p>Hola ' . htmlspecialchars($barbero->getNombre()) . ',</p>'
            . '<p>Tienes una nueva reserva asignada:</p>'
            . '<ul>'
            . '<li><strong>Cliente:</strong> ' . htmlspecialchars($cliente->getNombreCompleto()) . '</li>'
            . '<li><strong>Teléfono:</strong> ' . htmlspecialchars($cliente->getTelefono()) . '</li>'
            . '<li><strong>Servicio:</strong> ' . htmlspecialchars($servicio->getNombre()) . '</li>'
            . '<li><strong>Fecha y hora:</strong> ' . htmlspecialchars($fechaTexto) . '</li>'
            . '</ul>';
        $correo->AltBody = "Hola {$barbero->getNombre()},\n\n"
            . "Tienes una nueva reserva asignada.\n"
            . "Cliente: {$cliente->getNombreCompleto()}\n"
            . "Teléfono: {$cliente->getTelefono()}\n"
            . "Servicio: {$servicio->getNombre()}\n"
            . "Fecha y hora: {$fechaTexto}";
        $correo->send();
        return true;
    } catch (PHPMailerException $e) {
        return false;
    }
}

// Punto de entrada: envía ambos correos (cliente + barbero) para una reserva recién creada.
// Cada envío se intenta de forma independiente; un fallo en uno no detiene al otro,
// y ningún fallo de correo debe impedir que la reserva ya guardada siga su curso normal.
function enviarNotificacionesReserva(Cliente $cliente, Barbero $barbero, Servicio $servicio, Reserva $reserva): array {
    $clienteNotificado = enviarCorreoConfirmacionCliente($cliente, $barbero, $servicio, $reserva);
    $barberoNotificado = enviarCorreoAvisoBarbero($cliente, $barbero, $servicio, $reserva);

    return [
        'ok' => ($clienteNotificado || $barberoNotificado),
        'cliente_notificado' => $clienteNotificado,
        'barbero_notificado' => $barberoNotificado,
    ];
}
