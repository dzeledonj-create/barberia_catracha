<?php
require_once 'Usuario.php'; 
/*
$nombre = "Nuevo Barbero Test";
$email = "barbero_test@barberia.com";
$rol = "barbero";

$nuevoUsuario = new Usuario($nombre, $email, $rol);

$nuevoUsuario->crear();
*/
$usuario1 = Usuario::obtenerPorId(7);

var_dump($usuario1);

?>