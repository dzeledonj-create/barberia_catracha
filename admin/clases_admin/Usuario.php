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

    // CORREGIDO: Uso de consultas preparadas y retorna el ID insertado
    public function crear(): int {
        $conexion = BD::obtenerConexion();

        $sql = "INSERT INTO usuarios (nombre, email, password, rol, activo)
                VALUES (?, ?, '1234', ?, ?)";

        $stmt = $conexion->prepare($sql);
        $stmt->execute([
            $this->nombre,
            $this->email,
            $this->rol,
            $this->activo ? 1 : 0
        ]);

        // Retorna el ID autogenerado en Postgres
        $this->usuarioId = (int)$conexion->lastInsertId();
        return $this->usuarioId;
    }

    // CORREGIDO: Sanitizado contra Inyección SQL
    public function actualizar() {
        if ($this->usuarioId === null) {
            throw new Exception("El usuario debe tener un ID para ser actualizado.");
        }

        $conexion = BD::obtenerConexion();

        $sql = "UPDATE usuarios SET nombre = ?, email = ?, rol = ?, activo = ? WHERE usuario_id = ?";
        $stmt = $conexion->prepare($sql);
        $stmt->execute([
            $this->nombre,
            $this->email,
            $this->rol,
            $this->activo ? 1 : 0,
            $this->usuarioId
        ]);
    }

    // CORREGIDO: Sanitizado contra Inyección SQL
    public function eliminar() {
        if ($this->usuarioId === null) {
            throw new Exception("El usuario debe tener un ID para ser eliminado.");
        }

        $conexion = BD::obtenerConexion();

        $sql = "DELETE FROM usuarios WHERE usuario_id = ?";
        $stmt = $conexion->prepare($sql);
        $stmt->execute([$this->usuarioId]);
    }

    // CORREGIDO: Sanitizado contra Inyección SQL
    public static function obtenerPorId($usuarioId): ?Usuario {
        $conexion = BD::obtenerConexion();
    
        $sql = "SELECT * FROM usuarios WHERE usuario_id = ?";
        $stmt = $conexion->prepare($sql);
        $stmt->execute([$usuarioId]);
    
        if ($fila = $stmt->fetch(PDO::FETCH_ASSOC)) {
            return new Usuario(
                $fila['nombre'],
                $fila['email'],
                $fila['rol'],
                (bool)$fila['activo'],
                $fila['usuario_id']
            );
        }
        return null;
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
                (bool)$fila['activo'],
                $fila['usuario_id']
            );
        }
        return $usuarios;
    }
}