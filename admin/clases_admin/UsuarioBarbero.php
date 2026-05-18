<?php
require_once 'Usuario.php';

class UsuarioBarbero extends Usuario {
    public int $barberoId;

    public function __construct($nombre, $email, $barberoId, $activo = true, $usuarioId = null) {
        parent::__construct($nombre, $email, 'barbero', $activo, $usuarioId);
        $this->barberoId = $barberoId;
    }

    public function puedeGestionarSusReservas(): bool {
        return true;
    }

    public function getBarberoId(): int {
        return $this->barberoId;
    }

    public function puedeVerTodasLasReservas(): bool { 
        return true;
    }
    public static function obtenerBarberosActivos(): array {
        $conexion = BD::obtenerConexion();
        $sql = "SELECT * FROM usuarios WHERE rol = 'barbero' AND activo = 1";
        $resultado = $conexion->query($sql);

        $barberos = [];
        while ($fila = $resultado->fetch(PDO::FETCH_ASSOC)) {
            $barberos[] = new UsuarioBarbero(
                $fila['nombre'],
                $fila['email'],
                $fila['barbero_id'],
                $fila['activo'],
                $fila['usuario_id']
            );
        }
        return $barberos;
    }
}
?>