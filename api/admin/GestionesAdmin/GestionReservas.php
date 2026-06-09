<?php
require_once __DIR__ . '/../clases_admin/GestorUsuarios.php';
require_once __DIR__ . '/../../Clases/Reserva.php';
require_once __DIR__ . '/../../Clases/Barbero.php';
require_once __DIR__ . '/../../Clases/Servicio.php';
require_once __DIR__ . '/../../Clases/Cliente.php';
require_once __DIR__ . '/../../Clases/Horario.php';
ini_set('session.save_path', sys_get_temp_dir());
session_start();

$usuario = GestorUsuarios::obtenerDesdeSesion();
if (!$usuario) {
    header("Location: ../login.php");
    exit;
}

// Procesar acciones de aceptar, cancelar o eliminar reserva
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Procesar eliminación masiva de reservas seleccionadas
    if (isset($_POST['eliminar_seleccionadas']) && !empty($_POST['ids']) && is_array($_POST['ids'])) {
        foreach ($_POST['ids'] as $idEliminar) {
            Reserva::eliminar((int)$idEliminar);
        }
        header('Location: /barberia_catracha/api/admin/GestionesAdmin/GestionReservas.php');
        exit;
    }

    // Procesar la creación rápida de una reserva desde la agenda del panel
    if (isset($_POST['crear_reserva'])) {
        $_SESSION['flash_message'] = Reserva::registrarDesdeFormulario($_POST);

        $redirect = '/barberia_catracha/api/admin/GestionesAdmin/GestionReservas.php';
        $fechaReserva = trim($_POST['fecha'] ?? '');
        if ($fechaReserva !== '') {
            $redirect .= '?fecha_agenda=' . urlencode($fechaReserva);
        }
        header('Location: ' . $redirect);
        exit;
    }
}

// Procesar acciones individuales de aceptar, cancelar o eliminar reserva mediante GET
if (isset($_GET['accion'], $_GET['id'])) {
    $id = $_GET['id'];
    $accion = $_GET['accion'];

    // Validar que el ID es un número entero positivo
    if (!is_numeric($id) || $id <= 0) {
        header('Location: /barberia_catracha/api/admin/GestionesAdmin/GestionReservas.php');
        exit;
    }

    // Validar que el ID es un número entero positivo
    if ($accion === 'aceptar') {
        Reserva::cambiarEstado($id, 'confirmada');
    }

    // Validar que el ID es un número entero positivo
    if ($accion === 'cancelar') {
        Reserva::cambiarEstado($id, 'cancelada');
    }

    // Validar que el ID es un número entero positivo
    if ($accion === 'eliminar') {
        Reserva::eliminar($id);
    }

    header('Location: /barberia_catracha/api/admin/GestionesAdmin/GestionReservas.php');
    exit;
}

$reservas = Reserva::obtenerTodasConDetalles();

// Aplicar filtros por GET: mes, dia, anio
if (!empty($_GET['mes']) || !empty($_GET['dia']) || !empty($_GET['anio'])) {
    // Validar y formatear los filtros para compararlos con las fechas de las reservas
    $mesFiltro = !empty($_GET['mes']) ? str_pad((int)$_GET['mes'], 2, '0', STR_PAD_LEFT) : null;
    $diaFiltro = !empty($_GET['dia']) ? str_pad((int)$_GET['dia'], 2, '0', STR_PAD_LEFT) : null;
    $anioFiltro = !empty($_GET['anio']) ? (int)$_GET['anio'] : null;

    // Filtrar las reservas según los criterios seleccionados
    $reservas = array_filter($reservas, function ($r) use ($mesFiltro, $diaFiltro, $anioFiltro) {
        $ts = strtotime($r['fecha_hora']);
        if ($ts === false) return false;
        $mes = date('m', $ts);
        $dia = date('d', $ts);
        $anio = date('Y', $ts);

        // Validar cada filtro solo si se ha proporcionado. Si el filtro es nulo, no se aplica y se acepta cualquier valor para ese campo.
        if ($mesFiltro !== null && $mes !== $mesFiltro) return false;
        if ($diaFiltro !== null && $dia !== $diaFiltro) return false;
        if ($anioFiltro !== null && (int)$anio !== $anioFiltro) return false;
        return true;
    });
    // reindex
    $reservas = array_values($reservas);
}

