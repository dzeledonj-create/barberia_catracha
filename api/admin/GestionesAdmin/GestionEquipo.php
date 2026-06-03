<?php
require_once __DIR__ . '/../../Clases/Barbero.php';
require_once __DIR__ . '/../clases_admin/GestorUsuarios.php';
require_once __DIR__ . '/../clases_admin/Administrador.php';
ini_set('session.save_path', sys_get_temp_dir());
session_start();

//Obtenemos el usuario actual desde la sesión
$usuario = GestorUsuarios::obtenerDesdeSesion();
if (!$usuario) {
    header("Location: ../login.php");
    exit;
}
if (!$usuario instanceof Administrador) {
    header("Location: /barberia_catracha/api/login.php");
    exit;
}

// --- PROCESAMIENTO DEL FORMULARIO UNIFICADO ---
// Verificar que el formulario se ha enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion = $_POST['accion'] ?? '';
    $rolSeleccionado = $_POST['rol'] ?? 'barbero';
    $procesado = false;
    
    // Validar que el rol seleccionado es válido
    if ($accion === 'crear') {
        if ($rolSeleccionado === 'admin') {
            $nuevoAdmin = new Administrador(
                $_POST['nombre'] ?? '',
                $_POST['email'] ?? '',
                true
            );
            $nuevoAdmin->crear(); 
            $procesado = true;
        } else {
            $barbero = new Barbero(
                $_POST['nombre'] ?? '',
                $_POST['especialidad'] ?? '',
                $_POST['foto_url'] ?? 'assets/img/default-user.jpg',
                true,
                null,
                $_POST['descripcion'] ?? '',
                $_POST['etiquetas'] ?? '',
                'barbero',
                $_POST['email'] ?? ''
            );
            if ($barbero->guardar()) {
                $procesado = true;
            }
        }
    }

    // Para actualizar, primero determinamos el tipo de usuario por su rol
    if ($accion === 'actualizar') {
        $barberoId = (int)($_POST['barbero_id'] ?? 0);
        $usuarioId = (int)($_POST['usuario_id'] ?? 0);
        $rolSeleccionado = $_POST['rol'] ?? '';

        if ($rolSeleccionado === 'admin') {
            // Actualizar o crear admin
            if ($usuarioId) {
                // Si ya existe usuario_id, es actualización
                $admin = Administrador::obtenerPorId($usuarioId);
                if ($admin) {
                    $admin->setNombre($_POST['nombre'] ?? $admin->getNombre());
                    $admin->setEmail($_POST['email'] ?? $admin->getEmail());
                    $admin->setActivo(isset($_POST['activo']));
                    $admin->actualizar();
                    $procesado = true;
                }
            }
        } else {
            // Actualizar barbero
            $barbero = Barbero::obtenerPorId($barberoId);
            if ($barbero) {
                $barbero->setNombre($_POST['nombre'] ?? $barbero->getNombre());
                $barbero->setEmail($_POST['email'] ?? $barbero->getEmail());
                $barbero->setEspecialidad($_POST['especialidad'] ?? $barbero->getEspecialidad());
                $barbero->setDescripcion($_POST['descripcion'] ?? $barbero->getDescripcion());
                $barbero->setEtiquetas($_POST['etiquetas'] ?? $barbero->getEtiquetas());
                $barbero->setFotoUrl($_POST['foto_url'] ?? $barbero->getFotoUrl());
                $barbero->setActivo(isset($_POST['activo']));
                if ($barbero->guardar()) {
                    $procesado = true;
                }
            }
        }
    }

    // Para eliminar, determinamos el tipo de usuario por su rol
    if ($accion === 'eliminar') {
        $barberoId = (int)($_POST['barbero_id'] ?? 0);
        $usuarioId = (int)($_POST['usuario_id'] ?? 0);
        $rolSeleccionado = $_POST['rol'] ?? '';

        if ($rolSeleccionado === 'admin') {
            // Eliminar admin
            if ($usuarioId) {
                $admin = Administrador::obtenerPorId($usuarioId);
                if ($admin) {
                    $admin->eliminar();
                    $procesado = true;
                }
            }
        } else {
            // Eliminar barbero
            $barbero = Barbero::obtenerPorId($barberoId);
            if ($barbero) {
                $barbero->eliminar();
                $procesado = true;
            }
        }
    }

    // Si se procesó correctamente, redirigir
    if ($procesado) {
        header("Location: /barberia_catracha/api/admin/GestionesAdmin/GestionEquipo.php");
        exit;
    }
}

// Obtener todos los barberos para mostrar en la tabla
$barberos = Barbero::obtenerTodos();
// Para resaltar el formulario de edición si se accede con ?editar=ID
$editandoId = $_GET['editar'] ?? null;
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Panel Admin - Gestión de Equipo</title>
    <link rel="stylesheet" href="../../../assets/style.css">
    <link rel="stylesheet" href="/barberia_catracha/assets/style.css">
