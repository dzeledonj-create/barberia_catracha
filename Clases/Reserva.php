<?php
require_once 'BD.php';
require_once 'Cliente.php';
require_once 'Barbero.php';

class Reserva {
    public ?int $reservaId;
    public int $clienteId;
    public int $barberoId;
    public int $servicioId;
    public string $fechaHora;
    public string $estado;
    public ?string $creadoEn;

    public function __construct($clienteId, $barberoId, $servicioId, $fechaHora, $estado = "pendiente", $reservaId = null, $creadoEn = null) {
        $this->reservaId = $reservaId;
        $this->clienteId = $clienteId;
        $this->barberoId = $barberoId;
        $this->servicioId = $servicioId;
        $this->fechaHora = $fechaHora;
        $this->estado = $estado;
        $this->creadoEn = $creadoEn;
    }

    // Métodos para cambiar el estado de la reserva
    public function confirmar(): void {
        $this->estado = "confirmada";
    }

    // Método para cambiar el estado de la reserva
    public function cancelar(): void {
        $this->estado = "cancelada";
    }

    // Método para cambiar el estado de la reserva
    public function completar(): void {
        $this->estado = "completada";
    }

    // Método para verificar si la reserva está pendiente
    public function estaPendiente(): bool {
        return $this->estado === "pendiente";
    }

    // Método para guardar o actualizar la reserva en la base de datos
    public function guardar(): bool {
        if (!self::estaDisponible($this->barberoId, $this->fechaHora, $this->servicioId, $this->reservaId)) {
            return false;
        }

        $db = BD::obtenerConexion();

        if ($this->reservaId === null) {
            $sql = "INSERT INTO reservas (cliente_id, barbero_id, servicio_id, fecha_hora, estado)
                    VALUES (?, ?, ?, ?, ?)
                    RETURNING reserva_id";

            $stmt = $db->prepare($sql);
            $stmt->execute([
                $this->clienteId,
                $this->barberoId,
                $this->servicioId,
                $this->fechaHora,
                $this->estado
            ]);

            $this->reservaId = $stmt->fetchColumn();
            return true;
        }

        $sql = "UPDATE reservas
                SET cliente_id = ?, barbero_id = ?, servicio_id = ?, fecha_hora = ?, estado = ?
                WHERE reserva_id = ?";

        $stmt = $db->prepare($sql);
        return $stmt->execute([
            $this->clienteId,
            $this->barberoId,
            $this->servicioId,
            $this->fechaHora,
            $this->estado,
            $this->reservaId
        ]);
    }

    // Método para obtener todas las reservas con detalles de cliente, barbero y servicio
    public static function obtenerTodas(): array {
        $db = BD::obtenerConexion();

        $sql = "SELECT r.*, 
                       c.nombre AS cliente_nombre,
                       c.apellido AS cliente_apellido,
                       b.nombre AS barbero_nombre,
                       s.nombre AS servicio_nombre
                FROM reservas r
                JOIN clientes c ON r.cliente_id = c.cliente_id
                JOIN barberos b ON r.barbero_id = b.barbero_id
                JOIN servicios s ON r.servicio_id = s.servicio_id
                ORDER BY r.fecha_hora DESC";

        return $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    // Método para obtener una reserva por ID
    public static function obtenerPorId($reservaId): ?Reserva {
        $db = BD::obtenerConexion();

        $stmt = $db->prepare("SELECT * FROM reservas WHERE reserva_id = ?");
        $stmt->execute([$reservaId]);

        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$data) {
            return null;
        }

        return new Reserva(
            $data['cliente_id'],
            $data['barbero_id'],
            $data['servicio_id'],
            $data['fecha_hora'],
            $data['estado'],
            $data['reserva_id'],
            $data['creado_en']
        );
    }

    // Método para verificar si el barbero está disponible en la fecha y hora dada para el servicio seleccionado
    public static function estaDisponible($barberoId, $fechaHora, $servicioId, $reservaId = null): bool {
        $db = BD::obtenerConexion();

        $stmtServicio = $db->prepare("SELECT duracion_minutos FROM servicios WHERE servicio_id = ?");
        $stmtServicio->execute([$servicioId]);
        $duracion = $stmtServicio->fetchColumn();

        if (!$duracion) {
            return false;
        }

        $finNueva = date("Y-m-d H:i:s", strtotime($fechaHora . " + $duracion minutes"));

        $sql = "SELECT COUNT(*)
                FROM reservas r
                JOIN servicios s ON r.servicio_id = s.servicio_id
                WHERE r.barbero_id = ?
                AND r.estado != 'cancelada'
                AND (? IS NULL OR r.reserva_id != ?)
                AND (
                    ? < (r.fecha_hora + (s.duracion_minutos || ' minutes')::interval)
                    AND ? > r.fecha_hora
                )";

        $stmt = $db->prepare($sql);
        $stmt->execute([
            $barberoId,
            $reservaId,
            $reservaId,
            $fechaHora,
            $finNueva
        ]);

        return $stmt->fetchColumn() == 0;
    }

