<?php
// 1. Incluimos la clase Usuario
// (Asegúrate de que esta ruta sea la correcta para llegar a tu archivo Usuario.php)
require_once 'Usuario.php'; 

echo "<h1>Probando el método crear()</h1>";

// 2. Creamos un objeto con datos fijos para probar
$nombre = "Pedro Gomez";
$email = "pedro@correo.com"; // Ojo: si en tu BD este campo es UNIQUE, cámbialo en cada prueba
$rol = "usuario";

$nuevoUsuario = new Usuario($nombre, $email, $rol);

// 3. Llamamos al método crear()
$nuevoUsuario->crear();

// 4. Mostramos un mensaje básico para saber que el código terminó de ejecutarse
echo "<p>Se ha ejecutado la función crear para el usuario: <b>$nombre</b></p>";
echo "<p>Revisa tu base de datos (phpMyAdmin) para ver si se ha insertado correctamente.</p>";
?>