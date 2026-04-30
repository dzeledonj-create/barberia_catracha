<?php
require_once 'BD.php';

class Cliente {
    public int $clienteId;
    public string $nombre;
    public string $apellido;
    public string $telefono;
    public ?string $email;
    public string $fechaRegistro;
}

//Crear cliente
public static function crear($nombre, $apellido, $telefono, $email) {
        $db = BD::conectar();

        $sql = "INSERT INTO clientes (nombre, apellido, telefono, email)
                VALUES (?, ?, ?, ?)";

        $stmt = $db->prepare($sql);
        $stmt->execute([$nombre, $apellido, $telefono, $email]);
    }

//Obtener todos los clientes
public static function obtenerTodos() {
        $db = BD::conectar();

        $stmt = $db->query("SELECT * FROM clientes");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

//Obtener cliente por ID
public static function obtenerPorId($clienteId) {
        $db = BD::conectar();

        $stmt = $db->prepare("SELECT * FROM clientes WHERE clienteId = ?");
        $stmt->execute([$clienteId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

//Actualizar cliente
public static function actualizar($clienteId, $nombre, $apellido, $telefono, $email) {
        $db = BD::conectar();

        $sql = "UPDATE clientes SET nombre = ?, apellido = ?, telefono = ?, email = ?
                WHERE clienteId = ?";

        $stmt = $db->prepare($sql);
        $stmt->execute([$nombre, $apellido, $telefono, $email, $clienteId]);
    }

//Eliminar cliente
public static function eliminar($clienteId) {
        $db = BD::conectar();

        $stmt = $db->prepare("DELETE FROM clientes WHERE clienteId = ?");
        $stmt->execute([$clienteId]);
    }
    

