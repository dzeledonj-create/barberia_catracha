<?php

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../Clases/Reserva.php';

class ReservaTest extends TestCase
{
    public function testCrearReservaEsObjetoReserva()
    {
        $reserva = new Reserva(
            1,
            2,
            3,
            "2026-06-10 10:00:00"
        );

        $this->assertInstanceOf(Reserva::class, $reserva);
    }

    public function testReservaTieneFecha()
    {
        $reserva = new Reserva(
            1,
            2,
            3,
            "2026-06-10 10:00:00"
        );

        $this->assertNotEmpty($reserva);
    }
}