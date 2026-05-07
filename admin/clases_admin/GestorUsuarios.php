<?php
require_once 'Clases/BD.php';
require_once 'Administrador.php';
require_once 'UsuarioBarbero.php';

class GestorUsuarios {

    public static function autenticar($email, $password) {
        $db = BD::obtenerConexion();

        $sql = "SELECT * FROM usuarios WHERE email = ? AND activo = TRUE";
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
                $data['activo'],
                $data['usuario_id']
            );
        }

        if ($data['rol'] === 'barbero') {
            return new UsuarioBarbero(
                $data['nombre'],
                $data['email'],
                $data['barbero_id'],
                $data['activo'],
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
}