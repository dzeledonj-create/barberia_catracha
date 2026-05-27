<?php
require_once __DIR__ . '/../../Clases/BD.php';
require_once 'Administrador.php';
require_once 'UsuarioBarbero.php';

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

    public static function obtenerDesdeSesion(): ?Usuario {
        if (session_status() === PHP_SESSION_NONE) session_start();

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