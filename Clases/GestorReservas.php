<?php

class GestorReservas {
    private array $reservas = [];

    public function estaDisponible(
        Barbero $barbero,
        string $fechaHora,
        int $duracionMinutos
    ): bool {
        foreach ($this->reservas as $reserva) {
            if (
                $reserva->barbero->barberoId === $barbero->barberoId &&
                $reserva->fechaHora === $fechaHora &&
                $reserva->estado !== "cancelada"
            ) {
                return false;
            }
        }

        return true;
    }

    public function crearReserva(
        Cliente $cliente,
        Barbero $barbero,
        Servicio $servicio,
        string $fechaHora
    ): ?Reserva {
        if (!$this->estaDisponible($barbero, $fechaHora, $servicio->duracionMinutos)) {
            return null;
        }

        $reserva = new Reserva();
        $reserva->cliente = $cliente;
        $reserva->barbero = $barbero;
        $reserva->servicio = $servicio;
        $reserva->fechaHora = $fechaHora;
        $reserva->estado = "pendiente";
        $reserva->creadoEn = date("Y-m-d H:i:s");

        $this->reservas[] = $reserva;

        return $reserva;
    }
}