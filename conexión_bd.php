<?php
$host = 'localhost'; // Cambia si es necesario
$usuario = 'root'; // Tu usuario de MySQL
$contrasena = ''; // Tu contraseña de MySQL
$base_datos = 'registro_bd'; // Nombre de la BD
$conexion = mysqli_connect($host, $usuario, $contrasena, $base_datos);
if (!$conexion) {
    die("Error de conexión: " . mysqli_connect_error());
}
?>
