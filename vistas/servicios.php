<?php
require_once '../clases/BD.php';

// 1. Traemos TODOS los servicios de la base de datos una sola vez
$sql = "SELECT * FROM servicios ORDER BY servicio_id ASC";
$res = mysqli_query($conexion, $sql);

// 2. Preparamos los contenedores para organizar la información
$categorias = [
    'SERVICIOS POPULARES' => [],
    'OTROS SERVICIOS'     => [],
    'BARBA'               => [],
    'CEJAS'               => [],
    'COLOR'               => [],
    'CORTE Y BARBA'       => [],
    'CORTE'               => [],
    'PERMANENTE'          => []
];

// 3. Clasificamos los servicios automáticamente según su nombre en el SQL
while ($s = mysqli_fetch_assoc($res)) {
    $nombre = $s['nombre'];
    $nombre_lower = mb_strtolower($nombre);

    // Lógica para llenar 'Servicios Populares' (según tu criterio de imagen)
    if (in_array($nombre, ['Corte', 'Corte y barba', 'Barba'])) {
        $categorias['SERVICIOS POPULARES'][] = $s;
    }

    // Clasificación por palabras clave
    if (strpos($nombre_lower, 'barba') !== false) {
        $categorias['BARBA'][] = $s;
    }
    if (strpos($nombre_lower, 'cejas') !== false) {
        $categorias['CEJAS'][] = $s;
    }
    if (strpos($nombre_lower, 'tinte') !== false) {
        $categorias['COLOR'][] = $s;
    }
    if (strpos($nombre_lower, 'corte') !== false) {
        $categorias['CORTE'][] = $s;
    }
    if ($nombre == 'Corte y cejas con hilo') {
        $categorias['OTROS SERVICIOS'][] = $s;
    }
    if ($nombre == 'Permanente') {
        $categorias['PERMANENTE'][] = $s;
    }
    
    // Clasificación específica para el bloque 'Corte y Barba'
    if ($nombre == 'Corte y barba' || $nombre == 'Corte, barba y tinte De Pelo') {
        $categorias['CORTE Y BARBA'][] = $s;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Servicios - Barbería Catracha</title>
    <link rel="stylesheet" href="../assets/style.css">
    <link rel="stylesheet" href="../assets/servicios-style.css">
</head>
<body class="servicios-body">

<?php include_once '../includes/header.php'; ?>

<main class="servicios-main">
    <header class="servicios-header-top">
        <p class="subtitle">LISTA DE PRECIOS PREMIUM</p>
        <h1 class="titulo-principal">Nuestros Servicios</h1>
        <span class="underline"></span>
    </header>

    <?php foreach ($categorias as $titulo => $lista_servicios): ?>
        <?php if (!empty($lista_servicios)): ?>
            <section class="categoria-bloque <?php echo ($titulo === 'SERVICIOS POPULARES') ? 'destacado' : ''; ?>">
                <h2 class="categoria-nombre"><?php echo $titulo; ?></h2>
                <nav class="items-list">
                    <?php foreach ($lista_servicios as $datos): ?>
                        <article class="item-row">
                            <header class="item-info">
                                <h3>
                                    <?php echo strtoupper($datos['nombre']); ?>
                                    <?php 
                                        // Notas de horario automáticas
                                        if ($datos['nombre'] == 'Tinte de colores') echo " <small>(SOLO LUN, MAR Y MIÉ)</small>";
                                        if ($datos['nombre'] == 'Permanente') echo " <small>(DE LUNES A MIÉRCOLES)</small>";
                                    ?>
                                </h3>
                            </header>
                            <aside class="item-precio">
                                <span><?php echo number_format($datos['precio'], 0); ?>€ / <?php echo $datos['duracion_minutos']; ?> MIN</span>
                            </aside>
                        </article>
                    <?php endforeach; ?>
                </nav>

                <?php if ($titulo === 'SERVICIOS POPULARES'): ?>
                    <footer class="categoria-cta">
                        <a href="reservas.php" class="btn-reserva-premium">RESERVAR ESTOS SERVICIOS POPULARES</a>
                    </footer>
                <?php endif; ?>
            </section>
        <?php endif; ?>
    <?php endforeach; ?>
</main>

</body>
</html>