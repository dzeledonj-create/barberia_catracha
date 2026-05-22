<?php

class BD {
    // Guarda la única instancia de la conexión. Null hasta que se use por primera vez.
    private static $conexion = null;

    // El constructor privado impide hacer "new BD()" desde fuera — solo existe una conexión.
    private function __construct() {}

    public static function obtenerConexion(): PDO {
        // Solo crea la conexión si aún no existe (la primera vez que se llama)
        if (self::$conexion === null) {
            $host     = '192.168.4.24'; //aws-0-eu-west-1.pooler.supabase.com
            $puerto   = '5432';  //5432
            $bd       = 'barberia_catracha'; //postgres
            $usuario  = 'postgres'; //postgres.qefjlwgqavmumgahjipk
            $contrasena = 'Jinotega2014'; //C2mpusdigital10.

            $dsn = "pgsql:host=$host;port=$puerto;dbname=$bd";

            try {
                self::$conexion = new PDO($dsn, $usuario, $contrasena);
                self::$conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $e) {
                die("Error de conexión: " . $e->getMessage());
            }
        }
        // Las siguientes llamadas simplemente devuelven la conexión ya creada
        return self::$conexion;
    }
}