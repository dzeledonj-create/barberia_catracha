<?php
require_once __DIR__ . '/../../Clases/BD.php';

class Usuario {
    public ?int $usuarioId;
    public string $nombre;
    public string $email;
    public string $rol;
    public bool $activo;

    public function __construct($nombre, $email, $rol, $activo = true, $usuarioId = null) {
        $this->usuarioId = $usuarioId;
        $this->nombre = $nombre;
        $this->email = $email;
        $this->rol = $rol;
        $this->activo = $activo;
    }

    public function estaActivo(): bool {
        return $this->activo;
    }

    public function getNombre(): string {
        return $this->nombre;
    }

    public function crear() {
    $conexion = BD::obtenerConexion();

    // Añadimos 'password' en las columnas y un valor fijo '1234' en los VALUES
    $sql = "INSERT INTO usuarios (nombre, email, password, rol, activo)
            VALUES ('$this->nombre', '$this->email', '1234', '$this->rol', '$this->activo')";

    $conexion->query($sql);
}

    public function actualizar() {
        if ($this->usuarioId === null) {
            throw new Exception("El usuario debe tener un ID para ser actualizado.");
        }

        $conexion = BD::obtenerConexion();

        $sql = "UPDATE usuarios
                SET nombre = '$this->nombre',
                    email = '$this->email',
                    rol = '$this->rol',
                    activo = '$this->activo'
                WHERE usuario_id = $this->usuarioId";

        $conexion->query($sql);
    }

    public function eliminar() {
        if ($this->usuarioId === null) {
            throw new Exception("El usuario debe tener un ID para ser eliminado.");
        }

        $conexion = BD::obtenerConexion();

        $sql = "DELETE FROM usuarios WHERE usuario_id = $this->usuarioId";

        $conexion->query($sql);
    }

    public static function obtenerPorId($usuarioId): ?Usuario {
        $conexion = BD::obtenerConexion();
    
        $sql = "SELECT * FROM usuarios WHERE usuario_id = $usuarioId";
        $resultado = $conexion->query($sql);
    
        if ($resultado->rowCount() > 0) {
            $fila = $resultado->fetch(PDO::FETCH_ASSOC);
            return new Usuario(
                $fila['nombre'],
                $fila['email'],
                $fila['rol'],
                $fila['activo'],
                $fila['usuario_id']
            );
        }
        return null; // No se encontró el usuario
    }

    public static function obtenerTodos(): array {
        $conexion = BD::obtenerConexion();
    
        $sql = "SELECT * FROM usuarios";
        $resultado = $conexion->query($sql);
    
        $usuarios = [];
        while ($fila = $resultado->fetch(PDO::FETCH_ASSOC)) {
            $usuarios[] = new Usuario(
                $fila['nombre'],
                $fila['email'],
                $fila['rol'],
                $fila['activo'],
                $fila['usuario_id']
            );
        }
        return $usuarios;
    }
}
?>