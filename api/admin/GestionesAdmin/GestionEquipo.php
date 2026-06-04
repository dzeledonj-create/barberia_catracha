<?php
/**
 * Panel de administración - Gestión del Equipo
 * Permite crear, editar, eliminar y autorizar miembros del equipo (barberos y admins)
 */

// Carga las clases necesarias para manejar barberos, usuarios y administradores
require_once __DIR__ . '/../../Clases/Barbero.php';
require_once __DIR__ . '/../clases_admin/GestorUsuarios.php';
require_once __DIR__ . '/../clases_admin/Administrador.php';

// Configura la ruta de guardado de sesiones y la inicia
ini_set('session.save_path', sys_get_temp_dir());
session_start();

//Obtenemos el usuario actual desde la sesión
$usuario = GestorUsuarios::obtenerDesdeSesion();

// Si no hay sesión activa, redirige al login
if (!$usuario) {
    header("Location: ../login.php");
    exit;
}

// Si el usuario no es administrador, redirige al login general
if (!$usuario instanceof Administrador) {
    header("Location: /barberia_catracha/api/login.php");
    exit;
}

// --- PROCESAMIENTO DEL FORMULARIO UNIFICADO ---
// Verificar que el formulario se ha enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtiene la acción enviada (crear, actualizar, eliminar, autorizar_vista)
    $accion = $_POST['accion'] ?? '';
    // Obtiene el rol seleccionado (barbero por defecto)
    $rolSeleccionado = $_POST['rol'] ?? 'barbero';
    // Bandera para saber si la operación fue exitosa
    $procesado = false;
    
    // Validar que el rol seleccionado es válido
    if ($accion === 'crear') {
        if ($rolSeleccionado === 'admin') {
            // Crea una instancia de Administrador con los datos del formulario
            $nuevoAdmin = new Administrador(
                $_POST['nombre'] ?? '',
                $_POST['email'] ?? '',
                true
            );
            $nuevoAdmin->crear(); 
            $procesado = true;
        } else {
            // Crea una instancia de Barbero con todos los datos del formulario
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
            // Guarda el barbero en la base de datos
            if ($barbero->guardar()) {
                $procesado = true;
            }
        }
    }

    // Para actualizar, primero determinamos el tipo de usuario por su rol
    if ($accion === 'actualizar') {
        // Obtiene los IDs del barbero y del usuario del formulario
        $barberoId = (int)($_POST['barbero_id'] ?? 0);
        $usuarioId = (int)($_POST['usuario_id'] ?? 0);
        $rolSeleccionado = $_POST['rol'] ?? '';

        if ($rolSeleccionado === 'admin') {
            // Actualizar o crear admin
            if ($usuarioId) {
                // Si ya existe usuario_id, es actualización
                $admin = Administrador::obtenerPorId($usuarioId);
                if ($admin) {
                    // Actualiza los datos del administrador con los valores del formulario
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
                // Actualiza cada campo del barbero con los valores del formulario
                // Si un campo no viene en el POST, conserva el valor actual
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
        $barberoId = isset($_POST['barbero_id']) ? (int)$_POST['barbero_id'] : 0;
        $usuarioId = (int)($_POST['usuario_id'] ?? 0);
        $rolSeleccionado = $_POST['rol'] ?? '';

        if ($rolSeleccionado === 'admin' && $usuarioId) {
            // Si el usuario es administrador, eliminamos el usuario completo
            // incluyendo cualquier perfil de barbero asociado.
            $admin = Administrador::obtenerPorId($usuarioId);
            if ($admin) {
                $admin->eliminar();
                $procesado = true;
            }
        } elseif ($barberoId) {
            // Si no es admin, eliminamos solo el perfil de barbero.
            $barbero = Barbero::obtenerPorId($barberoId);
            if ($barbero) {
                $barbero->eliminar();
                $procesado = true;
            }
        } elseif ($usuarioId) {
            // Fallback: elimina directamente por usuario_id si no hay barbero_id
            $usuario = Usuario::obtenerPorId($usuarioId);
            if ($usuario) {
                $usuario->eliminar();
                $procesado = true;
            }
        }
    }

    // Acción para autorizar que un barbero aparezca en la vista pública del cliente
    if ($accion === 'autorizar_vista') {
        $barberoId = isset($_POST['barbero_id']) ? (int)$_POST['barbero_id'] : 0;
        if ($barberoId) {
            $barbero = Barbero::obtenerPorId($barberoId);
            // Llama al método que activa la visibilidad del barbero en la vista cliente
            if ($barbero && $barbero->autorizarEnVista()) {
                $procesado = true;
            }
        }
    }

    // Si se procesó correctamente, redirigir
    if ($procesado) {
        // Redirige a la misma página para evitar reenvío del formulario al recargar
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
    <!-- Barra lateral de navegación del panel admin -->
    <?php include_once __DIR__ . '/../includes/admin_sidebar.php'; ?>


    <main class="content">
        <header class="admin-header-main">
            <h1>Gestión del Equipo</h1>
            <p>Añade, edita o elimina miembros de tu equipo</p>
        </header>

        <!-- Panel para añadir un nuevo miembro al equipo -->
        <section class="add-member-panel">
            <div class="panel-header">
                <h2>NUEVO MIEMBRO</h2>
                <div class="line-gold"></div>
            </div>
            
            <!-- Formulario de creación: envía a la misma página con accion=crear -->
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
                        <!-- Define si el nuevo miembro será barbero o administrador -->
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


        <!-- Grid que muestra todos los miembros del equipo -->
        <section class="equipo-grid">
            <?php foreach ($barberos as $barber): ?>
                <?php 
                    // Determinar si es barbero o admin
                    $esBarbero = $barber->getBarberoId() !== null;
                    // Usa barbero_id si es barbero, o usuario_id si es admin puro
                    $idActual = $esBarbero ? $barber->getBarberoId() : $barber->getUsuarioId();
                    // Comprueba si este miembro es el que se está editando actualmente
                    $estanEditando = ($editandoId === (string)$idActual);
                ?>
                <!-- Tarjeta del miembro; añade clase 'editing' si está en modo edición -->
                <section class="barbero-card <?= $estanEditando ? 'editing' : '' ?>">
                    <?php if ($estanEditando && $esBarbero): ?>
                        <!-- Formulario de edición completo para barberos -->
                        <form action="" method="POST" class="edit-form">
                            <input type="hidden" name="accion" value="actualizar">
                            <!-- IDs necesarios para identificar al barbero y su usuario en el backend -->
                            <input type="hidden" name="barbero_id" value="<?= $barber->getBarberoId() ?>">
                            <input type="hidden" name="usuario_id" value="<?= $barber->getUsuarioId() ?>">
                            
                            <label>Nombre</label>
                            <input type="text" name="nombre" value="<?= htmlspecialchars($barber->getNombre()) ?>" required>
                            
                            <label>Email</label>
                            <input type="email" name="email" value="<?= htmlspecialchars($barber->getEmail()) ?>" required>
                            
                            <label>Especialidad</label>
                            <input type="text" name="especialidad" value="<?= htmlspecialchars($barber->getEspecialidad() ?? '') ?>">
                            
                            <label>Rol de Sistema</label>
                            <!-- Marca como seleccionado el rol actual del barbero -->
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
                                <!-- Cancelar descarta los cambios volviendo a la vista sin ?editar en la URL -->
                                <a href="/barberia_catracha/api/admin/GestionesAdmin/GestionEquipo.php" class="btn-cancel">CANCELAR</a>
                            </section>
                        </form>
                    <?php elseif ($estanEditando && !$esBarbero): ?>
                        <!-- Formulario de edición simplificado para administradores puros (sin perfil de barbero) -->
                        <form action="" method="POST" class="edit-form">
                            <input type="hidden" name="accion" value="actualizar">
                            <input type="hidden" name="usuario_id" value="<?= $barber->getUsuarioId() ?>">
                            <!-- El rol se envía como campo oculto porque los admins no cambian de rol aquí -->
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
                        <!-- Vista de tarjeta normal (modo lectura, sin edición activa) -->
                            <div class="card-image">
                                <!-- onerror reemplaza la imagen por la predeterminada si la URL falla -->
                                <img src="<?= htmlspecialchars($barber->getFotoUrl() ?? '/barberia_catracha/assets/img/default-user.jpg') ?>" alt="<?= htmlspecialchars($barber->getNombre()) ?>" onerror="this.src='/barberia_catracha/assets/img/default-user.jpg'">
                            </div>
                        <section class="info">
                            <h3><?= htmlspecialchars($barber->getNombre()) ?></h3>
                            <p class="rank"><?= htmlspecialchars($barber->getEspecialidad() ?? 'Admin') ?></p>
                            <p class="role-text"><?= strtoupper($barber->getRol() ?? 'Barbero') ?></p>
                            
                            <div class="actions-group">
                                <!-- Enlace que recarga la página con ?editar=ID para activar el formulario de edición -->
                                <a href="?editar=<?= $idActual ?>" class="btn-edit">EDITAR</a>

                                <?php if (Barbero::tieneColumnaMostrarEnVista() && $barber->getRol() === 'admin' && $barber->getBarberoId()): ?>
                                    <?php if (!$barber->getMostrarEnVista()): ?>
                                        <!-- Formulario para autorizar al admin-barbero a aparecer en la vista pública -->
                                        <form action="" method="POST" class="delete-form">
                                            <input type="hidden" name="accion" value="autorizar_vista">
                                            <input type="hidden" name="barbero_id" value="<?= $barber->getBarberoId() ?>">
                                            <button type="submit" class="btn-authorize">AUTORIZAR VISTA CLIENTE</button>
                                        </form>
                                    <?php else: ?>
                                        <!-- Indicador visual de que el barbero ya está autorizado en la vista cliente -->
                                        <span class="role-text" style="color: #2a7a2a; font-size: 0.75rem;">AUTORIZADO EN VISTA</span>
                                    <?php endif; ?>
                                <?php endif; ?>

                                <!-- Formulario de eliminación con confirmación JavaScript antes de enviar -->
                                <form action="" method="POST" class="delete-form" onsubmit="return confirm('¿Estás seguro de que quieres eliminar a este miembro?');">
                                    <input type="hidden" name="accion" value="eliminar">
                                    <!-- Se envían ambos IDs para que el backend decida qué eliminar según el rol -->
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