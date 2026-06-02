<?php
require_once __DIR__ . '/BD.php';

// Clase para manejar los datos de ubicación de la barbería, como dirección, teléfono, WhatsApp y mapa embebido
class DatosUbicacion {

// Función para obtener los datos de ubicación desde la base de datos
    public static function obtener() {
        $db = BD::obtenerConexion();

        $stmt = $db->query("SELECT * FROM datos_ubicacion LIMIT 1");
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Función para actualizar los datos de ubicación en la base de datos
    public static function actualizar($direccion, $telefono, $whatsapp, $mapaEmbed) {
        $db = BD::obtenerConexion();

        $sql = "UPDATE datos_ubicacion 
                SET direccion = ?, telefono = ?, whatsapp = ?, mapa_embed = ?
                WHERE id = 1";

        $stmt = $db->prepare($sql);
        return $stmt->execute([$direccion, $telefono, $whatsapp, $mapaEmbed]);
    }
}