<?php
require_once 'BD.php';

class Cliente {
    public ?int $clienteId;
    public string $nombre;
    public string $apellido;
    public string $telefono;
    public ?string $email;
    public ?string $fechaRegistro;

    public function __construct($nombre, $apellido, $telefono, $email = null, $clienteId = null, $fechaRegistro = null) {
        $this->clienteId = $clienteId;
        $this->nombre = $nombre;
        $this->apellido = $apellido;
        $this->telefono = $telefono;
        $this->email = $email;
        $this->fechaRegistro = $fechaRegistro;
    }

    // Método para obtener el nombre completo del cliente
    public function getNombreCompleto(): string {
        return $this->nombre . " " . $this->apellido;
    }

    // Método para guardar o actualizar el cliente en la base de datos
    public function guardar(): bool {
        $db = BD::obtenerConexion();

        if ($this->clienteId === null) {
            $sql = "INSERT INTO clientes (nombre, apellido, telefono, email)
                    VALUES (?, ?, ?, ?)
                    RETURNING cliente_id";

            $stmt = $db->prepare($sql);
            $stmt->execute([$this->nombre, $this->apellido, $this->telefono, $this->email]);

            $this->clienteId = $stmt->fetchColumn();
            return true;
        }

        $sql = "UPDATE clientes 
                SET nombre = ?, apellido = ?, telefono = ?, email = ?
                WHERE cliente_id = ?";

        $stmt = $db->prepare($sql);
        return $stmt->execute([
            $this->nombre,
            $this->apellido,
            $this->telefono,
            $this->email,
            $this->clienteId
        ]);
    }

    // Método para eliminar el cliente de la base de datos
    public function eliminar(): bool {
        $db = BD::obtenerConexion();

        $stmt = $db->prepare("DELETE FROM clientes WHERE cliente_id = ?");
        return $stmt->execute([$this->clienteId]);
    }

    // Método para obtener todos los clientes
    public static function obtenerTodos(): array {
        $db = BD::obtenerConexion();

        $stmt = $db->query("SELECT * FROM clientes ORDER BY cliente_id DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Método para obtener un cliente por ID
    public static function obtenerPorId($clienteId): ?Cliente {
        $db = BD::obtenerConexion();

        $stmt = $db->prepare("SELECT * FROM clientes WHERE cliente_id = ?");
        $stmt->execute([$clienteId]);

        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$data) {
            return null;
        }

        return new Cliente(
            $data['nombre'],
            $data['apellido'],
            $data['telefono'],
            $data['email'],
            $data['cliente_id'],
            $data['fecha_registro']
        );
    }
}