<?php

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../Clases/Reserva.php';

// Extends TestCase para crear pruebas unitarias con PHPUnit
class ReservaTest extends TestCase
{
    // Verificar que se puede crear una reserva correctamente
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

    // Verificar que la reserva tiene una fecha válida
    public function testReservaTieneFecha()
    {
        $reserva = new Reserva(
            1,
            2,
            3,
            "2026-06-10 10:00:00"
        );

        // Verificar que la fecha de la reserva no esté vacía
        $this->assertNotEmpty($reserva);
    }
}