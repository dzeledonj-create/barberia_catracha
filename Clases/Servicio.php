<?php
require_once 'BD.php';

class Servicio {
    public ?int $servicioId;
    public string $nombre;
    public ?string $descripcion;
    public float $precio;
    public int $duracionMinutos;

    public function __construct($nombre, $descripcion, $precio, $duracionMinutos, $servicioId = null) {
        $this->servicioId = $servicioId;
        $this->nombre = $nombre;
        $this->descripcion = $descripcion;
        $this->precio = $precio;
        $this->duracionMinutos = $duracionMinutos;
    }

    // Métodos para formatear precio y duración
    public function formatearPrecio(): string {
        return number_format($this->precio, 2) . " €";
    }

    public function formatearDuracion(): string {
        return $this->duracionMinutos . " min";
    }

    public function esServicioLargo(): bool {
        return $this->duracionMinutos >= 60;
    }

    // Método para guardar o actualizar el servicio en la base de datos
    public function guardar(): bool {
        $db = BD::obtenerConexion();

        if ($this->servicioId === null) {
            $sql = "INSERT INTO servicios (nombre, descripcion, precio, duracion_minutos)
                    VALUES (?, ?, ?, ?)
                    RETURNING servicio_id";

            $stmt = $db->prepare($sql);
            $stmt->execute([
                $this->nombre,
                $this->descripcion,
                $this->precio,
                $this->duracionMinutos
            ]);

            $this->servicioId = $stmt->fetchColumn();
            return true;
        }

        $sql = "UPDATE servicios
                SET nombre = ?, descripcion = ?, precio = ?, duracion_minutos = ?
                WHERE servicio_id = ?";

        $stmt = $db->prepare($sql);
        return $stmt->execute([
            $this->nombre,
            $this->descripcion,
            $this->precio,
            $this->duracionMinutos,
            $this->servicioId
        ]);
    }

    // Método para eliminar el servicio de la base de datos
    public function eliminar(): bool {
        $db = BD::obtenerConexion();

        $stmt = $db->prepare("DELETE FROM servicios WHERE servicio_id = ?");
        return $stmt->execute([$this->servicioId]);
    }

    // Método para obtener todos los servicios de la base de datos
    public static function obtenerTodos(): array {
        $db = BD::obtenerConexion();

        $stmt = $db->query("SELECT * FROM servicios ORDER BY servicio_id DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Método para obtener un servicio por ID
    public static function obtenerPorId($servicioId): ?Servicio {
        $db = BD::obtenerConexion();

        $stmt = $db->prepare("SELECT * FROM servicios WHERE servicio_id = ?");
        $stmt->execute([$servicioId]);

        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$data) {
            return null;
        }

        return new Servicio(
            $data['nombre'],
            $data['descripcion'],
            $data['precio'],
            $data['duracion_minutos'],
            $data['servicio_id']
        );
    }
}