    // Método para obtener todas las reservas con detalles para el panel de administración
    public static function obtenerTodasConDetalles(): array {
        $db = BD::obtenerConexion();

        // Hacemos JOIN con usuarios para obtener el nombre real del barbero (u.nombre)
        $sql = "SELECT r.reserva_id, 
                       r.fecha_hora, 
                       r.estado,
                       (c.nombre || ' ' || c.apellido) AS cliente,
                       u.nombre AS barbero, 
                       s.nombre AS servicio
                FROM reservas r
                INNER JOIN clientes c ON r.cliente_id = c.cliente_id
                INNER JOIN servicios s ON r.servicio_id = s.servicio_id
                LEFT JOIN barberos b ON r.barbero_id = b.barbero_id
                LEFT JOIN usuarios u ON b.usuario_id = u.usuario_id
                ORDER BY r.fecha_hora DESC";

        return $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }


    // Métodos para cambiar el estado de la reserva desde el panel de administración
    public static function cambiarEstado($reservaId, $estado) {
        $db = BD::obtenerConexion();

        $sql = "UPDATE reservas SET estado = ? WHERE reserva_id = ?";
        $stmt = $db->prepare($sql);
        return $stmt->execute([$estado, $reservaId]);
    }

    // Método para eliminar una reserva desde el panel de administración
    public static function eliminar($reservaId) {
        $db = BD::obtenerConexion();

        $stmt = $db->prepare("DELETE FROM reservas WHERE reserva_id = ?");
        return $stmt->execute([$reservaId]);
    }

    // para obtener por barbero
    public static function contarTotalPorBarbero(int $barberoId): int {
        $stmt = BD::obtenerConexion()->prepare(
            "SELECT COUNT(*) FROM reservas WHERE barbero_id = ?"
        );
        $stmt->execute([$barberoId]);
        return (int) $stmt->fetchColumn();
    }
 
    public static function contarPorEstadoYBarbero(string $estado, int $barberoId): int {
        $stmt = BD::obtenerConexion()->prepare(
            "SELECT COUNT(*) FROM reservas WHERE estado = ? AND barbero_id = ?"
        );
        $stmt->execute([$estado, $barberoId]);
        return (int) $stmt->fetchColumn();
    }
 
    public static function obtenerRecientesPorBarbero(int $barberoId, int $limite = 5): array {
        $stmt = BD::obtenerConexion()->prepare("
            SELECT r.reserva_id, r.fecha_hora, r.estado,
                   c.nombre || ' ' || c.apellido AS cliente,
                   b.nombre AS barbero,
                   s.nombre AS servicio
            FROM reservas r
            JOIN clientes  c ON r.cliente_id  = c.cliente_id
            JOIN barberos  b ON r.barbero_id  = b.barbero_id
            JOIN servicios s ON r.servicio_id = s.servicio_id
            WHERE r.barbero_id = ?
            ORDER BY r.creado_en DESC
            LIMIT ?
        ");
        $stmt->execute([$barberoId, $limite]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function contarPorEstado(string $estado): int {
        $stmt = BD::obtenerConexion()->prepare(
            "SELECT COUNT(*) FROM reservas WHERE estado = ?"
        );
        $stmt->execute([$estado]);
        return (int) $stmt->fetchColumn();
    }
 
    public static function obtenerRecientes(int $limite = 5): array {
        $stmt = BD::obtenerConexion()->prepare("
            SELECT r.reserva_id, r.fecha_hora, r.estado,
                   c.nombre || ' ' || c.apellido AS cliente,
                   b.nombre AS barbero,
                   s.nombre AS servicio
            FROM reservas r
            JOIN clientes  c ON r.cliente_id  = c.cliente_id
            JOIN barberos  b ON r.barbero_id  = b.barbero_id
            JOIN servicios s ON r.servicio_id = s.servicio_id
            ORDER BY r.creado_en DESC
            LIMIT ?
        ");
        $stmt->execute([$limite]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}