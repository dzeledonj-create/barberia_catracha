<?php
require_once 'BD.php';

class DatosUbicacion {

    public static function obtener() {
        $db = BD::obtenerConexion();

        $stmt = $db->query("SELECT * FROM datos_ubicacion LIMIT 1");
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function actualizar($direccion, $telefono, $whatsapp, $mapaEmbed) {
        $db = BD::obtenerConexion();

        $sql = "UPDATE datos_ubicacion 
                SET direccion = ?, telefono = ?, whatsapp = ?, mapa_embed = ?
                WHERE id = 1";

        $stmt = $db->prepare($sql);
        return $stmt->execute([$direccion, $telefono, $whatsapp, $mapaEmbed]);
    }
}