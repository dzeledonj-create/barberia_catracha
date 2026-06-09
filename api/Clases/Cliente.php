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

        if ($this->clienteId === null) {
            // La tabla tiene UNIQUE en telefono Y en email por separado, por lo que no es
            // posible cubrir ambas restricciones con un solo ON CONFLICT. En su lugar:
            // 1) buscamos un cliente existente que coincida por teléfono o por email,
            // 2) si lo encontramos lo actualizamos, 3) si no, insertamos uno nuevo.
            $existingId = null;

            // Búsqueda por teléfono (prioritaria: identificador más fiable)
            $stmtBuscar = $db->prepare("SELECT cliente_id FROM clientes WHERE telefono = ? LIMIT 1");
            $stmtBuscar->execute([$this->telefono]);
            $existingId = $stmtBuscar->fetchColumn() ?: null;

            // Si no apareció por teléfono, intentamos por email
            if ($existingId === null && $this->email !== null && $this->email !== '') {
                $stmtBuscar = $db->prepare("SELECT cliente_id FROM clientes WHERE email = ? LIMIT 1");
                $stmtBuscar->execute([$this->email]);
                $existingId = $stmtBuscar->fetchColumn() ?: null;
            }

            if ($existingId !== null) {
                $this->clienteId = (int)$existingId;
                $stmt = $db->prepare(
                    "UPDATE clientes SET nombre = ?, apellido = ?, telefono = ?, email = ?
                     WHERE cliente_id = ?"
                );
                return $stmt->execute([
                    $this->nombre, $this->apellido, $this->telefono, $this->email,
                    $this->clienteId,
                ]);
            }

            // Cliente nuevo: insertar
            $stmt = $db->prepare(
                "INSERT INTO clientes (nombre, apellido, telefono, email)
                 VALUES (?, ?, ?, ?)
                 RETURNING cliente_id"
            );
            $stmt->execute([$this->nombre, $this->apellido, $this->telefono, $this->email]);
            $this->clienteId = (int)$stmt->fetchColumn();
            return true;
        }
        // Si el cliente ya tiene un ID, actualizamos sus datos en la base de datos
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
        // Preparamos la consulta para buscar un cliente por su email
        $stmt = $db->prepare("SELECT * FROM clientes WHERE email = ? LIMIT 1");
        $stmt->execute([$email]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        // Si no se encuentra ningún cliente con ese email, retornamos null
        if (!$data) {
            return null;
        }
        // Si se encuentra un cliente, creamos una instancia de Cliente con los datos obtenidos y la retornamos
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
}