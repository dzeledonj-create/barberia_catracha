<?php
require_once 'Usuario.php';

// Clase Administrador que hereda de Usuario y tiene permisos completos para gestionar la barbería
class Administrador extends Usuario {

// El constructor del administrador establece el rol como 'admin' y permite configurar su estado activo
    public function __construct($nombre, $email, $activo = true, $usuarioId) {
        parent::__construct($nombre, $email, 'admin', $activo, $usuarioId);
    }

    // Funciones específicas del administrador para verificar permisos en diferentes áreas de gestión
    public function puedeVerTodasLasReservas(): bool {
        return true;
    }
    
    // Función para verificar si el administrador puede gestionar las reservas
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