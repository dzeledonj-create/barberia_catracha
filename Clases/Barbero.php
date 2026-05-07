<?php
require_once 'BD.php';

class Barbero {
    public ?int $barberoId;
    public string $nombre;
    public ?string $descripcion;
    public ?string $etiquetas;
    public ?string $especialidad;
    public ?string $fotoUrl;
    public bool $activo;

    public function __construct($nombre, $especialidad = null, $fotoUrl = null, $activo = true, $barberoId = null, $descripcion = null, $etiquetas = null) {
        $this->barberoId = $barberoId;
        $this->nombre = $nombre;
        $this->especialidad = $especialidad;
        $this->fotoUrl = $fotoUrl;
        $this->activo = $activo;
        $this->descripcion = $descripcion;
        $this->etiquetas = $etiquetas;
    }

    // Métodos para activar/desactivar al barbero
    public function estaActivo(): bool {
        return $this->activo;
    }

    // Métodos para activar/desactivar al barbero
    public function activar(): void {
        $this->activo = true;
    }

    // Métodos para activar/desactivar al barbero
    public function desactivar(): void {
        $this->activo = false;
    }

    // Método para guardar o actualizar el barbero en la base de datos
    public function guardar(): bool {
        $db = BD::obtenerConexion();

        if ($this->barberoId === null) {
            $sql = "INSERT INTO barberos (nombre, especialidad, descripcion, etiquetas, foto_url, activo)
                    VALUES (?, ?, ?, ?, ?, ?)
                    RETURNING barbero_id";

            $stmt = $db->prepare($sql);
            $stmt->execute([
                $this->nombre,
                $this->especialidad,
                $this->descripcion,
                $this->etiquetas,
                $this->fotoUrl,
                $this->activo,
            ]);

            $this->barberoId = $stmt->fetchColumn();
            return true;
        }

        $sql = "UPDATE barberos
                SET nombre = ?, especialidad = ?, foto_url = ?, activo = ?, descripcion = ?, etiquetas = ?
                WHERE barbero_id = ?";

        $stmt = $db->prepare($sql);
        return $stmt->execute([
            $this->nombre,
            $this->especialidad,
            $this->fotoUrl,
            $this->activo,
            $this->descripcion,
            $this->etiquetas,
            $this->barberoId
        ]);
    }

    // Método para eliminar el barbero de la base de datos
    public function eliminar(): bool {
        $db = BD::obtenerConexion();

        $stmt = $db->prepare("DELETE FROM barberos WHERE barbero_id = ?");
        return $stmt->execute([$this->barberoId]);
    }

    // Método para obtener todos los barberos
    public static function obtenerTodos(): array {
        $db = BD::obtenerConexion();

        $stmt = $db->query("SELECT * FROM barberos ORDER BY barbero_id DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Método para obtener solo los barberos activos
    public static function obtenerActivos(): array {
        $db = BD::obtenerConexion();

        $stmt = $db->query("SELECT * FROM barberos WHERE activo = TRUE");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Método para obtener un barbero por ID
    public static function obtenerPorId($barberoId): ?Barbero {
        $db = BD::obtenerConexion();

        $stmt = $db->prepare("SELECT * FROM barberos WHERE barbero_id = ?");
        $stmt->execute([$barberoId]);

        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$data) {
            return null;
        }

        return new Barbero(
            $data['nombre'],
            $data['especialidad'],
            $data['foto_url'],
            $data['activo'],
            $data['barbero_id'],
            $data['descripcion'],
            $data['etiquetas']
        );
    }
}