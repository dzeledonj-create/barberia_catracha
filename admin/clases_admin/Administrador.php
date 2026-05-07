<?php
require_once 'Usuario.php';

class Administrador extends Usuario {

    public function __construct($nombre, $email, $activo = true, $usuarioId = null) {
        parent::__construct($nombre, $email, 'admin', $activo, $usuarioId);
    }

    public function puedeGestionarBarberos(): bool {
        return true;
    }

    public function puedeVerTodasLasReservas(): bool {
        return true;
    }
}
?>