<?php
require_once __DIR__ . '/../../Clases/BD.php';

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

    // --- OPERACIONES CRUD ENCAPSULADAS ---

    /**
     * Inserta un nuevo usuario en la base de datos usando sentencias preparadas seguras.
     */
    public function crear(): void {
        $conexion = BD::obtenerConexion();

        $sql = "INSERT INTO usuarios (nombre, email, password, rol, activo)
                VALUES (?, ?, '1234', ?, ?)";

        $stmt = $conexion->prepare($sql);
        $stmt->execute([
            $this->nombre,
            $this->email,
            $this->rol,
            $this->activo ? 1 : 0
        ]);

        // Asignamos el ID generado automáticamente por la base de datos al objeto actual
        $this->usuarioId = (int)$conexion->lastInsertId();
    }

    /**
     * Actualiza los datos del usuario actual en la base de datos de manera segura.
     */
    public function actualizar(): void {
        if ($this->usuarioId === null) {
            throw new Exception("El usuario debe tener un ID para ser actualizado.");
        }

        $conexion = BD::obtenerConexion();

        $sql = "UPDATE usuarios
                SET nombre = ?,
                    email = ?,
                    rol = ?,
                    activo = ?
                WHERE usuario_id = ?";

        $stmt = $conexion->prepare($sql);
        $stmt->execute([
            $this->nombre,
            $this->email,
            $this->rol,
            $this->activo ? 1 : 0,
            $this->usuarioId
        ]);
    }

    /**
     * Elimina el registro del usuario actual en la base de datos.
     */
    public function eliminar(): void {
        if ($this->usuarioId === null) {
            throw new Exception("El usuario debe tener un ID para ser eliminado.");
        }

        $conexion = BD::obtenerConexion();

        $sql = "DELETE FROM usuarios WHERE usuario_id = ?";
        
        $stmt = $conexion->prepare($sql);
        $stmt->execute([$this->usuarioId]);
    }

    // --- MÉTODOS ESTÁTICOS DE CONSULTA (READ) ---

    /**
     * Busca un usuario por su ID y devuelve una instancia de la clase Usuario o null.
     */
    public static function obtenerPorId($usuarioId): ?Usuario {
        $conexion = BD::obtenerConexion();
    
        $sql = "SELECT * FROM usuarios WHERE usuario_id = ?";
        $stmt = $conexion->prepare($sql);
        $stmt->execute([$usuarioId]);
        
        $fila = $stmt->fetch(PDO::FETCH_ASSOC);
    
        if ($fila) {
            return new Usuario(
                $fila['nombre'],
                $fila['email'],
                $fila['rol'],
                (bool)$fila['activo'],
                (int)$fila['usuario_id']
            );
        }
        return null; // No se encontró el usuario
    }

    /**
     * Obtiene todos los usuarios registrados en el sistema.
     */
    public static function obtenerTodos(): array {
        $conexion = BD::obtenerConexion();
    
        $sql = "SELECT * FROM usuarios ORDER BY usuario_id DESC";
        $resultado = $conexion->query($sql);
    
        $usuarios = [];
        while ($fila = $resultado->fetch(PDO::FETCH_ASSOC)) {
            $usuarios[] = new Usuario(
                $fila['nombre'],
                $fila['email'],
                $fila['rol'],
                (bool)$fila['activo'],
                (int)$fila['usuario_id']
            );
        }
        return $usuarios;
    }
}