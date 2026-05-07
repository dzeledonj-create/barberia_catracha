<?php
class Usuario {
    public ?int $usuarioId;
    public string $nombre;
    public string $email;
    public string $rol;
    public bool $activo;

    public function __construct($nombre, $email, $rol, $activo = true, $usuarioId = null) {
        $this->usuarioId = $usuarioId;
        $this->nombre = $nombre;
        $this->email = $email;
        $this->rol = $rol;
        $this->activo = $activo;
    }

    public function estaActivo(): bool {
        return $this->activo;
    }

    public function getNombre(): string {
        return $this->nombre;
    }
}
?>