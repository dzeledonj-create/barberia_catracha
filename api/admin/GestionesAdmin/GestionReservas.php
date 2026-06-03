<?php
require_once __DIR__ . '/../clases_admin/GestorUsuarios.php';

$usuario = GestorUsuarios::obtenerDesdeSesion();
if (!$usuario) {
    header("Location: ../login.php");
    exit;
}

require_once __DIR__ . '/../../Clases/Reserva.php';

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
                </form>

            <?php endif; ?>

        </section>
    </main>

</section>

        </body>
        </html>