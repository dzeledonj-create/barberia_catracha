<?php
require_once '../clases/BD.php';
require_once '../clases/Servicio.php';

// 1. Obtener servicios y agruparlos por la columna 'limite'
$serviciosRaw = Servicio::obtenerTodos();
$bloques = [];

foreach ($serviciosRaw as $s) {
    // Si el servicio no tiene 'limite', lo mandamos a una sección general
    $categoria = $s['limite'] ?: 'otros'; 
    $bloques[$categoria][] = $s;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Servicios - Barbería Catracha</title>
    <link rel="stylesheet" href="../assets/style.css">
    <link rel="stylesheet" href="../assets/servicios-style.css">
</head>
<body class="servicios-body">

<?php if (file_exists('../includes/header.php')) include_once '../includes/header.php'; ?>

<main class="servicios-main">
    <header class="servicios-header-top">
        <p class="subtitle">LISTA DE PRECIOS PREMIUM</p>
        <h1 class="titulo-principal">Nuestros Servicios</h1>
        <span class="underline"></span>
    </header>

    <!-- 2. Generar un bloque por cada categoría de 'limite' -->
    <?php foreach ($bloques as $nombreBloque => $listaServicios): ?>
        <section class="categoria-bloque">
            <h2 class="categoria-nombre"><?= strtoupper(str_replace('_', ' ', $nombreBloque)) ?></h2>

            <nav class="items-list">
                <?php foreach ($listaServicios as $s): ?>
                    <article class="item-row">
                        <header class="item-info">
                            <h3>
                                <?= strtoupper($s['nombre']) ?>
                                <?php if ($s['nombre'] == 'Tinte de colores'): ?> <small>(SOLO LUN, MAR Y MIÉ)</small> <?php endif; ?>
                                <?php if ($s['nombre'] == 'Permanente'): ?> <small>(LUN A MIÉ)</small> <?php endif; ?>
                            </h3>
                            <p class="descripcion"><?= $s['descripcion'] ?></p>
                        </header>

                        <aside class="item-precio">
                            <span><?= number_format($s['precio'], 2) ?>€ / <?= $s['duracion_minutos'] ?> MIN</span>
                        </aside>
                    </article>
                <?php endforeach; ?>
            </nav>
        </section>
    <?php endforeach; ?>

    <footer class="categoria-cta">
        <a href="reservas.php" class="btn-reserva-premium">RESERVAR CITA AHORA</a>
    </footer>

</main>
</body>
</html>