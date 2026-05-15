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

    // Método para comprobar si está activo
    public function estaActivo(): bool {
        return $this->activo;
    }

    // Método para activar usuario
    public function activar(): void {
        $this->activo = true;
    }

    // Método para desactivar usuario
    public function desactivar(): void {
        $this->activo = false;
    }

    // Método para guardar o actualizar usuario
    public function guardar(): bool {

        $db = BD::obtenerConexion();

        // INSERT
        if ($this->usuarioId === null) {

            $sql = "INSERT INTO usuarios (nombre, email, rol, activo)
                    VALUES (?, ?, ?, ?)
                    RETURNING usuario_id";

            $stmt = $db->prepare($sql);

            $stmt->execute([
                $this->nombre,
                $this->email,
                $this->rol,
                $this->activo
            ]);

            $this->usuarioId = $stmt->fetchColumn();

            return true;
        }

        // UPDATE
        $sql = "UPDATE usuarios
                SET nombre = ?, email = ?, rol = ?, activo = ?
                WHERE usuario_id = ?";

        $stmt = $db->prepare($sql);

        return $stmt->execute([
            $this->nombre,
            $this->email,
            $this->rol,
            $this->activo,
            $this->usuarioId
        ]);
    }

    // Método para eliminar usuario
    public function eliminar(): bool {

        $db = BD::obtenerConexion();

        $stmt = $db->prepare(
            "DELETE FROM usuarios WHERE usuario_id = ?"
        );

        return $stmt->execute([$this->usuarioId]);
    }

    // Obtener todos los usuarios
    public static function obtenerTodos(): array {

        $db = BD::obtenerConexion();

        $stmt = $db->query(
            "SELECT * FROM usuarios ORDER BY usuario_id DESC"
        );

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtener usuario por ID
    public static function obtenerPorId($usuarioId): ?Usuario {

        $db = BD::obtenerConexion();

        $stmt = $db->prepare(
            "SELECT * FROM usuarios WHERE usuario_id = ?"
        );

        $stmt->execute([$usuarioId]);

        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$data) {
            return null;
        }

        return new Usuario(
            $data['nombre'],
            $data['email'],
            $data['rol'],
            $data['activo'],
            $data['usuario_id']
        );
    }
}

?>