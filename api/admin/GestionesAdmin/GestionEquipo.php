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

function guardarImagenEquipo(string $campoFoto, ?string $rutaActual = null): array {
    if (empty($_FILES[$campoFoto]) || $_FILES[$campoFoto]['error'] === UPLOAD_ERR_NO_FILE) {
        return ['path' => $rutaActual, 'uploaded' => false, 'error' => ''];
    }

    $archivo = $_FILES[$campoFoto];
    if ($archivo['error'] !== UPLOAD_ERR_OK) {
        $errores = [
            UPLOAD_ERR_INI_SIZE => 'El archivo excede el tamaño máximo del servidor.',
            UPLOAD_ERR_FORM_SIZE => 'El archivo excede el tamaño máximo permitido.',
            UPLOAD_ERR_PARTIAL => 'El archivo se subió parcialmente.',
            UPLOAD_ERR_NO_FILE => 'No se seleccionó ningún archivo.',
            UPLOAD_ERR_NO_TMP_DIR => 'Falta el directorio temporal.',
            UPLOAD_ERR_CANT_WRITE => 'No se pudo escribir el archivo en disco.',
            UPLOAD_ERR_EXTENSION => 'La subida fue detenida por una extensión.',
        ];
        return ['path' => $rutaActual, 'uploaded' => false, 'error' => $errores[$archivo['error']] ?? 'Error desconocido al subir la imagen.'];
    }

    if (!is_uploaded_file($archivo['tmp_name'])) {
        return ['path' => $rutaActual, 'uploaded' => false, 'error' => 'El archivo no es una subida válida.'];
    }

    $uploadDir = __DIR__ . '/../../../assets/img/equipo/';
    if (!is_dir($uploadDir)) {
        @mkdir($uploadDir, 0755, true);
    }

    $ext = strtolower(pathinfo($archivo['name'], PATHINFO_EXTENSION));
    if (!preg_match('/^[a-z0-9]+$/i', $ext)) {
        $ext = 'jpg';
    }
    $filename = time() . '_' . bin2hex(random_bytes(4)) . ($ext ? '.' . $ext : '');
    $destino = $uploadDir . $filename;

    if (!move_uploaded_file($archivo['tmp_name'], $destino)) {
        return ['path' => $rutaActual, 'uploaded' => false, 'error' => 'No se pudo guardar el archivo en el servidor.'];
    }

    return ['path' => 'assets/img/equipo/' . $filename, 'uploaded' => true, 'error' => ''];
}

