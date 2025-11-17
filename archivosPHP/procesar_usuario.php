<?php
// Mostrar errores en desarrollo (puedes quitarlo en producción)
error_reporting(E_ALL);
ini_set('display_errors', '1');

// Incluir la conexión (MISMA CARPETA que este archivo)
require_once __DIR__ . '/conexion.php';

// Si no hay acción, vuelve a la lista
if (!isset($_GET['accion'])) {
    header("Location: usuarios.php");
    exit;
}

$accion = $_GET['accion'];

switch ($accion) {

    // ==================
    // AGREGAR (CREATE)
    // ==================
    case 'agregar':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nombre    = $_POST['nombre']   ?? '';
            $ap        = $_POST['ap']       ?? '';
            $am        = $_POST['am']       ?? '';
            $telefono  = $_POST['telefono'] ?? '';
            $email     = $_POST['email']    ?? '';
            $pass      = $_POST['pass']     ?? '';
            $direccion = $_POST['direccion']?? '';
            $rol       = (int)($_POST['rol'] ?? 0);

            // Hashear contraseña
            $pass_hashed = password_hash($pass, PASSWORD_DEFAULT);

            $sql = "INSERT INTO tblusuario 
                    (vchNombres, vchApaterno, vchAmaterno, vchTelefono, vchCorreo, vchPassword, vchDireccion, intIdRol)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssssssi", $nombre, $ap, $am, $telefono, $email, $pass_hashed, $direccion, $rol);
            $stmt->execute();
            $stmt->close();
        }
        break;

    // ==================
    // ACTUALIZAR (UPDATE)
    // ==================
    case 'actualizar':
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['id'])) {
            $id        = (int)$_GET['id'];
            $nombre    = $_POST['nombre']    ?? '';
            $ap        = $_POST['ap']        ?? '';
            $am        = $_POST['am']        ?? '';
            $telefono  = $_POST['telefono']  ?? '';
            $email     = $_POST['email']     ?? '';
            $pass      = $_POST['pass']      ?? ''; // puede venir vacío
            $direccion = $_POST['direccion'] ?? '';
            $rol       = (int)($_POST['rol'] ?? 0);

            if (!empty($pass)) {
                // Cambiando contraseña
                $pass_hashed = password_hash($pass, PASSWORD_DEFAULT);
                $sql = "UPDATE tblusuario SET 
                        vchNombres = ?, vchApaterno = ?, vchAmaterno = ?, 
                        vchTelefono = ?, vchCorreo = ?, vchPassword = ?, 
                        vchDireccion = ?, intIdRol = ?
                        WHERE intIdUsuario = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("sssssssii", $nombre, $ap, $am, $telefono, $email, $pass_hashed, $direccion, $rol, $id);
            } else {
                // Sin cambiar contraseña
                $sql = "UPDATE tblusuario SET 
                        vchNombres = ?, vchApaterno = ?, vchAmaterno = ?, 
                        vchTelefono = ?, vchCorreo = ?, vchDireccion = ?, 
                        intIdRol = ?
                        WHERE intIdUsuario = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ssssssii", $nombre, $ap, $am, $telefono, $email, $direccion, $rol, $id);
            }
            $stmt->execute();
            $stmt->close();
        }
        break;

    // ==================
    // ELIMINAR (DELETE)
    // ==================
    case 'eliminar':
        if (isset($_GET['id'])) {
            $id = (int)$_GET['id'];
            $sql = "DELETE FROM tblusuario WHERE intIdUsuario = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $stmt->close();
        }
        break;

    default:
        // acción desconocida
        break;
}

// Cerrar conexión y volver a la lista
if (isset($conn) && $conn instanceof mysqli) {
    $conn->close();
}
header("Location: usuarios.php");
exit;
