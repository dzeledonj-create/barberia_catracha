<?php

class Pago {
    public int $pagoId;
    public Reserva $reserva;
    public float $monto;
    public string $metodoPago;
    public string $estadoPago;
    public string $fechaPago;
}