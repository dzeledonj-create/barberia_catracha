<?php
// ============================================================================
// --- BUSCADOR INTELIGENTE DE RUTAS PARA VERCEL (Evita errores de Mayúsculas) ---
// ============================================================================

// 1. Cargamos la Base de Datos de forma segura
if (file_exists(__DIR__ . '/../../Clases/BD.php')) {
    require_once __DIR__ . '/../../Clases/BD.php';
} else {
    require_once __DIR__ . '/../../clases/BD.php';
}

// 2. Cargamos la clase base "Usuario" (¡Aquí estaba el fallo de Vercel!)
$rutasUsuario = [
    __DIR__ . '/Usuario.php',
    __DIR__ . '/usuario.php',
    __DIR__ . '/../../Clases/Usuario.php',
    __DIR__ . '/../../clases/Usuario.php',
    __DIR__ . '/../../Clases/usuario.php',
    __DIR__ . '/../../clases/usuario.php'
];
foreach ($rutasUsuario as $ruta) {
    if (file_exists($ruta)) {
        require_once $ruta;
        break;
    }
}

// 3. Cargamos Administrador de forma segura
if (file_exists(__DIR__ . '/Administrador.php')) {
    require_once __DIR__ . '/Administrador.php';
} else if (file_exists(__DIR__ . '/administrador.php')) {
    require_once __DIR__ . '/administrador.php';
}

// 4. Cargamos UsuarioBarbero de forma segura
if (file_exists(__DIR__ . '/UsuarioBarbero.php')) {
    require_once __DIR__ . '/UsuarioBarbero.php';
} else if (file_exists(__DIR__ . '/usuariobarbero.php')) {
    require_once __DIR__ . '/usuariobarbero.php';
}

// ============================================================================
// --- CLASE GESTOR USUARIOS ---
// ============================================================================

class GestorUsuarios {

    public static function autenticar($email, $password) {
        $db = BD::obtenerConexion();

        $sql = "SELECT u.*, b.barbero_id 
                FROM usuarios u
                LEFT JOIN barberos b ON u.usuario_id = b.usuario_id
                WHERE u.email = ? AND u.activo = TRUE";
                
        $stmt = $db->prepare($sql);
        $stmt->execute([$email]);

        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$data) {
            return null;
        }

        if ($password !== $data['password']) {
            return null;
        }

        if ($data['rol'] === 'admin') {
            return new Administrador(
                $data['nombre'],
                $data['email'],
                (bool)$data['activo'],
                $data['usuario_id']
            );
        }

        if ($data['rol'] === 'barbero') {
            return new UsuarioBarbero(
                $data['nombre'],
                $data['email'],
                $data['barbero_id'],
                (bool)$data['activo'],
                $data['usuario_id']
            );
        }

        return null;
    }

    public static function obtenerDatosSesion($usuario): array {
        $datos = [
            'usuario_id' => $usuario->usuarioId,
            'nombre' => $usuario->nombre,
            'email' => $usuario->email,
            'rol' => $usuario->rol
        ];

        if ($usuario instanceof UsuarioBarbero) {
            $datos['barbero_id'] = $usuario->barberoId;
        }

        return $datos;
    }

    // Eliminamos la restricción estricta de tipo en el retorno para evitar caídas en producción
    public static function obtenerDesdeSesion() {
        // --- SOLUCIÓN DE SESIONES EN VERCEL ---
        if (session_status() === PHP_SESSION_NONE) {
            ini_set('session.save_path', '/tmp');
            session_start();
        }

        if (empty($_SESSION['usuario_id'])) return null;

        if ($_SESSION['rol'] === 'admin') {
            return new Administrador(
                $_SESSION['nombre'],
                $_SESSION['email'],
                true,
                $_SESSION['usuario_id']
            );
        }

        if ($_SESSION['rol'] === 'barbero') {
            return new UsuarioBarbero(
                $_SESSION['nombre'],
                $_SESSION['email'],
                $_SESSION['barbero_id'],
                true,
                $_SESSION['usuario_id']
            );
        }

        return null;
    }
}