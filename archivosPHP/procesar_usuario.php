<?php
// Mostrar errores en desarrollo
error_reporting(E_ALL);
ini_set('display_errors', '1');

session_start();
// Validar admin (Seguridad extra)
if (!isset($_SESSION['rol_id']) || $_SESSION['rol_id'] != 1) {
    header("Location: ../index.php"); 
    exit;
}

require_once __DIR__ . '/conexion.php';

// Si no hay acción, vuelve a la lista
if (!isset($_GET['accion'])) {
    header("Location: usuarios.php");
    exit;
}

$accion = $_GET['accion'];

try {
    switch ($accion) {

        // ==================
        // AGREGAR (CREATE)
        // ==================
        case 'agregar':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $nombre    = $_POST['nombre']    ?? '';
                $ap        = $_POST['ap']        ?? '';
                $am        = $_POST['am']        ?? '';
                $telefono  = $_POST['telefono']  ?? '';
                $email     = $_POST['email']     ?? '';
                $pass      = $_POST['pass']      ?? '';
                $direccion = $_POST['direccion'] ?? '';
                $rol       = (int)($_POST['rol'] ?? 0);

                $pass_hashed = md5($pass);

                $sql = "INSERT INTO tblusuario 
                        (vchNombres, vchApaterno, vchAmaterno, vchTelefono, vchCorreo, vchPassword, vchDireccion, intIdRol)
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("sssssssi", $nombre, $ap, $am, $telefono, $email, $pass_hashed, $direccion, $rol);
                
                if($stmt->execute()) {
                    header("Location: usuarios.php?mensaje=agregado");
                } else {
                    header("Location: usuarios.php?mensaje=error");
                }
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
                $pass      = $_POST['pass']      ?? ''; 
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
                
                if($stmt->execute()) {
                    header("Location: usuarios.php?mensaje=actualizado");
                } else {
                    header("Location: usuarios.php?mensaje=error");
                }
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
                
                if($stmt->execute()) {
                    header("Location: usuarios.php?mensaje=eliminado");
                } else {
                    header("Location: usuarios.php?mensaje=error");
                }
                $stmt->close();
            }
            break;

        default:
            header("Location: usuarios.php");
            break;
    }

} catch (Exception $e) {
    header("Location: usuarios.php?mensaje=error");
}

if (isset($conn) && $conn instanceof mysqli) {
    $conn->close();
}
exit;
?>