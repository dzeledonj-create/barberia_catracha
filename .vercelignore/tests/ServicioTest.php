<?php

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../../api/Clases/Servicio.php';

//extends TestCase es necesario para que PHPUnit reconozca esta clase como una clase de prueba
class ServicioTest extends TestCase
{
    // Prueba para verificar que se puede crear un nuevo servicio correctamente
    public function testCrearServicio()
    {
        // Crear un nuevo servicio con datos de prueba
        $servicio = new Servicio(
            "Corte Test",
            "Descripción test",
            15,
            30,
            null,
            "Corte"
        );

        //Para verificar que las propiedades del servicio se asignaron correctamente
        $this->assertEquals("Corte Test", $servicio->getNombre());// Verificar que el nombre del servicio es correcto
        $this->assertEquals("Descripción test", $servicio->getDescripcion());
        $this->assertEquals(15, $servicio->getPrecio());
        $this->assertEquals(30, $servicio->getDuracionMinutos());
    }
}