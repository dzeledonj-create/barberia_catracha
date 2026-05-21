<?php
require_once '../clases/BD.php';
require_once '../clases/Barbero.php';
require_once '../clases/Servicio.php';

$servicios = Servicio::obtenerTodos();
$barberos = Barbero::obtenerActivos(); // Devuelve arrays por el FETCH_ASSOC
$db = BD::obtenerConexion();

$stmt = $db->query("SELECT barbero_id, servicio_id FROM barbero_servicio");
$relaciones = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reservas - Barbería Catracha</title>
    <link rel="stylesheet" href="../assets/style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="icon" href="/barberia_catracha/assets/img/logo.png" type="image/png">
</head>
<body>

<?php include_once '../includes/header.php'; ?>

<section class="reservas-page">
    <div class="reservas-container">
        <h1>Reserva tu Cita</h1>

        <div class="pasos-bar">
            <div class="paso-item activo" data-paso="0"><span class="paso-numero">1</span><span class="paso-texto">Servicio</span></div>
            <div class="paso-item" data-paso="1"><span class="paso-numero">2</span><span class="paso-texto">Barbero</span></div>
            <div class="paso-item" data-paso="2"><span class="paso-numero">3</span><span class="paso-texto">Fecha y Hora</span></div>
            <div class="paso-item" data-paso="3"><span class="paso-numero">4</span><span class="paso-texto">Tus Datos</span></div>
            <div class="paso-item" data-paso="4"><span class="paso-numero">5</span><span class="paso-texto">Confirmación</span></div>
        </div>

        <form id="form-reserva" action="procesar_reserva.php" method="POST">
            
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
                        // Lógica automática para asignar categoría temporal basándonos en el nombre
                        $nombreLower = mb_strtolower($servicio['nombre']);
                        $descLower = mb_strtolower($servicio['descripcion']);
                        
                        $cat = 'otros';
                        if (str_contains($nombreLower, 'corte') || str_contains($nombreLower, 'pelo') || str_contains($nombreLower, 'cejas')) {
                            $cat = 'corte';
                        }
                        if (str_contains($nombreLower, 'barba')) {
                            // Si tiene corte y barba, se puede quedar en combos/otros o barba
                            $cat = (str_contains($nombreLower, 'corte')) ? 'otros' : 'barba';
                        }
                        if (str_contains($nombreLower, 'tinte') || str_contains($nombreLower, 'color') || str_contains($nombreLower, 'mechas')) {
                            $cat = 'tinte';
                        }
                    ?>
                        <label class="card-option servicio-card" data-cat="<?= $cat ?>">
                            <input type="radio" name="servicio_id" value="<?= $servicio['servicio_id'] ?>" required>
                            <div class="card-content">
                                <div class="card-info">
                                    <h3><?= htmlspecialchars($servicio['nombre']) ?></h3>
                                    <p><?= htmlspecialchars($servicio['descripcion']) ?></p>
                                </div>
                                <div class="card-meta">
                                    <span class="precio"><?= number_format($servicio['precio'], 2) ?> €</span>
                                    <span class="duracion"><?= $servicio['duracion_minutos'] ?> min</span>
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
                <h2>Selecciona tu barbero</h2>
                <div class="barberos-grid">
                    <?php foreach ($barberos as $barbero): ?>
                        <label class="card-option barbero-card" data-id="<?= $barbero['barbero_id'] ?>">
                            <input type="radio" name="barbero_id" value="<?= $barbero['barbero_id'] ?>" required>
                            <div class="card-content-barbero">
                                <img src="<?= htmlspecialchars($barbero['foto_url'] ?? '../assets/img/default-avatar.png') ?>" alt="<?= htmlspecialchars($barbero['nombre']) ?>" class="barbero-img">
                                <h3><?= htmlspecialchars($barbero['nombre']) ?></h3>
                                <span class="especialidad"><?= htmlspecialchars($barbero['especialidad'] ?? 'Barbero Profesional') ?></span>
                            </div>
                        </label>
                    <?php endforeach; ?>
                </div>
                <div class="nav-buttons">
                    <button type="button" class="btn-atras">Atrás</button>
                    <button type="button" class="btn-siguiente">Siguiente Paso</button>
                </div>
            </div>

            <div class="reserva-step" id="step-3">
                <h2>Elige fecha y hora</h2>
                <div class="agenda-container">
                    <div class="calendario-box">
                        <input type="text" id="datepicker" name="fecha" placeholder="Selecciona una fecha" readonly required>
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
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/es.js"></script>
<script src="../assets/script.js"></script>
</body>
</html>