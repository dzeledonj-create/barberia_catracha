<?php

require_once 'Usuario.php'; 

echo "<h1>método crear()</h1>";


$nuevoUsuario = new Usuario($nombre, $email, $rol);

$nuevoUsuario->crear();
*/
$usuario1 = Usuario::obtenerPorId(7);

var_dump($usuario1);


echo "<p>Se ha ejecutado la función crear para el usuario: <b>$nombre</b></p>";
echo "<p>Revisa tu base de datos (phpMyAdmin) para ver si se ha insertado correctamente.</p>";
?>