// =====================================================================
// Datos para la agenda visual del día (calendario por barbero/horario)
// y para el formulario rápido de "Nueva Reserva"
// =====================================================================
$fechaAgenda = $_GET['fecha_agenda'] ?? date('Y-m-d');
if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $fechaAgenda)) {
    $fechaAgenda = date('Y-m-d');
}

// Un día ya pasado no admite nuevas reservas (ni aunque queden bloques horarios "libres" en la agenda)
$fechaAgendaPasada = $fechaAgenda < date('Y-m-d');

$barberosAgenda = Barbero::obtenerActivos();
$serviciosAgenda = Servicio::obtenerTodos();

// Bloques horarios reservables según el horario configurado para el día (se ajustan solos si se cambia el horario en Gestión de Ubicación)
$bloquesHorarios = Horario::generarBloquesHorarios($fechaAgenda);

// Reservas del día seleccionado, indexadas por barbero y bloque horario
$agendaPorBarberoYHora = Reserva::obtenerAgendaDelDiaPorBarbero($fechaAgenda);

$agendaColores = ['gold', 'azul', 'morado', 'verde'];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Gestión de Reservas</title>
    <link rel="stylesheet" href="../../../assets/style.css">
</head>
<body>

<section class="admin-layout">

    <?php include_once __DIR__ . '/../includes/admin_sidebar.php'; ?>

    <main class="admin-main">
        <p class="admin-small-title">RESERVAS</p>
        <h1>Gestión de Reservas</h1>

        <?php
        $flashMensaje = $_SESSION['flash_message'] ?? '';
        unset($_SESSION['flash_message']);
        ?>
        <div id="flash-message" data-message="<?= htmlspecialchars($flashMensaje, ENT_QUOTES, 'UTF-8') ?>"></div>

        <section class="admin-panel-box agenda-reservas">
            <div class="agenda-cabecera">
                <h2>Agenda del día</h2>
                <button type="button" class="btn agenda-btn-nueva" data-modal-trigger data-date="<?= htmlspecialchars($fechaAgenda) ?>"
                        <?= $fechaAgendaPasada ? 'disabled title="No se pueden crear reservas en un día que ya pasó"' : '' ?>>+ Nueva Reserva</button>
            </div>

            <div class="agenda-toolbar">
                <div class="agenda-date-nav">
                    <button type="button" id="agenda-dia-anterior" class="agenda-nav-btn" aria-label="Día anterior">&lsaquo;</button>
                    <div class="agenda-fecha-actual">
                        <strong><?= ucfirst(Reserva::formatearFechaAgenda($fechaAgenda)) ?></strong>
                        <?php if ($fechaAgenda === date('Y-m-d')): ?><span class="agenda-hoy">Hoy</span><?php endif; ?>
                    </div>
                    <button type="button" id="agenda-dia-siguiente" class="agenda-nav-btn" aria-label="Día siguiente">&rsaquo;</button>
                </div>
                <input type="date" id="agenda-fecha-input" value="<?= htmlspecialchars($fechaAgenda) ?>">
            </div>

            <?php if (empty($barberosAgenda)): ?>
                <p>No hay barberos activos para mostrar la agenda.</p>
            <?php else: ?>

                <div class="agenda-leyenda">
                    <?php foreach ($barberosAgenda as $i => $barberoAgenda): $colorAgenda = $agendaColores[$i % count($agendaColores)]; ?>
                        <span class="agenda-leyenda-item">
                            <i class="agenda-dot agenda-dot-<?= $colorAgenda ?>"></i> <?= htmlspecialchars($barberoAgenda->getNombre()) ?>
                        </span>
                    <?php endforeach; ?>
                    <span class="agenda-leyenda-item agenda-leyenda-libre">
                        <i class="agenda-dot agenda-dot-libre"></i> Libre
                    </span>
                </div>

                <div class="agenda-tabla-wrap">
                    <table class="agenda-tabla">
                        <thead>
                            <tr>
                                <th class="agenda-col-hora">Hora</th>
                                <?php foreach ($barberosAgenda as $i => $barberoAgenda): $colorAgenda = $agendaColores[$i % count($agendaColores)]; ?>
                                    <th>
                                        <i class="agenda-dot agenda-dot-<?= $colorAgenda ?>"></i>
                                        <?= htmlspecialchars($barberoAgenda->getNombre()) ?>
                                    </th>
                                <?php endforeach; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($bloquesHorarios as $bloqueHora): ?>
                                <tr>
                                    <td class="agenda-hora-celda"><?= $bloqueHora ?></td>
                                    <?php foreach ($barberosAgenda as $i => $barberoAgenda):
                                        $colorAgenda = $agendaColores[$i % count($agendaColores)];
                                        $idBarberoAgenda = (int)$barberoAgenda->getBarberoId();
                                        $reservaCelda = $agendaPorBarberoYHora[$idBarberoAgenda][$bloqueHora] ?? null;
                                        $horaYaPaso = $fechaAgendaPasada || ($fechaAgenda === date('Y-m-d') && $bloqueHora < date('H:i'));
                                    ?>
                                        <td class="agenda-celda">
                                            <?php if ($reservaCelda): ?>
                                                <div class="agenda-reserva-card agenda-fondo-<?= $colorAgenda ?>">
                                                    <p class="agenda-reserva-cliente agenda-texto-<?= $colorAgenda ?>"><?= htmlspecialchars(trim($reservaCelda['cliente_nombre'] . ' ' . $reservaCelda['cliente_apellido'])) ?></p>
                                                    <p class="agenda-reserva-servicio"><?= htmlspecialchars($reservaCelda['servicio_nombre']) ?></p>
                                                    <span class="agenda-reserva-estado estado-<?= htmlspecialchars($reservaCelda['estado']) ?>"><?= ucfirst(htmlspecialchars($reservaCelda['estado'])) ?></span>
                                                </div>
                                            <?php elseif ($horaYaPaso): ?>
                                                <button type="button" class="agenda-celda-libre" disabled
                                                        aria-label="Hora ya pasada para <?= htmlspecialchars($barberoAgenda->getNombre()) ?> a las <?= htmlspecialchars($bloqueHora) ?>">+</button>
                                            <?php else: ?>
                                                <button type="button" class="agenda-celda-libre" data-modal-trigger
                                                        data-date="<?= htmlspecialchars($fechaAgenda) ?>"
                                                        data-time="<?= htmlspecialchars($bloqueHora) ?>"
                                                        data-barber-id="<?= $idBarberoAgenda ?>"
                                                        aria-label="Crear reserva para <?= htmlspecialchars($barberoAgenda->getNombre()) ?> a las <?= htmlspecialchars($bloqueHora) ?>">+</button>
                                            <?php endif; ?>
                                        </td>
                                    <?php endforeach; ?>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <div class="agenda-resumen">
                    <?php foreach ($barberosAgenda as $i => $barberoAgenda):
                        $colorAgenda = $agendaColores[$i % count($agendaColores)];
                        $idBarberoAgenda = (int)$barberoAgenda->getBarberoId();
                        $ocupadosAgenda = count($agendaPorBarberoYHora[$idBarberoAgenda] ?? []);
                        $libresAgenda = count($bloquesHorarios) - $ocupadosAgenda;
                    ?>
                        <div class="agenda-resumen-card agenda-borde-<?= $colorAgenda ?>">
                            <div class="agenda-resumen-titulo agenda-texto-<?= $colorAgenda ?>">
                                <i class="agenda-dot agenda-dot-<?= $colorAgenda ?>"></i> <?= htmlspecialchars($barberoAgenda->getNombre()) ?>
                            </div>
                            <strong><?= $libresAgenda ?></strong>
                            <p>huecos libres</p>
                            <span><?= $ocupadosAgenda ?> ocupados</span>
                        </div>
                    <?php endforeach; ?>
                </div>

            <?php endif; ?>
        </section>

        <section class="admin-panel-box">

            <?php if (empty($reservas)): ?>
                <p>No hay reservas registradas.</p>
            <?php else: ?>

                <form method="get" class="filtros-reservas" style="margin-bottom:12px; display:flex; gap:10px; align-items:center; flex-wrap:wrap;">
                    <label>Mes:
                        <select name="mes">
                            <option value="">Todos</option>
                            // Generar opciones de mes del 1 al 12 con formato de dos dígitos para mostrar en el select
                            <?php for ($m=1;$m<=12;$m++): $val=str_pad($m,2,'0',STR_PAD_LEFT); ?>
                                <option value="<?= $m ?>" <?= (isset($_GET['mes']) && (int)$_GET['mes']===$m)?'selected':'' ?>><?= $val ?></option>
                            <?php endfor; ?>
                        </select>
                    </label>

                    <label>Día:
                        <select name="dia">
                            <option value="">Todos</option>
                            // Generar opciones de día del 1 al 31 con formato de dos dígitos para mostrar en el select
                            <?php for ($d=1;$d<=31;$d++): $dv=str_pad($d,2,'0',STR_PAD_LEFT); ?>
                                <option value="<?= $d ?>" <?= (isset($_GET['dia']) && (int)$_GET['dia']===$d)?'selected':'' ?>><?= $dv ?></option>
                            <?php endfor; ?>
                        </select>
                    </label>

                    <label>Año:
                        <select name="anio">
                            <option value="">Todos</option>
                            // Generar opciones de año desde el año actual hasta 5 años atrás para mostrar en el select
                            <?php $currentYear = (int)date('Y'); for ($y=$currentYear; $y>=($currentYear-5); $y--): ?>
                                <option value="<?= $y ?>" <?= (isset($_GET['anio']) && (int)$_GET['anio']===$y)?'selected':'' ?>><?= $y ?></option>
                            <?php endfor; ?>
                        </select>
                    </label>

                    <button type="submit" class="btn">Filtrar</button>
                </form>

                <form method="post" id="reservas-form">
                    <div style="margin-bottom:8px; display:flex; gap:8px; align-items:center;">
                        <button type="submit" name="eliminar_seleccionadas" onclick="return confirm('¿Eliminar reservas seleccionadas?')" class="btn btn-danger">Eliminar seleccionadas</button>
                    </div>
                    
                    <div class="table-responsive">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th><input type="checkbox" id="select-all"></th>
                                <th>Cliente</th>
                                <th>Barbero</th>
                                <th>Servicio</th>
                                <th>Fecha</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php foreach ($reservas as $reserva): ?>
                                <tr>
                                    <td><input type="checkbox" name="ids[]" value="<?= (int)$reserva['reserva_id'] ?>" class="row-checkbox"></td>
                                    <td><?= htmlspecialchars($reserva['cliente']) ?></td>
                                    <td><?= htmlspecialchars($reserva['barbero']) ?></td>
                                    <td><?= htmlspecialchars($reserva['servicio']) ?></td>
                                    <td><?= htmlspecialchars($reserva['fecha_hora']) ?></td>
                                    <td><span class="estado estado-<?= htmlspecialchars($reserva['estado']) ?>"><?= ucfirst(htmlspecialchars($reserva['estado'])) ?></span></td>
                                    <td>
                                        <?php if ($reserva['estado'] === 'pendiente'): ?>
                                            <a href="?accion=aceptar&id=<?= $reserva['reserva_id'] ?>">Aceptar</a>
                                            <a href="?accion=cancelar&id=<?= $reserva['reserva_id'] ?>">Cancelar</a>
                                            <a href="?accion=eliminar&id=<?= $reserva['reserva_id'] ?>" onclick="return confirm('¿Eliminar reserva?')">Eliminar</a>
                                        <?php elseif ($reserva['estado'] === 'confirmada'): ?>
                                            <span class="texto-aceptada">Aceptada</span>
                                            <a href="?accion=eliminar&id=<?= $reserva['reserva_id'] ?>" onclick="return confirm('¿Eliminar reserva?')">Eliminar</a>
                                        <?php else: ?>
                                            <span class="texto-cancelada">Cancelada</span>
                                            <a href="?accion=eliminar&id=<?= $reserva['reserva_id'] ?>" onclick="return confirm('¿Eliminar reserva?')">Eliminar</a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    </div>
                </form>

            <?php endif; ?>

        </section>

        <div id="modal-nueva-reserva" class="modal-overlay" hidden>
            <div class="modal-box">
                <div class="modal-header">
                    <div>
                        <h2>Nueva Reserva</h2>
                        <p>Añadir reserva desde el panel</p>
                    </div>
                    <button type="button" class="modal-close" id="cerrar-modal-reserva" aria-label="Cerrar">&times;</button>
                </div>

                <form method="post" id="form-nueva-reserva" class="modal-body">
                    <input type="hidden" name="crear_reserva" value="1">

                    <div class="modal-section">
                        <p class="modal-section-titulo">Datos del cliente</p>
                        <div class="input-box">
                            <label>Nombre *</label>
                            <input type="text" name="client_nombre" id="nr-nombre" placeholder="Nombre del cliente" required>
                        </div>
                        <div class="input-box">
                            <label>Apellido *</label>
                            <input type="text" name="client_apellido" id="nr-apellido" placeholder="Apellido del cliente" required>
                        </div>
                        <div class="input-box">
                            <label>Teléfono *</label>
                            <input type="tel" name="client_phone" id="nr-telefono" placeholder="Ej: 9999-9999" required>
                        </div>
                        <div class="input-box">
                            <label>Correo</label>
                            <input type="email" name="client_email" id="nr-email" placeholder="correo@ejemplo.com">
                        </div>
                    </div>

                    <div class="modal-section">
                        <p class="modal-section-titulo">Servicio *</p>
                        <div class="modal-opciones-grid">
                            <?php foreach ($serviciosAgenda as $servicioModal): ?>
                                <button type="button" class="modal-opcion-btn nr-servicio-btn" data-id="<?= $servicioModal->getServicioId() ?>">
                                    <span class="modal-opcion-nombre"><?= htmlspecialchars($servicioModal->getNombre()) ?></span>
                                    <span class="modal-opcion-precio"><?= number_format($servicioModal->getPrecio(), 2) ?> €</span>
                                </button>
                            <?php endforeach; ?>
                        </div>
                        <input type="hidden" name="servicio_id" id="nr-servicio-id">
                    </div>

                    <div class="modal-section">
                        <p class="modal-section-titulo">Barbero *</p>
                        <div class="modal-barberos-grid">
                            <?php foreach ($barberosAgenda as $barberoModal): ?>
                                <button type="button" class="modal-opcion-btn nr-barbero-btn" data-id="<?= $barberoModal->getBarberoId() ?>">
                                    <?= htmlspecialchars($barberoModal->getNombre()) ?>
                                </button>
                            <?php endforeach; ?>
                        </div>
                        <input type="hidden" name="barbero_id" id="nr-barbero-id">
                    </div>

                    <div class="modal-section">
                        <p class="modal-section-titulo">Fecha y Hora *</p>
                        <input type="date" name="fecha" id="nr-fecha" min="<?= date('Y-m-d') ?>" required>
                        <div class="modal-horas-grid" id="nr-horas-grid">
                            <?php foreach ($bloquesHorarios as $horaModal): ?>
                                <button type="button" class="modal-hora-btn" data-time="<?= $horaModal ?>"><?= $horaModal ?></button>
                            <?php endforeach; ?>
                        </div>
                        <p class="modal-aviso-sin-horarios" id="nr-aviso-sin-horarios" hidden>No quedan horarios disponibles para este día.</p>
                        <input type="hidden" name="hora" id="nr-hora">
                    </div>

                    <div class="modal-footer">
                        <button type="button" id="cancelar-modal-reserva" class="btn-cancelar-modal">Cancelar</button>
                        <button type="submit" id="confirmar-modal-reserva" class="btn-confirmar-modal" disabled>Confirmar Reserva</button>
                    </div>
                </form>
            </div>
        </div>
    </main>

</section>

        </body>
        </html>