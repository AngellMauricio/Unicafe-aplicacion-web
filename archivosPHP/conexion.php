<?php
/*$host = "localhost";
$usuario = "root";
$psw = "";
$bd = "unicafe";*/



Datos de conexión (Actualizados)
$host = '127.0.0.1:3306';
$bd = 'u138650717_bdpelis';
$usuario = 'u138650717_victor';
$psw = '1PMEqU/hed[0wpT/';

// ...
$conn = new mysqli($host, $usuario, $psw, $bd);

// Verificar conexión
if ($conn->connect_error) {
    // Es mejor usar 'die' o lanzar una excepción aquí para detener la ejecución si falla la BD.
    die("Error en la conexión: " . $conn->connect_error);

} 
?>