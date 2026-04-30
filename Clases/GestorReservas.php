<?php
require_once 'BD.php';
require_once 'Reserva.php';

class GestorReservas {

    // Método para crear una nueva reserva
    public static function crearReserva($clienteId, $barberoId, $servicioId, $fechaHora): bool {
        $reserva = new Reserva($clienteId, $barberoId, $servicioId, $fechaHora);

        return $reserva->guardar();
    }

    // Métodos para cambiar el estado de la reserva
    public static function confirmarReserva($reservaId): bool {
        $reserva = Reserva::obtenerPorId($reservaId);

        if (!$reserva) {
            return false;
        }

        $reserva->confirmar();
        return $reserva->guardar();
    }

    // Método para cancelar una reserva
    public static function cancelarReserva($reservaId): bool {
        $reserva = Reserva::obtenerPorId($reservaId);

        if (!$reserva) {
            return false;
        }

        $reserva->cancelar();
        return $reserva->guardar();
    }

    // Método para completar una reserva
    public static function completarReserva($reservaId): bool {
        $reserva = Reserva::obtenerPorId($reservaId);

        if (!$reserva) {
            return false;
        }

        $reserva->completar();
        return $reserva->guardar();
    }

    //Método para obtener Agenda del Barbero
    public static function obtenerAgendaBarbero($barberoId): array {
        $db = BD::obtenerConexion();

        $sql = "SELECT r.*, 
                       c.nombre AS cliente_nombre,
                       c.apellido AS cliente_apellido,
                       s.nombre AS servicio_nombre,
                       s.duracion_minutos
                FROM reservas r
                JOIN clientes c ON r.cliente_id = c.cliente_id
                JOIN servicios s ON r.servicio_id = s.servicio_id
                WHERE r.barbero_id = ?
                AND r.estado != 'cancelada'
                ORDER BY r.fecha_hora ASC";

        $stmt = $db->prepare($sql);
        $stmt->execute([$barberoId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Método para obtener las reservas de un día específico
    public static function obtenerReservasDelDia($fecha): array {
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
                WHERE DATE(r.fecha_hora) = ?
                ORDER BY r.fecha_hora ASC";

        $stmt = $db->prepare($sql);
        $stmt->execute([$fecha]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Método para contar reservas pendientes
    public static function contarReservasPendientes(): int {
        $db = BD::obtenerConexion();

        $stmt = $db->query("SELECT COUNT(*) FROM reservas WHERE estado = 'pendiente'");
        return (int) $stmt->fetchColumn();
    }

    // Método para validar que la reserva esté dentro del horario de la barbería
    public static function validarHorarioBarberia($fechaHora): bool {
        $hora = date("H:i", strtotime($fechaHora));

        return $hora >= "09:00" && $hora <= "20:00";
    }

    // Método para verificar si el barbero está disponible en la fecha y hora dada para el servicio seleccionado
    public static function puedeReservar($barberoId, $servicioId, $fechaHora): bool {
        if (!self::validarHorarioBarberia($fechaHora)) {
            return false;
        }

        return Reserva::estaDisponible($barberoId, $fechaHora, $servicioId);
    }

    // Método para eliminar una reserva
    public static function eliminarReserva($reservaId): bool {
        $reserva = Reserva::obtenerPorId($reservaId);

        if (!$reserva) {
            return false;
        }

        return $reserva->eliminar();
    }
}