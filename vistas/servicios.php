<?php
require_once '../clases/BD.php';

try {
    // Así es como se obtiene la conexión según tu archivo BD.php
    $conexion = BD::obtenerConexion();
} catch (Exception $e) {
    die("Error de conexión: " . $e->getMessage());
}

// 1. Traemos todos los servicios (Usando PDO que es lo que tiene tu BD.php)
$sql_all = "SELECT * FROM servicios";
$stmt = $conexion->prepare($sql_all);
$stmt->execute();
$resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

$servicios_db = [];
foreach ($resultados as $row) {
    $servicios_db[$row['nombre']] = $row;
}

// 2. Estructura de categorías (Mantenla igual)
$categorias = [
    'SERVICIOS POPULARES' => ['Corte', 'Corte y barba', 'Barba'],
    'OTROS SERVICIOS'     => ['Corte y cejas con hilo'],
    'BARBA'               => ['Barba', 'Barba y tinte'],
    'CEJAS'               => ['Cejas con cuchilla', 'Cejas con hilo'],
    'COLOR'               => ['Tinte negro', 'Tinte de colores'],
    'CORTE Y BARBA'       => ['Corte y barba', 'Corte, barba y tinte De Pelo'],
    'CORTE'               => ['Corte', 'Corte para jubilados', 'Corte y diseño'],
    'PERMANENTE'          => ['Permanente']
];
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

<?php 
// Asegúrate de que la ruta al header también sea correcta
if (file_exists('../includes/header.php')) {
    include_once '../includes/header.php'; 
}
?>

<main class="servicios-main">
    <header class="servicios-header-top">
        <p class="subtitle">LISTA DE PRECIOS PREMIUM</p>
        <h1 class="titulo-principal">Nuestros Servicios</h1>
        <span class="underline"></span>
    </header>

    <?php foreach ($categorias as $titulo => $nombres): ?>
        <section class="categoria-bloque <?php echo ($titulo === 'SERVICIOS POPULARES') ? 'destacado' : ''; ?>">
            <h2 class="categoria-nombre"><?php echo $titulo; ?></h2>
            <nav class="items-list">
                <?php foreach ($nombres as $n): ?>
                    <?php if (isset($servicios_db[$n])): 
                        $s = $servicios_db[$n]; ?>
                        <article class="item-row">
                            <header class="item-info">
                                <h3>
                                    <?php echo strtoupper($s['nombre']); ?>
                                    <?php 
                                        if ($s['nombre'] == 'Tinte de colores') echo " <small>(SOLO LUN, MAR Y MIÉ)</small>";
                                        if ($s['nombre'] == 'Permanente') echo " <small>(LUN A MIÉ)</small>";
                                    ?>
                                </h3>
                            </header>
                            <aside class="item-precio">
                                <span><?php echo number_format($s['precio'], 0); ?>€ / <?php echo $s['duracion_minutos']; ?> MIN</span>
                            </aside>
                        </article>
                    <?php endif; ?>
                <?php endforeach; ?>
            </nav>

            <?php if ($titulo === 'SERVICIOS POPULARES'): ?>
                <footer class="categoria-cta">
                    <a href="reservas.php" class="btn-reserva-premium">RESERVAR ESTOS SERVICIOS POPULARES</a>
                </footer>
            <?php endif; ?>
        </section>
    <?php endforeach; ?>
</main>
</body>
</html>