<?php
session_start();
require_once __DIR__ . '/conexion.php'; // Misma carpeta

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario  = trim($_POST['usuario']  ?? '');
    $password = trim($_POST['password'] ?? '');

    // Buscamos el usuario
    $sql  = "SELECT intIdUsuario, vchNombres, vchCorreo, vchPassword, intIdRol
             FROM tblusuario WHERE vchCorreo = ? LIMIT 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $usuario);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($fila = $resultado->fetch_assoc()) {
        
        // Verificación de contraseña
        if (password_verify($password, $fila['vchPassword'])) {
            // Regenerar ID de sesión por seguridad
            session_regenerate_id(true);
            
            // Guardar variables de sesión
            $_SESSION['usuario']    = $fila['vchNombres'];
            $_SESSION['rol_id']     = $fila['intIdRol'];
            $_SESSION['usuario_id'] = $fila['intIdUsuario']; // Corrección previa aplicada aquí
            
            // --- CORRECCIÓN IMPORTANTE AQUÍ ---
            // Salimos de 'archivosPHP' (../) para ir al index.php en la raíz
            header("Location: ../index.php"); 
            exit;
            
        } else {
            // Contraseña incorrecta
            echo "<script>
                    alert('Contraseña incorrecta'); 
                    window.location='../archivosHTML/login.html';
                  </script>";
            exit;
        }
    } else {
        // Usuario no encontrado
        echo "<script>
                alert('Usuario no encontrado'); 
                window.location='../archivosHTML/login.html';
              </script>";
        exit;
    }
}

if (isset($stmt) && $stmt instanceof mysqli_stmt) { $stmt->close(); }
if (isset($conn) && $conn instanceof mysqli) { $conn->close(); }
?>
