<?php

require_once 'BD.php';

class Horario {

    public static function obtenerTodos() {

        $db = BD::obtenerConexion();

        $sql = "
            SELECT * FROM horarios
            ORDER BY CASE dia_semana
                WHEN 'Lunes' THEN 1
                WHEN 'Martes' THEN 2
                WHEN 'Miércoles' THEN 3
                WHEN 'Jueves' THEN 4
                WHEN 'Viernes' THEN 5
                WHEN 'Sábado' THEN 6
                WHEN 'Domingo' THEN 7
            END
        ";

        $stmt = $db->query($sql);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function actualizar(
        $horarioId,
        $horaApertura,
        $horaCierre,
        $cerrado
    ) {

        $db = BD::obtenerConexion();

        $sql = "
            UPDATE horarios
            SET hora_apertura = ?,
                hora_cierre = ?,
                cerrado = ?
            WHERE horario_id = ?
        ";

        $stmt = $db->prepare($sql);

        return $stmt->execute([
            $horaApertura,
            $horaCierre,
            $cerrado,
            $horarioId
        ]);
    }
}