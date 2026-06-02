<?php
require_once 'Usuario.php';

// Clase UsuarioBarbero que hereda de Usuario y representa a un barbero con permisos limitados para gestionar sus propias reservas
class UsuarioBarbero extends Usuario {
    public int $barberoId;

    public function __construct($nombre, $email, $barberoId, $activo = true, $usuarioId = null) {
        parent::__construct($nombre, $email, 'barbero', $activo, $usuarioId);
        $this->barberoId = $barberoId;
    }

    public function puedeGestionarSusReservas(): bool {
        return true;
    }

    public function getBarberoId(): int {
        return $this->barberoId;
    }

    public function puedeVerTodasLasReservas(): bool { 
        return true;
    }
    
}
?>