<?php
/*$host = "localhost";
$usuario = "root";
$psw = "";
$bd = "unicafe";*/



//Datos de conexión (Actualizados)
$host = '127.0.0.1:3306';
$bd = 'u138650717_Unicafe';
$usuario = 'u138650717_Unicafe';
$psw = 'kC$Xsa$1';

// ...
$conn = new mysqli($host, $usuario, $psw, $bd);

// Verificar conexión
if ($conn->connect_error) {
    // Es mejor usar 'die' o lanzar una excepción aquí para detener la ejecución si falla la BD.
    die("Error en la conexión: " . $conn->connect_error);

} 
?>
<?php
// conexion.php
$servername = "localhost";
$username = "root";      // Usuario por defecto de XAMPP
$password = "";          // Contraseña por defecto de XAMPP (vacía)
$dbname = "cafeteria_uthh"; // El nombre que le pusimos a tu base de datos

// Crear la conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar si hubo error
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}
// echo "Conectado exitosamente"; // Descomenta esto solo para probar si funciona
?>