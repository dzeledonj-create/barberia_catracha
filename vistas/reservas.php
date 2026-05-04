<?php
require_once '../clases/Servicio.php';
require_once '../clases/Barbero.php';

$servicios = Servicio::obtenerTodos();
$barberos = Barbero::obtenerActivos();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reservas - Barbería Catracha</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>

<?php include_once '../includes/header.php'; ?>

<section class="reservas-page">

    <section class="reservas-container">

        <h1>Reserva tu Cita</h1>

        <section class="pasos-reserva">
            <span class="paso activo">1</span>
            <span class="paso">2</span>
            <span class="paso">3</span>
            <span class="paso">4</span>
            <span class="paso">5</span>
        </section>

        <form method="POST" action="../controladores/guardar_reserva.php" class="form-reserva">

            <!-- PASO 1: SERVICIO -->
            <section class="reserva-step activo" id="step-1">
                <h2>Elige tu servicio</h2>

                <?php foreach ($servicios as $servicio): ?>
                    <label class="opcion-reserva">
                        <input type="radio" name="servicio_id" value="<?= $servicio['servicio_id'] ?>" required>
                        <section>
                            <strong><?= $servicio['nombre'] ?></strong>
                            <small><?= $servicio['duracion_minutos'] ?> min</small>
                        </section>
                        <span><?= number_format($servicio['precio'], 2) ?> €</span>
                    </label>
                <?php endforeach; ?>

                <button type="button" class="btn-siguiente">Siguiente</button>
            </section>

            <!-- PASO 2: BARBERO -->
            <section class="reserva-step" id="step-2">
                <h2>Elige tu barbero</h2>

                <?php foreach ($barberos as $barbero): ?>
                    <label class="opcion-barbero">
                        <input type="radio" name="barbero_id" value="<?= $barbero['barbero_id'] ?>" required>
                        <img src="../<?= $barbero['foto_url'] ?>" alt="<?= $barbero['nombre'] ?>">
                        <section>
                            <strong><?= $barbero['nombre'] ?></strong>
                            <small><?= $barbero['especialidad'] ?></small>
                        </section>
                    </label>
                <?php endforeach; ?>

                <button type="button" class="btn-atras">Atrás</button>
                <button type="button" class="btn-siguiente">Siguiente</button>
            </section>

            <!-- PASO 3: FECHA Y HORA -->
            <section class="reserva-step" id="step-3">
                <h2>Elige fecha y hora</h2>

                <input type="datetime-local" name="fecha_hora" required>

                <button type="button" class="btn-atras">Atrás</button>
                <button type="button" class="btn-siguiente">Siguiente</button>
            </section>

            <!-- PASO 4: DATOS CLIENTE -->
            <section class="reserva-step" id="step-4">
                <h2>Tus datos</h2>

                <input type="text" name="nombre" placeholder="Nombre" required>
                <input type="text" name="apellido" placeholder="Apellido" required>
                <input type="tel" name="telefono" placeholder="Teléfono" required>
                <input type="email" name="email" placeholder="Email">

                <button type="button" class="btn-atras">Atrás</button>
                <button type="button" class="btn-siguiente">Siguiente</button>
            </section>

            <!-- PASO 5: CONFIRMACIÓN -->
            <section class="reserva-step" id="step-5">
                <h2>Confirmar reserva</h2>

                <p>Revisa los datos antes de confirmar tu cita.</p>

                <button type="button" class="btn-atras">Atrás</button>
                <button type="submit" class="btn-confirmar">Confirmar reserva</button>
            </section>

        </form>

    </section>

</section>

<script src="../assets/script.js"></script>
<?php include_once '../includes/footer.php'; ?>

</body>
</html>