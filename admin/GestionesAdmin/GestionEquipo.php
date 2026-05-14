<?php
require_once '../../clases/Barbero.php';
require_once '../clases_admin/GestorUsuarios.php';

$usuario = GestorUsuarios::obtenerDesdeSesion();
if (!$usuario instanceof Administrador) {
    header("Location: ../../login.php");
    exit;
}

$barberos = Barbero::obtenerTodos(); // Traemos todos para gestionar activos e inactivos

// Lógica simple para detectar si estamos editando
$editandoId = $_GET['editar'] ?? null;
$barberoAEditar = $editandoId ? Barbero::obtenerPorId($editandoId) : null;
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel Admin - Gestión de Equipo</title>
    <link rel="stylesheet" href="../../assets/style.css">
</head>
<body class="admin-panel">
    <?php include_once '../includes/admin_sidebar.php'; ?>

    <main class="content">
        <header class="admin-header-main">
            <h1>Gestión del Equipo</h1>
            <p>Añade, edita o elimina miembros de tu equipo</p>
        </header>

        <section class="add-member-panel">
            <div class="panel-header">
                <h2>NUEVO MIEMBRO</h2>
                <div class="line-gold"></div>
            </div>
            
            <form action="../../Clases/barbero.php" method="POST" class="add-form-grid">
                <input type="hidden" name="accion" value="crear">
                
                <div class="form-row">
                    <div class="input-group">
                        <label>Nombre Completo</label>
                        <input type="text" name="nombre" placeholder="Ej: Juan Pérez" required>
                    </div>
                    <div class="input-group">
                        <label>Especialidad / Rango</label>
                        <input type="text" name="especialidad" placeholder="Ej: Master Barber">
                    </div>
                    <div class="input-group">
                        <label>Rol de Sistema</label>
                        <select name="rol" required>
                            <option value="barbero">Usuario Barbero</option>
                            <option value="admin">Administrador</option>
                        </select>
                    </div>
                </div>

                <div class="form-row">
                    <div class="input-group">
                        <label>Email (para el login)</label>
                        <input type="email" name="email" placeholder="email@ejemplo.com" required>
                    </div>
                    <div class="input-group">
                        <label>Contraseña Provisional</label>
                        <input type="password" name="password" placeholder="****" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="input-group">
                        <label>URL de la Foto</label>
                        <input type="text" name="foto_url" placeholder="assets/img/equipo/foto.jpg">
                    </div>
                    <div class="input-group">
                        <label>Etiquetas (separadas por coma)</label>
                        <input type="text" name="etiquetas" placeholder="Corte, Barba, Estilo">
                    </div>
                </div>

                <div class="input-group full-width">
                    <label>Descripción / Biografía</label>
                    <textarea name="descripcion" rows="2" placeholder="Describe brevemente al barbero..."></textarea>
                </div>

                <div class="form-footer">
                    <button type="submit" class="btn-save-new">AÑADIR AL EQUIPO</button>
                </div>
            </form>
        </section>

        <hr class="separator-gold">

        <section class="equipo-grid">
            <?php foreach ($barberos as $b_data): 
                $b = Barbero::obtenerPorId($b_data['barbero_id']);
            ?>
                <section class="barbero-card <?= ($editandoId == $b->barberoId) ? 'editing' : '' ?>">
                    <?php if ($editandoId == $b->barberoId): ?>
                        <form action="../../Clases/barbero.php" method="POST" class="edit-form">
                            <input type="hidden" name="accion" value="editar">
                            <input type="hidden" name="barbero_id" value="<?= $b->barberoId ?>">
                            
                            <label>Nombre</label>
                            <input type="text" name="nombre" value="<?= htmlspecialchars($b->nombre) ?>">
                            
                            <label>Especialidad</label>
                            <input type="text" name="especialidad" value="<?= htmlspecialchars($b->especialidad) ?>">
                            
                            <label>Rol de Sistema</label>
                            <select name="rol">
                                <option value="barbero" <?= ($b->rol == 'barbero') ? 'selected' : '' ?>>Usuario Barbero</option>
                                <option value="admin" <?= ($b->rol == 'admin') ? 'selected' : '' ?>>Administrador</option>
                            </select>

                            <label>Descripción</label>
                            <textarea name="descripcion" rows="3"><?= htmlspecialchars($b->descripcion) ?></textarea>
                            
                            <label>Etiquetas</label>
                            <input type="text" name="etiquetas" value="<?= htmlspecialchars($b->etiquetas) ?>">
                            
                            <label>Foto URL</label>
                            <input type="text" name="foto_url" value="<?= htmlspecialchars($b->fotoUrl) ?>">

                            <section class="form-buttons">
                                <button type="submit" class="btn-save">GUARDAR</button>
                                <a href="GestionEquipo.php" class="btn-cancel">CANCELAR</a>
                            </section>
                        </form>
                    <?php else: ?>
                        <div class="card-image">
                             <img src="../../<?= htmlspecialchars($b->fotoUrl) ?>" alt="<?= htmlspecialchars($b->nombre) ?>" onerror="this.src='../../assets/img/default-user.jpg'">
                        </div>
                        <section class="info">
                            <h3><?= htmlspecialchars($b->nombre) ?></h3>
                            <p class="rank"><?= htmlspecialchars($b->especialidad) ?></p>
                            <p class="role-text"><?= strtoupper($b->rol ?? 'Barbero') ?></p>
                            
                            <div class="actions-group">
                                <a href="?editar=<?= $b->barberoId ?>" class="btn-edit">EDITAR</a>
                                
                                <form action="../../Clases/barbero.php" method="POST" class="delete-form" onsubmit="return confirm('¿Estás seguro de que quieres eliminar a este miembro?');">
                                    <input type="hidden" name="accion" value="eliminar">
                                    <input type="hidden" name="barbero_id" value="<?= $b->barberoId ?>">
                                    <button type="submit" class="btn-delete">ELIMINAR</button>
                                </form>
                            </div>
                        </section>
                    <?php endif; ?>
                </section>
            <?php endforeach; ?>
        </section>
    </main>
</body>
</html>