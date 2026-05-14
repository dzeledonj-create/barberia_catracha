<?php
require_once 'Usuario.php';

class Administrador extends Usuario {

    public function __construct($nombre, $email, $activo = true, $usuarioId = null) {
        parent::__construct($nombre, $email, 'admin', $activo, $usuarioId);
    }

    public function puedeVerTodasLasReservas(): bool {
        return true;
    }
    
    public function puedeGestionarReservas(): bool { 
        return true; 
    }

    // Barberos y equipo
    public function puedeGestionarEquipo(): bool { 
        return true; 
    }

    // Servicios
    public function puedeGestionarServicios(): bool { 
        return true; 
    }

    // Galería
    public function puedeGestionarGaleria(): bool { 
        return true; 
    }

    // Blog
    public function puedeGestionarBlog(): bool { 
        return true; 
    }

    // Reseñas
    public function puedeGestionarResenas(): bool { 
        return true; 
    }

    // Ubicación
    public function puedeGestionarUbicacion(): bool { 
        return true; 
    }
}
?>