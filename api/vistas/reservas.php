<?php
require_once __DIR__ . '/../Clases/BD.php';
require_once __DIR__ . '/../Clases/Barbero.php';
require_once __DIR__ . '/../Clases/Servicio.php';
require_once __DIR__ . '/../Clases/Horario.php';
require_once __DIR__ . '/../Clases/Reserva.php';
require_once __DIR__ . '/../Clases/Cliente.php';
require_once __DIR__ . '/../send_notification.php';

$mensajeExito = null;
$mensajeError = null;

if (isset($_GET['reserva']) && $_GET['reserva'] === 'ok') {
    $mensajeExito = 'Tu reserva se ha guardado correctamente. Gracias por reservar con nosotros.';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $servicioId = $_POST['servicio_id'] ?? null;
    $barberoId = $_POST['barbero_id'] ?? null;
    $fecha = $_POST['fecha'] ?? null;
    $hora = $_POST['hora'] ?? null;
    $nombre = trim($_POST['nombre'] ?? '');
    $apellido = trim($_POST['apellido'] ?? '');
    $telefono = trim($_POST['telefono'] ?? '');
    $email = trim($_POST['email'] ?? '');

    if (!$servicioId || !$barberoId || !$fecha || !$hora || !$nombre || !$apellido || !$telefono) {
        $mensajeError = 'Por favor completa todos los campos obligatorios antes de confirmar tu reserva.';
    } else {
        $fechaHora = DateTime::createFromFormat('d/m/Y H:i', "$fecha $hora");
        if (!$fechaHora) {
            $mensajeError = 'La fecha u hora seleccionadas no son válidas. Verifica tu selección.';
        } else {
            $cliente = new Cliente($nombre, $apellido, $telefono, $email ?: null);
            if (!$cliente->guardar()) {
                $mensajeError = 'No se pudo guardar la información del cliente. Por favor, inténtalo de nuevo.';
            } else {
                $reserva = new Reserva(
                    $cliente->getClienteId(),
                    (int)$barberoId,
                    (int)$servicioId,
                    $fechaHora->format('Y-m-d H:i:s')
                );

                if ($reserva->guardar()) {
                    // Enviamos los correos de notificación (cliente + barbero) ya con la
                    // reserva guardada y sus datos reales. Si el correo falla por la razón
                    // que sea (SMTP no configurado, credenciales inválidas, etc.) la reserva
                    // ya quedó guardada y el cliente debe ver la confirmación igualmente.
                    try {
                        $barberoReservado = Barbero::obtenerPorId($barberoId);
                        $servicioReservado = Servicio::obtenerPorId($servicioId);
                        if ($barberoReservado && $servicioReservado) {
                            enviarNotificacionesReserva($cliente, $barberoReservado, $servicioReservado, $reserva);
                        }
                    } catch (Exception $e) {
                        // No interrumpimos el flujo de la reserva por un fallo de envío de correo.
                    }

                    header('Location: reservas.php?reserva=ok');
                    exit;
                }

                $mensajeError = 'No se pudo guardar la reserva. El barbero tiene ya esa hora reservada.';
            }
        }
    }
}

$servicios = Servicio::obtenerTodos();
$barberos = Barbero::obtenerActivos(); // Devuelve objetos Barbero con getters disponibles
$horarios = Horario::obtenerTodos();
$db = BD::obtenerConexion();

$stmt = $db->query("SELECT barbero_id, servicio_id FROM barbero_servicio");
$relaciones = $stmt->fetchAll(PDO::FETCH_ASSOC);

$barberoServicios = [];
foreach ($relaciones as $rel) {
    $barberoServicios[$rel['barbero_id']][] = $rel['servicio_id'];
}

// Reservas activas (no canceladas) para validar en el navegador si un barbero ya está ocupado a la hora elegida
$stmtOcupadas = $db->query("SELECT barbero_id, fecha_hora FROM reservas WHERE estado != 'cancelada'");
$reservasOcupadas = $stmtOcupadas->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reservas - Barbería Catracha</title>
    <link rel="stylesheet" href="../../assets/style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="icon" href="../../assets/img/logo.png" type="image/png">
</head>
<body>

<?php include_once __DIR__ . '/../includes/header.php'; ?>

