<?php
require_once __DIR__ . '/BD.php';

class Servicio {
    // Propiedades del servicio
    private ?int $servicioId;
    private string $nombre;
    private ?string $descripcion;
    private float $precio;
    private int $duracionMinutos;
    private ?string $categoria;

    // Constructor para inicializar las propiedades del servicio
    public function __construct($nombre, $descripcion, $precio, $duracionMinutos, $servicioId = null, $categoria = null) {
        $this->servicioId = $servicioId;
        $this->nombre = $nombre;
        $this->descripcion = $descripcion;
        $this->precio = $precio;
        $this->duracionMinutos = $duracionMinutos;
        $this->categoria = $categoria;
    }

    // Métodos para formatear el precio y la duración del servicio
    public function formatearPrecio(): string {
        // Formatea el precio con dos decimales y el símbolo de euro
        return number_format($this->precio, 2) . " €";
    }

    public function formatearDuracion(): string {
        // Formatea la duración en minutos
        return $this->duracionMinutos . " min";
    }

    public function esServicioLargo(): bool {
        return $this->duracionMinutos >= 60;
    }

    // Método para guardar o actualizar el servicio en la base de datos
    public function guardar(): bool {
        $db = BD::obtenerConexion();

        if ($this->servicioId === null) {
            $sql = "INSERT INTO servicios (nombre, descripcion, precio, duracion_minutos, categoria)
                    VALUES (?, ?, ?, ?, ?)
                    RETURNING servicio_id";

            $stmt = $db->prepare($sql);
            $stmt->execute([
                $this->nombre,
                $this->descripcion,
                $this->precio,
                $this->duracionMinutos,
                $this->categoria
            ]);

            $this->servicioId = $stmt->fetchColumn();
            return true;
        }

        // Si el servicio ya tiene un ID, actualizamos el registro existente
        $sql = "UPDATE servicios
                SET nombre = ?, descripcion = ?, precio = ?, duracion_minutos = ?, categoria = ?
                WHERE servicio_id = ?";

        $stmt = $db->prepare($sql);
        return $stmt->execute([
            $this->nombre,
            $this->descripcion,
            $this->precio,
            $this->duracionMinutos,
            $this->categoria,
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

        $stmt = $db->query("SELECT * FROM servicios ORDER BY categoria ASC, nombre ASC");
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

        // Si se encuentra el servicio, se crea una instancia de la clase Servicio con los datos obtenidos
        return new Servicio(
            $data['nombre'],
            $data['descripcion'],
            $data['precio'],
            $data['duracion_minutos'],
            $data['servicio_id'],
            $data['categoria']
        );
    }
}