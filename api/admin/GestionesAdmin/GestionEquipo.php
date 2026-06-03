<?php
require_once __DIR__ . '/../../Clases/Barbero.php';
require_once __DIR__ . '/../clases_admin/GestorUsuarios.php';
require_once __DIR__ . '/../clases_admin/Administrador.php'; 

//Obtenemos el usuario actual desde la sesión
$usuario = GestorUsuarios::obtenerDesdeSesion();
if (!$usuario) {
    header("Location: ../login.php");
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
    
    // Validar que el rol seleccionado es válido
    if ($accion === 'crear') {
        if ($rolSeleccionado === 'admin') {
            $nuevoAdmin = new Administrador(
                $_POST['nombre'] ?? '',
                $_POST['email'] ?? '',
                true
            );
            $nuevoAdmin->crear(); 
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
            $barbero->guardar();
        }
    }

    // Para actualizar, primero obtenemos el barbero existente para preservar datos no editados
    if ($accion === 'actualizar') {
        $barberoId = (int)($_POST['barbero_id'] ?? 0);
        $barbero = Barbero::obtenerPorId($barberoId);
        
        // Si el barbero existe, actualizamos sus datos. Si se cambia el rol a admin, eliminamos el barbero y creamos un nuevo admin con los datos proporcionados.
        if ($barbero) {
            if ($rolSeleccionado === 'admin') {
                $barbero->eliminar();
                $nuevoAdmin = new Administrador($_POST['nombre'], $_POST['email'], isset($_POST['activo']));
                $nuevoAdmin->crear();
            } else {
                $barbero->SetNombre($_POST['nombre'] ?? $barbero->getNombre());
                $barbero->SetEmail($_POST['email'] ?? $barbero->getEmail());
                $barbero->SetEspecialidad($_POST['especialidad'] ?? $barbero->getEspecialidad());
                $barbero->SetDescripcion($_POST['descripcion'] ?? $barbero->getDescripcion());
                $barbero->SetEtiquetas($_POST['etiquetas'] ?? $barbero->getEtiquetas());
                $barbero->SetFotoUrl($_POST['foto_url'] ?? $barbero->getFotoUrl());
                $barbero->SetActivo(isset($_POST['activo']));
                $barbero->guardar();
            }
        }
    }

    // Para eliminar, simplemente obtenemos el barbero por ID y lo eliminamos. Si el rol es admin, eliminamos el admin correspondiente.
    if ($accion === 'eliminar') {
        $barberoId = (int)($_POST['barbero_id'] ?? 0);
        $barbero = Barbero::obtenerPorId($barberoId);
        if ($barbero) {
            $barbero->eliminar(); 
        }
    }

    header("Location: /barberia_catracha/api/admin/GestionesAdmin/GestionEquipo.php");
    exit;
}

// Obtener todos los barberos para mostrar en la tabla
$barberos = Barbero::obtenerTodos(); 
// Para resaltar el formulario de edición si se accede con ?editar=ID
$editandoId = $_GET['editar'] ?? null;
$barberoAEditar = $editandoId ? Barbero::obtenerPorId($editandoId) : null;
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
                <section class="barbero-card <?= ($editandoId == $barber->barberoId) ? 'editing' : '' ?>">
                    <?php if ($editandoId == $barber->barberoId && $barber->barberoId !== null): ?>
                        
                        <form action="" method="POST" class="edit-form">
                            <input type="hidden" name="accion" value="actualizar">
                            <input type="hidden" name="barbero_id" value="<?= $barber->barberoId ?>">
                            
                            <label>Nombre</label>
                            <input type="text" name="nombre" value="<?= htmlspecialchars($barber->nombre) ?>" required>
                            
                            <label>Email</label>
                            <input type="email" name="email" value="<?= htmlspecialchars($barber->email) ?>" required>
                            
                            <label>Especialidad</label>
                            <input type="text" name="especialidad" value="<?= htmlspecialchars($barber->especialidad ?? '') ?>">
                            
                            <label>Rol de Sistema</label>
                            <select name="rol" required>
                                <option value="barbero" <?= ($barber->rol === 'barbero') ? 'selected' : '' ?>>Barbero Profesional</option>
                                <option value="admin" <?= ($barber->rol === 'admin') ? 'selected' : '' ?>>Administrador del Sistema</option>
                            </select>

                            <label>Descripción</label>
                            <textarea name="descripcion" rows="3"><?= htmlspecialchars($barber->descripcion ?? '') ?></textarea>
                            
                            <label>Etiquetas</label>
                            <input type="text" name="etiquetas" value="<?= htmlspecialchars($barber->etiquetas ?? '') ?>">
                            
                            <label>Foto URL</label>
                            <input type="text" name="foto_url" value="<?= htmlspecialchars($barber->fotoUrl ?? '') ?>">

                            <section class="form-buttons">
                                <button type="submit" class="btn-save">GUARDAR</button>
                                <a href="/barberia_catracha/api/admin/GestionesAdmin/GestionEquipo.php" class="btn-cancel">CANCELAR</a>
                            </section>
                        </form>
                    <?php else: ?>
                            <div class="card-image">
                                <img src="/barberia_catracha/<?= htmlspecialchars($barber->fotoUrl ?? 'assets/img/default-user.jpg') ?>" alt="<?= htmlspecialchars($barber->nombre) ?>" onerror="this.src='/barberia_catracha/assets/img/default-user.jpg'">
                            </div>
                        <section class="info">
                            <h3><?= htmlspecialchars($barber->nombre) ?></h3>
                            <p class="rank"><?= htmlspecialchars($barber->especialidad ?? 'Admin') ?></p>
                            <p class="role-text"><?= strtoupper($barber->rol ?? 'Barbero') ?></p>
                            
                            <div class="actions-group">
                                <?php if($barber->barberoId): ?>
                                    <a href="?editar=<?= $barber->barberoId ?>" class="btn-edit">EDITAR</a>
                                    
                                    <form action="" method="POST" class="delete-form" onsubmit="return confirm('¿Estás seguro de que quieres eliminar a este miembro?');">
                                        <input type="hidden" name="accion" value="eliminar">
                                        <input type="hidden" name="barbero_id" value="<?= $barber->barberoId ?>">
                                        <button type="submit" class="btn-delete">ELIMINAR</button>
                                    </form>
                                <?php else: ?>
                                    <span class="role-text" style="color:#aaa; font-size:0.8rem;">(Edición avanzada de Admins en progreso)</span>
                                <?php endif; ?>
                            </div>
                        </section>
                    <?php endif; ?>
                </section>
            <?php endforeach; ?>
        </section>
    </main>
</body>
</html>
