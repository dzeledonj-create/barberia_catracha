<?php

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../Clases/Reserva.php';

class ReservaTest extends TestCase
{
    public function testReservaEmpiezaPendiente()
    {
        $reserva = new Reserva(
            1,
            1,
            1,
            '2026-05-20 10:00:00'
        );

        $this->assertEquals('pendiente', $reserva->estado);
        $this->assertTrue($reserva->estaPendiente());
    }

    public function testCancelarReserva()
    {
        $reserva = new Reserva(
            1,
            1,
            1,
            '2026-05-20 10:00:00'
        );

        $reserva->cancelar();

        $this->assertEquals('cancelada', $reserva->estado);
    }
}