// Verificar que el formulario se ha enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtiene la acción enviada (crear, actualizar, eliminar, autorizar_vista)
    $accion = $_POST['accion'] ?? '';
    // Obtiene el rol seleccionado (barbero por defecto)
    $rolSeleccionado = $_POST['rol'] ?? 'barbero';
    // Bandera para saber si la operación fue exitosa
    $procesado = false;
    
    // Validar que el rol seleccionado es válido
    if ($accion === 'crear') { // Crear nuevo miembro del equipo
        $uploadResult = guardarImagenEquipo('foto', 'assets/img/default-user.jpg');

        if ($rolSeleccionado === 'admin') {// Si el rol es admin, se crea un nuevo administrador
            $nuevoAdmin = new Administrador(
                $_POST['nombre'] ?? '',
                $_POST['email'] ?? '',
                true
            );
            $nuevoAdmin->crear();
            $procesado = true;

            if ($uploadResult['uploaded']) {
                try {
                    $db = BD::obtenerConexion();
                    $sqlBarbero = "INSERT INTO barberos (usuario_id, especialidad, foto_url, descripcion, etiquetas";
                    $sqlBarbero .= Barbero::tieneColumnaMostrarEnVista() ? ", mostrar_en_vista" : "";
                    $sqlBarbero .= ") VALUES (?, ?, ?, ?, ?";
                    $sqlBarbero .= Barbero::tieneColumnaMostrarEnVista() ? ", ?" : "";
                    $sqlBarbero .= ")";

                    $params = [
                        $nuevoAdmin->getUsuarioId(),
                        'Administrador',
                        $uploadResult['path'],
                        'Administrador del sistema.',
                        ''
                    ];
                    if (Barbero::tieneColumnaMostrarEnVista()) {
                        $params[] = false;
                    }
                    $stmt = $db->prepare($sqlBarbero);
                    $stmt->execute($params);
                    $_SESSION['flash_message'] = 'Imagen subida correctamente.';
                } catch (Exception $e) {
                    $_SESSION['flash_message'] = 'Administrador creado, pero no se pudo guardar la imagen.';
                }
            } elseif ($uploadResult['error']) {
                $_SESSION['flash_message'] = 'Administrador creado. ' . $uploadResult['error'];
            }
        } else {
            $barbero = new Barbero(
                $_POST['nombre'] ?? '',
                $_POST['especialidad'] ?? '',
                $uploadResult['path'],
                true,
                null,
                $_POST['descripcion'] ?? '',
                $_POST['etiquetas'] ?? '',
                'barbero',
                $_POST['email'] ?? ''
            );

            if ($barbero->guardar()) {
                $procesado = true;
                if ($uploadResult['uploaded']) {
                    $_SESSION['flash_message'] = 'Imagen subida correctamente.';
                }
            } elseif ($uploadResult['error']) {
                $_SESSION['flash_message'] = $uploadResult['error'];
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
                    // Manejar petición de borrar foto si se solicitó
                    if (!empty($_POST['foto_delete']) && $_POST['foto_delete'] == '1') {
                        try {
                            $db = BD::obtenerConexion();
                            $q = $db->prepare("SELECT foto_url FROM barberos WHERE usuario_id = ? LIMIT 1");
                            $q->execute([$usuarioId]);
                            $current = $q->fetchColumn();
                            if ($current) {
                                $possible = ltrim($current, '/');
                                if (strpos($possible, 'assets/img/equipo/') !== false) {
                                    $oldFull = __DIR__ . '/../../../' . $possible;
                                    if (is_file($oldFull)) @unlink($oldFull);
                                }
                            }
                            $upd = $db->prepare("UPDATE barberos SET foto_url = ? WHERE usuario_id = ?");
                            $upd->execute(['assets/img/default-user.jpg', $usuarioId]);
                            $_SESSION['flash_message'] = 'Imagen eliminada correctamente.';
                        } catch (Exception $e) {
                        }
                    }

                    $uploadResult = guardarImagenEquipo('foto');

                    if (!empty($_POST['foto_delete']) && $_POST['foto_delete'] == '1') {
                        try {
                            $db = BD::obtenerConexion();
                            $q = $db->prepare("SELECT foto_url FROM barberos WHERE usuario_id = ? LIMIT 1");
                            $q->execute([$usuarioId]);
                            $current = $q->fetchColumn();
                            if ($current) {
                                $possible = ltrim($current, '/');
                                if (strpos($possible, 'assets/img/equipo/') !== false) {
                                    $oldFull = __DIR__ . '/../../../' . $possible;
                                    if (is_file($oldFull)) @unlink($oldFull);
                                }
                            }
                            $upd = $db->prepare("UPDATE barberos SET foto_url = ? WHERE usuario_id = ?");
                            $upd->execute(['assets/img/default-user.jpg', $usuarioId]);
                            $_SESSION['flash_message'] = 'Imagen eliminada correctamente.';
                        } catch (Exception $e) {
                            // No bloqueamos la actualización si falla la limpieza.
                        }
                    }

                    if ($uploadResult['uploaded']) {
                        try {
                            $db = BD::obtenerConexion();
                            $stmt = $db->prepare("SELECT barbero_id, foto_url FROM barberos WHERE usuario_id = ? LIMIT 1");
                            $stmt->execute([$usuarioId]);
                            $barberoRow = $stmt->fetch(PDO::FETCH_ASSOC);
                            $current = $barberoRow['foto_url'] ?? null;

                            if ($barberoRow) {
                                $upd = $db->prepare("UPDATE barberos SET foto_url = ? WHERE usuario_id = ?");
                                $upd->execute([$uploadResult['path'], $usuarioId]);
                            } else {
                                $sqlInsert = "INSERT INTO barberos (usuario_id, especialidad, foto_url, descripcion, etiquetas";
                                $sqlInsert .= Barbero::tieneColumnaMostrarEnVista() ? ", mostrar_en_vista" : "";
                                $sqlInsert .= ") VALUES (?, ?, ?, ?, ?";
                                $sqlInsert .= Barbero::tieneColumnaMostrarEnVista() ? ", ?" : "";
                                $sqlInsert .= ")";

                                $params = [
                                    $usuarioId,
                                    'Administrador',
                                    $uploadResult['path'],
                                    'Administrador del sistema.',
                                    ''
                                ];
                                if (Barbero::tieneColumnaMostrarEnVista()) {
                                    $params[] = false;
                                }
                                $ins = $db->prepare($sqlInsert);
                                $ins->execute($params);
                            }

                            if ($current) {
                                $possible = ltrim($current, '/');
                                if (strpos($possible, 'assets/img/equipo/') !== false) {
                                    $oldFull = __DIR__ . '/../../../' . $possible;
                                    if (is_file($oldFull)) @unlink($oldFull);
                                }
                            }

                            $_SESSION['flash_message'] = 'Imagen subida correctamente.';
                        } catch (Exception $e) {
                            $_SESSION['flash_message'] = 'Administrador actualizado, pero no se pudo guardar la nueva imagen.';
                        }
                    } elseif ($uploadResult['error']) {
                        $_SESSION['flash_message'] = 'Administrador actualizado. ' . $uploadResult['error'];
                    }
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
                // Si se sube una nueva foto, guardarla y asignarla; si no, conservar la actual
                $currentFoto = $barbero->getFotoUrl();
                $newFotoPath = null;
                // Si se solicitó borrar la foto
                if (!empty($_POST['foto_delete']) && $_POST['foto_delete'] == '1') {
                    // eliminar fichero actual si existe en carpeta equipo
                    if ($currentFoto) {
                        $possibleOld = ltrim($currentFoto, '/');
                        if (strpos($possibleOld, 'assets/img/equipo/') !== false) {
                            $oldFullPath = __DIR__ . '/../../../' . $possibleOld;
                            if (is_file($oldFullPath)) @unlink($oldFullPath);
                        }
                    }
                    $newFotoPath = 'assets/img/default-user.jpg';
                    $_SESSION['flash_message'] = 'Imagen eliminada correctamente.';
                }

                if (isset($_FILES['foto']) && isset($_FILES['foto']['tmp_name']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
                    $uploadDir = __DIR__ . '/../../../assets/img/equipo/';
                    if (!is_dir($uploadDir)) {@mkdir($uploadDir, 0755, true);} 
                    $ext = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
                    $filename = time() . '_' . bin2hex(random_bytes(4)) . ($ext ? '.' . $ext : '');
                    $dest = $uploadDir . $filename;
                    if (move_uploaded_file($_FILES['foto']['tmp_name'], $dest)) {
                        $newFotoPath = 'assets/img/equipo/' . $filename;
                        // Si la foto anterior estaba en assets/img/equipo/ borrarla
                        if ($currentFoto) {
                            $possibleOld = ltrim($currentFoto, '/');
                            if (strpos($possibleOld, 'assets/img/equipo/') !== false) {
                                $oldFullPath = __DIR__ . '/../../../' . $possibleOld;
                                if (is_file($oldFullPath)) @unlink($oldFullPath);
                            }
                        }
                        $_SESSION['flash_message'] = 'Imagen subida correctamente.';
                    }
                }
                $barbero->setFotoUrl($newFotoPath ?? $currentFoto);
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
            <form action="" method="POST" enctype="multipart/form-data" class="add-form-grid">
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
                        <label>Foto (subir desde dispositivo)</label>
                        <input type="file" name="foto" accept="image/*">
                        <input type="hidden" name="foto_delete" value="0">
                        <img class="preview-image" src="/barberia_catracha/assets/img/default-user.jpg" alt="Vista previa" />
                        <button type="button" class="btn-delete-photo">Eliminar foto</button>
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
                        <form action="" method="POST" enctype="multipart/form-data" class="edit-form">
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
                            
                            <label>Foto (subir desde dispositivo)</label>
                            <input type="file" name="foto" accept="image/*">
                            <input type="hidden" name="foto_url" value="<?= htmlspecialchars($barber->getFotoUrl() ?? '') ?>">
                            <input type="hidden" name="foto_delete" value="0">
                            <img class="preview-image" src="<?= htmlspecialchars($barber->getFotoUrl() ?? '/barberia_catracha/assets/img/default-user.jpg') ?>" alt="Vista previa" style="display:block;max-width:120px;margin-top:8px;" />
                            <button type="button" class="btn-delete-photo" style="margin-top:8px;">Eliminar foto</button>

                            <section class="form-buttons">
                                <button type="submit" class="btn-save">GUARDAR</button>
                                <!-- Cancelar descarta los cambios volviendo a la vista sin ?editar en la URL -->
                                <a href="/barberia_catracha/api/admin/GestionesAdmin/GestionEquipo.php" class="btn-cancel">CANCELAR</a>
                            </section>
                        </form>
                    <?php elseif ($estanEditando && !$esBarbero): ?>
                        <!-- Formulario de edición simplificado para administradores puros (sin perfil de barbero) -->
                        <form action="" method="POST" enctype="multipart/form-data" class="edit-form">
                            <input type="hidden" name="accion" value="actualizar">
                            <input type="hidden" name="usuario_id" value="<?= $barber->getUsuarioId() ?>">
                            <!-- El rol se envía como campo oculto porque los admins no cambian de rol aquí -->
                            <input type="hidden" name="rol" value="<?= htmlspecialchars($barber->getRol()) ?>">
                            
                            <label>Nombre</label>
                            <input type="text" name="nombre" value="<?= htmlspecialchars($barber->getNombre()) ?>" required>
                            
                            <label>Email</label>
                            <input type="email" name="email" value="<?= htmlspecialchars($barber->getEmail()) ?>" required>

                            <label>Foto (subir desde dispositivo)</label>
                            <input type="file" name="foto" accept="image/*">
                            <input type="hidden" name="foto_delete" value="0">
                            <img class="preview-image" src="<?= htmlspecialchars($barber->getFotoUrl() ?? '/barberia_catracha/assets/img/default-user.jpg') ?>" alt="Vista previa" style="display:block;max-width:120px;margin-top:8px;" />
                            <button type="button" class="btn-delete-photo" style="margin-top:8px;">Eliminar foto</button>

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
                                        <span class="role-text autorizado-en-vista">AUTORIZADO EN VISTA</span>
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

    <div id="flash-message" data-message="<?= htmlspecialchars($_SESSION['flash_message'] ?? '', ENT_QUOTES, 'UTF-8') ?>"></div>
    <?php unset($_SESSION['flash_message']); ?>
</body>
</html>