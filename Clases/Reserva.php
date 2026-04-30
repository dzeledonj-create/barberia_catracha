<?php

class Reserva {
    public int $reservaId;
    public Cliente $cliente;
    public Barbero $barbero;
    public Servicio $servicio;
    public string $fechaHora;
    public string $estado = "pendiente";
    public string $creadoEn;

    public function confirmar(): void {
        $this->estado = "confirmada";
    }

    public function cancelar(): void {
        $this->estado = "cancelada";
    }

    public function completar(): void {
        $this->estado = "completada";
    }
}