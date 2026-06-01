<?php

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../Clases/Servicio.php';

class ServicioTest extends TestCase
{
    public function testCrearServicio()
    {
        $servicio = new Servicio(
            "Corte Test",
            "Descripción test",
            15,
            30,
            null,
            "Corte"
        );

        $this->assertEquals("Corte Test", $servicio->nombre);
        $this->assertEquals("Descripción test", $servicio->descripcion);
        $this->assertEquals(15, $servicio->precio);
        $this->assertEquals(30, $servicio->duracionMinutos);
    }
}