<section class="reservas-page">
    <div class="reservas-container">
        <h1>Reserva tu Cita</h1>

        <div class="pasos-bar">
            <div class="paso-item activo" data-paso="0"><span class="paso-numero">1</span><span class="paso-texto">Servicio</span></div>
            <div class="paso-item" data-paso="1"><span class="paso-numero">2</span><span class="paso-texto">Fecha y Hora</span></div>
            <div class="paso-item" data-paso="2"><span class="paso-numero">3</span><span class="paso-texto">Barbero</span></div>
            <div class="paso-item" data-paso="3"><span class="paso-numero">4</span><span class="paso-texto">Tus Datos</span></div>
            <div class="paso-item" data-paso="4"><span class="paso-numero">5</span><span class="paso-texto">Confirmación</span></div>
        </div>

        <?php if ($mensajeExito !== null): ?>
            <div class="alert alert-success">
                <span class="alert-icono">&#10003;</span>
                <div class="alert-texto">
                    <strong>¡Reserva confirmada!</strong>
                    <p><?= htmlspecialchars($mensajeExito) ?></p>
                </div>
            </div>
        <?php elseif ($mensajeError !== null): ?>
            <div class="alert alert-error">
                <span class="alert-icono">&#9888;</span>
                <div class="alert-texto">
                    <strong>No se pudo completar la reserva</strong>
                    <p><?= htmlspecialchars($mensajeError) ?></p>
                </div>
            </div>
        <?php endif; ?>

        <div class="seguimiento-live" id="seguimiento-live">
            <div class="seg-item" id="track-servicio"><strong>Servicio:</strong> <span>Ninguno seleccionado</span></div>
            <div class="seg-item" id="track-barbero"><strong>Barbero:</strong> <span>Ninguno seleccionado</span></div>
            <div class="seg-item" id="track-cita"><strong>Cita:</strong> <span>No establecida</span></div>
        </div>

        <form id="form-reserva" action="" method="POST">
            
            <div class="reserva-step activo" id="step-1">
                <h2>Selecciona un servicio</h2>
                
                <div class="servicios-filtros">
                    <button type="button" class="filtro-btn activo" data-categoria="todos">Todos</button>
                    <button type="button" class="filtro-btn" data-categoria="corte">Cortes</button>
                    <button type="button" class="filtro-btn" data-categoria="barba">Barba</button>
                    <button type="button" class="filtro-btn" data-categoria="tinte">Tintes / Color</button>
                    <button type="button" class="filtro-btn" data-categoria="otros">Otros Combos</button>
                </div>

                <div class="servicios-grid">
                    <?php foreach ($servicios as $servicio): 
                        $nombreLower = mb_strtolower($servicio->getNombre());
                        $cat = 'otros';
                        if (str_contains($nombreLower, 'corte') || str_contains($nombreLower, 'pelo') || str_contains($nombreLower, 'cejas')) { $cat = 'corte'; }
                        if (str_contains($nombreLower, 'barba')) { $cat = (str_contains($nombreLower, 'corte')) ? 'otros' : 'barba'; }
                        if (str_contains($nombreLower, 'tinte') || str_contains($nombreLower, 'color') || str_contains($nombreLower, 'mechas')) { $cat = 'tinte'; }
                    ?>
                        <label class="card-option servicio-card" data-cat="<?= $cat ?>" data-duracion="<?= htmlspecialchars($servicio->getDuracionMinutos()) ?>">
                            <input type="radio" name="servicio_id" value="<?= $servicio->getServicioId() ?>" required>
                            <div class="card-content">
                                <div class="card-info">
                                    <h3><?= htmlspecialchars($servicio->getNombre()) ?></h3>
                                    <p><?= htmlspecialchars($servicio->getDescripcion() ?? '') ?></p>
                                </div>
                                <div class="card-meta">
                                    <span class="precio"><?= number_format($servicio->getPrecio(), 2) ?> €</span>
                                    <span class="duracion"><?= $servicio->getDuracionMinutos() ?> min</span>
                                </div>
                            </div>
                        </label>
                    <?php endforeach; ?>
                </div>
                <div class="nav-buttons single-btn">
                    <button type="button" class="btn-siguiente">Siguiente Paso</button>
                </div>
            </div>

            <div class="reserva-step" id="step-2">
                <h2>Elige fecha y hora</h2>
                <div class="agenda-container">
                    <div class="calendario-box">
                        <input type="text" id="datepicker" name="fecha" placeholder="Selecciona una fecha" readonly required>
                        <input type="hidden" name="hora" id="hora-seleccionada">
                    </div>
                    <div class="horas-box">
                        <h3>Horas disponibles</h3>
                        <div id="horas-grid" class="horas-grid">
                            <p class="select-date-msg">Por favor, selecciona una fecha primero.</p>
                        </div>
                    </div>
                </div>
                <div class="nav-buttons">
                    <button type="button" class="btn-atras">Atrás</button>
                    <button type="button" class="btn-siguiente" id="btn-validar-hora">Siguiente Paso</button>
                </div>
            </div>

            <div class="reserva-step" id="step-3">
                <h2>Selecciona tu barbero</h2>
                <p class="paso-ayuda">Solo se muestran disponibles los barberos que pueden atenderte en la fecha y hora elegidas.</p>
                <div class="barberos-grid">
                    <?php foreach ($barberos as $barbero):
                        // Normalización inteligente de rutas de imágenes
                        $foto = $barbero->getFotoUrl() ?? '';
                        if (!empty($foto) && !str_starts_with($foto, 'http') && !str_starts_with($foto, '/') && !str_starts_with($foto, '../')) {
                            $foto = '../' . $foto;
                        }
                        if (empty($foto)) { $foto = '../../assets/img/default-avatar.png'; }
                    ?>
                        <label class="card-option barbero-card" data-id="<?= $barbero->getBarberoId() ?>" data-servicios="<?= htmlspecialchars(implode(',', $barberoServicios[$barbero->getBarberoId()] ?? [])) ?>">
                            <input type="radio" name="barbero_id" value="<?= $barbero->getBarberoId() ?>" required>
                            <div class="card-content-barbero">
                                <div class="barbero-img">
                                    <img src="<?= htmlspecialchars($foto) ?>" onerror="this.src='../assets/img/default-avatar.png';" alt="<?= htmlspecialchars($barbero->getNombre()) ?>">
                                </div>
                                <h3><?= htmlspecialchars($barbero->getNombre()) ?></h3>
                                <span class="especialidad"><?= htmlspecialchars($barbero->getEspecialidad() ?? 'Barbero Profesional') ?></span>
                            </div>
                        </label>
                    <?php endforeach; ?>
                </div>
                <div class="nav-buttons">
                    <button type="button" class="btn-atras">Atrás</button>
                    <button type="button" class="btn-siguiente">Siguiente Paso</button>
                </div>
            </div>

            <div class="reserva-step" id="step-4">
                <h2>Tus datos personales</h2>
                <div class="form-group-grid">
                    <div class="input-box">
                        <label>Nombre *</label>
                        <input type="text" name="nombre" placeholder="Ingresa tu nombre" required>
                    </div>
                    <div class="input-box">
                        <label>Apellido *</label>
                        <input type="text" name="apellido" placeholder="Ingresa tu apellido" required>
                    </div>
                    <div class="input-box">
                        <label>Teléfono *</label>
                        <input type="tel" name="telefono" placeholder="Ej: 600000000" required>
                    </div>
                    <div class="input-box">
                        <label>Correo Electrónico</label>
                        <input type="email" name="email" placeholder="nombre@correo.com">
                    </div>
                </div>
                <div class="nav-buttons">
                    <button type="button" class="btn-atras">Atrás</button>
                    <button type="button" class="btn-siguiente">Siguiente Paso</button>
                </div>
            </div>

            <div class="reserva-step" id="step-5">
                <h2>Confirmar tu cita</h2>
                <div class="resumen-reserva-box">
                    <p>Por favor, verifica que los detalles de tu cita sean correctos antes de finalizar.</p>
                    <div class="ticket-resumen">
                        <div class="ticket-line"><strong>Servicio:</strong> <span id="resumen-servicio">-</span></div>
                        <div class="ticket-line"><strong>Barbero:</strong> <span id="resumen-barbero">-</span></div>
                        <div class="ticket-line"><strong>Fecha:</strong> <span id="resumen-fecha">-</span></div>
                        <div class="ticket-line"><strong>Hora:</strong> <span id="resumen-hora">-</span></div>
                        <div class="ticket-line total"><strong>Precio total:</strong> <span id="resumen-precio">-</span></div>
                    </div>
                </div>
                <div class="nav-buttons">
                    <button type="button" class="btn-atras">Atrás</button>
                    <button type="submit" class="btn-confirmar">Confirmar Reserva</button>
                </div>
            </div>

        </form>
    </div>
</section>

<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
    window.reservaScheduleData = <?= json_encode($horarios, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT); ?>;
    window.reservasOcupadas = <?= json_encode($reservasOcupadas, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT); ?>;
</script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/es.js"></script>
<script src="../../assets/script.js"></script>
</body>
<?php include_once __DIR__ . '/../includes/footer.php'; ?>
</html>