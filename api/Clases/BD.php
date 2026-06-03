<?php

class BD {
    // Guarda la única instancia de la conexión. Null hasta que se use por primera vez.
    private static $conexion = null;

    // El constructor privado impide hacer "new BD()" desde fuera — solo existe una conexión.
    private function __construct() {}

    public static function obtenerConexion(): PDO {
        // Solo crea la conexión si aún no existe (la primera vez que se llama)
        if (self::$conexion === null) {
            
            // Intentamos leer la única clave de conexión desde Vercel
            $dbUrl = getenv('DATABASE_URL') ?: getenv('SUPABASE_DATABASE_URL') ?: getenv('SUPABASE_URL');

            if ($dbUrl) {
                // Si existe en Vercel/Supabase, parseamos la URL de conexión automáticamente
                $dbparts = parse_url($dbUrl);
                $query   = [];
                if (isset($dbparts['query'])) {
                    parse_str($dbparts['query'], $query);
                }

                $host       = $dbparts['host'] ?? '';
                $puerto     = $dbparts['port'] ?? '5432';
                $bd         = isset($dbparts['path']) ? ltrim($dbparts['path'], '/') : '';
                $usuario    = $dbparts['user'] ?? '';
                $contrasena = $dbparts['pass'] ?? '';
                $sslmode    = $query['sslmode'] ?? 'require';
            } else {
                // Si NO existe (entorno local en tu PC), usa tus datos locales por defecto
                $host       = '192.168.4.24';
                $puerto     = '5432';
                $bd         = 'barberia_catracha';
                $usuario    = 'postgres';
                $contrasena = 'Jinotega2014';
                $sslmode    = 'disable';
            }

            // Construcción del DSN para PostgreSQL
            $dsn = "pgsql:host=$host;port=$puerto;dbname=$bd;sslmode=$sslmode";

            try {
                self::$conexion = new PDO($dsn, $usuario, $contrasena);
                self::$conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $e) {
                // Evitamos mostrar el error interno en producción por seguridad
                die("Error de conexión a la base de datos.");
            }
        }
        // Las siguientes llamadas simplemente devuelven la conexión ya creada
        return self::$conexion;
    }
}