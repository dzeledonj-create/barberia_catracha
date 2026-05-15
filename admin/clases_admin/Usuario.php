<?php

class Usuario {

    public ?int $usuarioId;
    public string $nombre;
    public string $email;
    public string $rol;
    public bool $activo;

    // Array donde guardamos los usuarios
    public static array $usuarios = [];

    // ID automático
    public static int $ultimoId = 0;

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

    // =========================
    // CREATE
    // =========================

    public static function crear($nombre, $email, $rol, $activo = true) {

        self::$ultimoId++;

        $usuario = new Usuario(
            $nombre,
            $email,
            $rol,
            $activo,
            self::$ultimoId
        );

        self::$usuarios[self::$ultimoId] = $usuario;
    }

    // =========================
    // READ
    // =========================

    public static function listar() {
        return self::$usuarios;
    }

    public static function obtener($id) {

        if (isset(self::$usuarios[$id])) {
            return self::$usuarios[$id];
        }

        return null;
    }

    // =========================
    // UPDATE
    // =========================

    public static function editar($id, $nombre, $email, $rol, $activo) {

        if (isset(self::$usuarios[$id])) {

            self::$usuarios[$id]->nombre = $nombre;
            self::$usuarios[$id]->email = $email;
            self::$usuarios[$id]->rol = $rol;
            self::$usuarios[$id]->activo = $activo;
        }
    }

    // =========================
    // DELETE
    // =========================

    public static function eliminar($id) {

        if (isset(self::$usuarios[$id])) {
            unset(self::$usuarios[$id]);
        }
    }
}