</head>
<body class="admin-panel">
    <?php include_once __DIR__ . '/../includes/admin_sidebar.php'; ?>


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
            
            <form action="" method="POST" class="add-form-grid">
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
            <?php foreach ($barberos as $barber): ?>
                <?php 
                    // Determinar si es barbero o admin
                    $esBarbero = $barber->getBarberoId() !== null;
                    $idActual = $esBarbero ? $barber->getBarberoId() : $barber->getUsuarioId();
                    $estanEditando = ($editandoId === (string)$idActual);
                ?>
                <section class="barbero-card <?= $estanEditando ? 'editing' : '' ?>">
                    <?php if ($estanEditando && $esBarbero): ?>
                        
                        <form action="" method="POST" class="edit-form">
                            <input type="hidden" name="accion" value="actualizar">
                            <input type="hidden" name="barbero_id" value="<?= $barber->getBarberoId() ?>">
                            <input type="hidden" name="usuario_id" value="<?= $barber->getUsuarioId() ?>">
                            
                            <label>Nombre</label>
                            <input type="text" name="nombre" value="<?= htmlspecialchars($barber->getNombre()) ?>" required>
                            
                            <label>Email</label>
                            <input type="email" name="email" value="<?= htmlspecialchars($barber->getEmail()) ?>" required>
                            
                            <label>Especialidad</label>
                            <input type="text" name="especialidad" value="<?= htmlspecialchars($barber->getEspecialidad() ?? '') ?>">
                            
                            <label>Rol de Sistema</label>
                            <select name="rol" required>
                                <option value="barbero" <?= ($barber->getRol() === 'barbero') ? 'selected' : '' ?>>Barbero Profesional</option>
                                <option value="admin" <?= ($barber->getRol() === 'admin') ? 'selected' : '' ?>>Administrador del Sistema</option>
                            </select>

                            <label>Descripción</label>
                            <textarea name="descripcion" rows="3"><?= htmlspecialchars($barber->getDescripcion() ?? '') ?></textarea>
                            
                            <label>Etiquetas</label>
                            <input type="text" name="etiquetas" value="<?= htmlspecialchars($barber->getEtiquetas() ?? '') ?>">
                            
                            <label>Foto URL</label>
                            <input type="text" name="foto_url" value="<?= htmlspecialchars($barber->getFotoUrl() ?? '') ?>">

                            <section class="form-buttons">
                                <button type="submit" class="btn-save">GUARDAR</button>
                                <a href="/barberia_catracha/api/admin/GestionesAdmin/GestionEquipo.php" class="btn-cancel">CANCELAR</a>
                            </section>
                        </form>
                    <?php elseif ($estanEditando && !$esBarbero): ?>
                        
                        <form action="" method="POST" class="edit-form">
                            <input type="hidden" name="accion" value="actualizar">
                            <input type="hidden" name="usuario_id" value="<?= $barber->getUsuarioId() ?>">
                            <input type="hidden" name="rol" value="<?= htmlspecialchars($barber->getRol()) ?>">
                            
                            <label>Nombre</label>
                            <input type="text" name="nombre" value="<?= htmlspecialchars($barber->getNombre()) ?>" required>
                            
                            <label>Email</label>
                            <input type="email" name="email" value="<?= htmlspecialchars($barber->getEmail()) ?>" required>

                            <section class="form-buttons">
                                <button type="submit" class="btn-save">GUARDAR</button>
                                <a href="/barberia_catracha/api/admin/GestionesAdmin/GestionEquipo.php" class="btn-cancel">CANCELAR</a>
                            </section>
                        </form>
                    <?php else: ?>
                            <div class="card-image">
                                <img src="<?= htmlspecialchars($barber->getFotoUrl() ?? '/barberia_catracha/assets/img/default-user.jpg') ?>" alt="<?= htmlspecialchars($barber->getNombre()) ?>" onerror="this.src='/barberia_catracha/assets/img/default-user.jpg'">
                            </div>
                        <section class="info">
                            <h3><?= htmlspecialchars($barber->getNombre()) ?></h3>
                            <p class="rank"><?= htmlspecialchars($barber->getEspecialidad() ?? 'Admin') ?></p>
                            <p class="role-text"><?= strtoupper($barber->getRol() ?? 'Barbero') ?></p>
                            
                            <div class="actions-group">
                                <a href="?editar=<?= $idActual ?>" class="btn-edit">EDITAR</a>
                                
                                <form action="" method="POST" class="delete-form" onsubmit="return confirm('¿Estás seguro de que quieres eliminar a este miembro?');">
                                    <input type="hidden" name="accion" value="eliminar">
                                    <input type="hidden" name="barbero_id" value="<?= $barber->getBarberoId() ?>">
                                    <input type="hidden" name="usuario_id" value="<?= $barber->getUsuarioId() ?>">
                                    <input type="hidden" name="rol" value="<?= htmlspecialchars($barber->getRol()) ?>">
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
