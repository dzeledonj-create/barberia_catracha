<?php
require_once __DIR__ . '/../../Clases/BD.php';
require_once __DIR__ . '/../../Clases/Reserva.php';
require_once __DIR__ . '/../clases_admin/GestorUsuarios.php';
ini_set('session.save_path', sys_get_temp_dir());
session_start();

// ¡AÑADIMOS SEGURIDAD AL PANEL! Solo los administradores y barberos pueden acceder
$usuario = GestorUsuarios::obtenerDesdeSesion();
if (!$usuario) {
    header("Location: ../login.php");
    exit;
}

// App ID de OneSignal para inicializar el SDK de Web Push en el navegador del barbero/
// administrador. Es un identificador público (no la clave privada REST API), por lo
// que es seguro incluirlo en el HTML. Vive en config_notificaciones.php (fuera de git).
$configuracionNotificaciones = __DIR__ . '/../../config_notificaciones.php';
$oneSignalAppId = '';
if (is_file($configuracionNotificaciones)) {
    require_once $configuracionNotificaciones;
    $oneSignalAppId = defined('ONESIGNAL_APP_ID') ? ONESIGNAL_APP_ID : '';
}

// total de reservas por estado para mostrar estadísticas en el panel
$totalReservas = Reserva::contarPorEstado('pendiente') + Reserva::contarPorEstado('confirmada') + Reserva::contarPorEstado('cancelada');
$pendientes = Reserva::contarPorEstado('pendiente');
$confirmadas = Reserva::contarPorEstado('confirmada');
$canceladas = Reserva::contarPorEstado('cancelada');
$recientesReservas = Reserva::obtenerRecientes(5);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Panel Admin</title>
    <link rel="stylesheet" href="../../../assets/style.css">

    <?php if ($oneSignalAppId !== ''): ?>
    <!-- SDK de OneSignal: se inicializa aquí, en el panel del barbero/administrador,
         para que pueda suscribirse a las notificaciones Web Push de nuevas reservas. -->
    <script src="https://cdn.onesignal.com/sdks/web/v16/OneSignalSDK.page.js" defer></script>
    <script>
        window.OneSignalDeferred = window.OneSignalDeferred || [];
        OneSignalDeferred.push(async function (OneSignal) {
            await OneSignal.init({
                appId: '<?= htmlspecialchars($oneSignalAppId, ENT_QUOTES) ?>',
                serviceWorkerPath: '<?= BASE_PATH ?>/OneSignalSDKWorker.js',
            });
        });
    </script>
    <?php endif; ?>
</head>
<body>

<section class="admin-layout">

    <?php include_once __DIR__ . '/../includes/admin_sidebar.php'; ?>

    <main class="admin-main">
        <p class="admin-small-title">DASHBOARD</p>

        <h1>Bienvenido al Panel</h1>
        <p class="admin-subtitle">Gestiona toda la barbería desde aquí.</p>

        <?php if ($oneSignalAppId !== ''): ?>
            <section class="admin-actions">
                <button type="button" class="btn-activar-onesignal" id="btn-activar-onesignal">
                    Activar notificaciones
                </button>
            </section>
        <?php endif; ?>

        <?php if ($pendientes > 0): ?>
            <section class="admin-alert">
                <strong>Notificación:</strong> Tienes <?= htmlspecialchars($pendientes) ?> reserva<?= $pendientes === 1 ? '' : 's' ?> pendiente<?= $pendientes === 1 ? '' : 's' ?>.
                <a href="GestionReservas.php">Ver reservas</a>
            </section>
        <?php endif; ?>

        <section class="admin-stats">
            <section class="admin-card">
                <span>Total reservas</span>
                <strong><?= htmlspecialchars($totalReservas) ?></strong>
            </section>

            <section class="admin-card">
                <span>Pendientes</span>
                <strong><?= htmlspecialchars($pendientes) ?></strong>
            </section>

            <section class="admin-card">
                <span>Confirmadas</span>
                <strong><?= htmlspecialchars($confirmadas) ?></strong>
            </section>

            <section class="admin-card">
                <span>Canceladas</span>
                <strong><?= htmlspecialchars($canceladas) ?></strong>
            </section>
        </section>

        <section class="admin-panel-box">
            <h2>Reservas recientes</h2>
            <?php if (empty($recientesReservas)): ?>
                <p>No hay reservas todavía.</p>
            <?php else: ?>
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Cliente</th>
                            <th>Barbero</th>
                            <th>Servicio</th>
                            <th>Fecha</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recientesReservas as $reserva): ?>
                            <tr>
                                <td><?= htmlspecialchars($reserva['reserva_id']) ?></td>
                                <td><?= htmlspecialchars($reserva['cliente']) ?></td>
                                <td><?= htmlspecialchars($reserva['barbero']) ?></td>
                                <td><?= htmlspecialchars($reserva['servicio']) ?></td>
                                <td><?= htmlspecialchars($reserva['fecha_hora']) ?></td>
                                <td><span class="estado estado-<?= htmlspecialchars($reserva['estado']) ?>"><?= ucfirst(htmlspecialchars($reserva['estado'])) ?></span></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </section>

        <?php if ($usuario instanceof UsuarioBarbero): ?>
        <section class="admin-actions">
            <a href="GestionReservas.php">Ver Reservas</a>
        </section>
        <?php else: ?>
        <section class="admin-actions">
            <a href="GestionServicios.php">Gestionar Servicios</a>
            <a href="GestionEquipo.php">Gestionar Equipo</a>
            <a href="GestionReservas.php">Ver Reservas</a>
            <a href="GestionGaleria.php">Gestionar Galería</a>
            <a href="GestionBlog.php">Editar Blog</a>
            <a href="GestionUbicacion.php">Actualizar Ubicación</a>
        </section>
        <?php endif; ?>
    </main>

</section>

</body>
</html>
