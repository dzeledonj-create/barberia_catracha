<?php
require_once __DIR__ . '/BD.php';

class Cliente {
    private ?int $clienteId;
    private string $nombre;
    private string $apellido;
    private string $telefono;
    private ?string $email;
    private ?string $fechaRegistro;

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

    // Getters y setters para las propiedades del cliente
    public function getClienteId(): ?int {
        return $this->clienteId;
    }

    public function setClienteId(?int $clienteId): void {
        $this->clienteId = $clienteId;
    }

    public function getNombre(): string {
        return $this->nombre;
    }

    public function setNombre(string $nombre): void {
        $this->nombre = $nombre;
    }

    public function getApellido(): string {
        return $this->apellido;
    }

    public function setApellido(string $apellido): void {
        $this->apellido = $apellido;
    }

    public function getTelefono(): string {
        return $this->telefono;
    }

    public function setTelefono(string $telefono): void {
        $this->telefono = $telefono;
    }

    public function getEmail(): ?string {
        return $this->email;
    }

    public function setEmail(?string $email): void {
        $this->email = $email;
    }

    public function getFechaRegistro(): ?string {
        return $this->fechaRegistro;
    }

    public function setFechaRegistro(?string $fechaRegistro): void {
        $this->fechaRegistro = $fechaRegistro;
    }

    // Método para guardar o actualizar el cliente en la base de datos
    public function guardar(): bool {
        $db = BD::obtenerConexion();

        // Si el cliente no tiene un ID, es una creación; de lo contrario, es una actualización
        if ($this->clienteId === null) {
            // Antes de crear un nuevo cliente, verificamos si ya existe uno con el mismo email para evitar duplicados
            if ($this->email) {
                // Verificar si ya existe un cliente con el mismo email para evitar duplicados
                $existingCliente = self::obtenerPorEmail($this->email);
                if ($existingCliente) {
                    // Si ya existe un cliente con ese email, actualizamos sus datos en lugar de crear uno nuevo
                    $this->clienteId = $existingCliente->clienteId;
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
            }

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

    // Método para obtener un cliente por su email
    public static function obtenerPorEmail(string $email): ?Cliente {
        $db = BD::obtenerConexion();

        $stmt = $db->prepare("SELECT * FROM clientes WHERE email = ? LIMIT 1");
        $stmt->execute([$email]);
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
     public function getClienteId(): int {
        return $this->clienteId;
    }
}