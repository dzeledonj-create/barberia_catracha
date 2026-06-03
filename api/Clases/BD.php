<?php

class BD {
    private static $conexion = null;

    private function __construct() {}

    public static function obtenerConexion(): PDO {
        if (self::$conexion === null) {

            // ============================================================
            // CAMBIA ESTE VALOR: 'local' para tu PC, 'supabase' para producción
            $entorno = 'supabase';
            // ============================================================

            if ($entorno === 'supabase') {
                // Datos de Supabase (visibles en tu captura)
                $host       = 'aws-1-eu-west-2.pooler.supabase.com';
                $puerto     = '5432';
                $bd         = 'postgres';
                $usuario    = 'postgres.olpdmrghuorzgswjityz';
                $contrasena = 'ProyectoBarberua'; // <-- Pon aquí tu contraseña
                $sslmode    = 'require';
            } else {
                // Datos locales (tu PC)
                $host       = '192.168.4.24';
                $puerto     = '5432';
                $bd         = 'barberia_catracha';
                $usuario    = 'postgres';
                $contrasena = 'Jinotega2014';
                $sslmode    = 'disable';
            }

            $dsn = "pgsql:host=$host;port=$puerto;dbname=$bd;sslmode=$sslmode";

            try {
                self::$conexion = new PDO($dsn, $usuario, $contrasena);
                self::$conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $e) {
                die("Error de conexión a la base de datos.");
            }
        }

        return self::$conexion;
    }
}