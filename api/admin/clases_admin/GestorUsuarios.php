<?php
// ============================================================================
// --- AUTOLOADER GLOBAL INTELIGENTE (Solución Definitiva para Vercel/Linux) ---
// ============================================================================
spl_autoload_register(function ($clase) {
    // El root de la app/api está 2 niveles arriba de este archivo (/api)
    $baseDir = dirname(dirname(__DIR__)); 
    
    // Directorios donde residen las clases (probamos ambas combinaciones de mayúsculas)
    $directorios = [
        $baseDir . '/Clases/',
        $baseDir . '/clases/',
        $baseDir . '/admin/clases_admin/',
        $baseDir . '/admin/Clases_Admin/'
    ];
    
    foreach ($directorios as $directorio) {
        // Probamos variaciones de nombre de archivo (Exacto, minúsculas, Primera Mayúscula)
        $posiblesArchivos = [
            $directorio . $clase . '.php',
            $directorio . strtolower($clase) . '.php',
            $directorio . ucfirst(strtolower($clase)) . '.php'
        ];
        
        foreach ($posiblesArchivos as $archivo) {
            if (file_exists($archivo)) {
                require_once $archivo;
                return;
            }
        }
    }
});

// Forzamos la carga inicial de la conexión para asegurar compatibilidad
if (class_exists('BD')) {
    BD::obtenerConexion();
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
        return [
            'usuario_id' => $usuario->usuarioId,
            'nombre' => $usuario->nombre,
            'email' => $usuario->email,
            'rol' => $usuario->rol,
            'barbero_id' => ($usuario instanceof UsuarioBarbero) ? $usuario->barberoId : null
        ];
    }

    // Eliminamos permanentemente la restricción estricta ': ?Usuario' de la firma
    public static function obtenerDesdeSesion() {
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