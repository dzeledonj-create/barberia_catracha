<?php
require_once 'BD.php';

class Barbero {
    public ?int $barberoId;
    public ?int $usuarioId; // Guardamos el ID de usuario relacionado
    public string $nombre;
    public ?string $descripcion;
    public ?string $etiquetas;
    public ?string $especialidad;
    public ?string $fotoUrl;
    public bool $activo;
    public ?string $rol;
    public ?string $email;

    public function __construct($nombre, $especialidad = null, $fotoUrl = null, $activo = true, $barberoId = null, $descripcion = null, $etiquetas = null, $rol = null, $email = null, $usuarioId = null) {
        $this->barberoId = $barberoId;
        $this->usuarioId = $usuarioId;
        $this->nombre = $nombre;
        $this->especialidad = $especialidad;
        $this->fotoUrl = $fotoUrl;
        $this->activo = $activo;
        $this->descripcion = $descripcion;
        $this->etiquetas = $etiquetas;
        $this->rol = $rol;
        $this->email = $email;
    }

    public function estaActivo(): bool {
        return $this->activo;
    }

    // --- EL CRUD ENCAPSULADO TOTALMENTE AQUÍ ---
    public function guardar(): bool {
        $db = BD::obtenerConexion();

        try {
            $db->beginTransaction();

            if ($this->barberoId === null) {
                // 1. OPERACIÓN CREAR: Insertar primero en usuarios (obligatorio por el esquema)
                $sqlUsuario = "INSERT INTO usuarios (nombre, email, password, rol, activo) 
                               VALUES (?, ?, '1234', 'barbero', ?)";
                $stmtU = $db->prepare($sqlUsuario);
                $stmtU->execute([
                    $this->nombre,
                    $this->email,
                    $this->activo ? 1 : 0
                ]);
                $this->usuarioId = (int)$db->lastInsertId();

                // 2. Insertar en la tabla barberos vinculando el usuario_id obtenido
                $sqlBarbero = "INSERT INTO barberos (usuario_id, especialidad, foto_url, descripcion, etiquetas) 
                               VALUES (?, ?, ?, ?, ?)";
                $stmtB = $db->prepare($sqlBarbero);
                $stmtB->execute([
                    $this->usuarioId,
                    $this->especialidad,
                    $this->fotoUrl,
                    $this->descripcion,
                    $this->etiquetas
                ]);
                $this->barberoId = (int)$db->lastInsertId();

            } else {
                // 2. OPERACIÓN ACTUALIZAR: Modificar tabla barberos
                $sqlBarbero = "UPDATE barberos
                               SET especialidad = ?, foto_url = ?, descripcion = ?, etiquetas = ?
                               WHERE barbero_id = ?";
                $stmtB = $db->prepare($sqlBarbero);
                $stmtB->execute([
                    $this->especialidad,
                    $this->fotoUrl,
                    $this->descripcion,
                    $this->etiquetas,
                    $this->barberoId
                ]);

                // Modificar tabla usuarios (nombre, email y estado activo)
                $sqlUsuario = "UPDATE usuarios 
                               SET nombre = ?, email = ?, activo = ? 
                               WHERE usuario_id = (SELECT usuario_id FROM barberos WHERE barbero_id = ?)";
                $stmtU = $db->prepare($sqlUsuario);
                $stmtU->execute([
                    $this->nombre,
                    $this->email,
                    $this->activo ? 1 : 0,
                    $this->barberoId
                ]);
            }

            $db->commit();
            return true;
        } catch (Exception $e) {
            if ($db->inTransaction()) $db->rollBack();
            return false;
        }
    }

    public function eliminar(): bool {
        $db = BD::obtenerConexion();
        try {
            $db->beginTransaction();

            // Averiguamos el usuario_id asociado antes de borrar el perfil profesional
            $stmt = $db->prepare("SELECT usuario_id FROM barberos WHERE barbero_id = ?");
            $stmt->execute([$this->barberoId]);
            $uId = $stmt->fetchColumn();

            if ($uId) {
                // 1. Borramos el registro de la tabla barberos
                $stmtB = $db->prepare("DELETE FROM barberos WHERE barbero_id = ?");
                $stmtB->execute([$this->barberoId]);

                // 2. Borramos el registro de la tabla usuarios
                $stmtU = $db->prepare("DELETE FROM usuarios WHERE usuario_id = ?");
                $stmtU->execute([$uId]);
            }

            $db->commit();
            return true;
        } catch (Exception $e) {
            if ($db->inTransaction()) $db->rollBack();
            return false;
        }
    }

    public static function obtenerTodos(): array {
        $db = BD::obtenerConexion();
        // Cambiamos INNER JOIN por LEFT JOIN para incluir usuarios que no están en la tabla barberos (como los admins)
        $stmt = $db->query("SELECT u.usuario_id, u.nombre, u.activo, u.rol, u.email, 
                                b.barbero_id, b.especialidad, b.foto_url, b.descripcion, b.etiquetas 
                            FROM usuarios u
                            LEFT JOIN barberos b ON u.usuario_id = b.usuario_id 
                            WHERE u.rol IN ('barbero', 'admin')
                            ORDER BY u.usuario_id DESC");
        
        $barberos = [];
        while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $barberos[] = new Barbero(
                $data['nombre'],
                $data['especialidad'] ?? 'Administrador', // Valor por defecto si es admin
                $data['foto_url'] ?? 'assets/img/default-user.jpg', // Foto por defecto si es admin
                (bool)$data['activo'],
                $data['barbero_id'] ? (int)$data['barbero_id'] : null,
                $data['descripcion'] ?? 'Administrador del sistema.',
                $data['etiquetas'] ?? '',
                $data['rol'],
                $data['email'],
                (int)$data['usuario_id']
            );
        }
        return $barberos;
    }

    public static function obtenerActivos(): array {
        $db = BD::obtenerConexion();
        $stmt = $db->query("SELECT b.*, u.nombre, u.activo, u.rol, u.email 
                            FROM barberos b
                            INNER JOIN usuarios u ON b.usuario_id = u.usuario_id
                            WHERE u.activo = TRUE AND u.rol = 'barbero'");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function obtenerPorId($barberoId): ?Barbero {
        $db = BD::obtenerConexion();
        $stmt = $db->prepare("SELECT b.*, u.nombre, u.activo, u.rol, u.email 
                              FROM barberos b 
                              INNER JOIN usuarios u ON b.usuario_id = u.usuario_id 
                              WHERE b.barbero_id = ?");
        $stmt->execute([$barberoId]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$data) return null;

        return new Barbero(
            $data['nombre'],
            $data['especialidad'],
            $data['foto_url'],
            (bool)$data['activo'],
            $data['barbero_id'],
            $data['descripcion'],
            $data['etiquetas'],
            $data['rol'],
            $data['email'],
            $data['usuario_id']
        );
    }
}