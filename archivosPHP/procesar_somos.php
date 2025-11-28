<?php
session_start();
require_once 'conexion.php';

// Seguridad
if (!isset($_SESSION['rol_id']) || $_SESSION['rol_id'] != 1) { 
    header("Location: ../index.php"); 
    exit; 
}

$accion = $_GET['accion'] ?? '';

// --- FUNCIÓN SUBIR IMAGEN ---
function subirImagenSomos($archivo) {
    $dir = "../imagenes_somos/";
    if (!file_exists($dir)) mkdir($dir, 0777, true);
    
    $ext = pathinfo($archivo["name"], PATHINFO_EXTENSION);
    $nombre = "somos_" . uniqid() . "." . $ext;
    $destino = $dir . $nombre;
    
    if (move_uploaded_file($archivo["tmp_name"], $destino)) {
        return "imagenes_somos/" . $nombre;
    }
    return "";
}

// --- 1. AGREGAR ---
if ($accion == 'agregar' && $_SERVER['REQUEST_METHOD'] == 'POST') {
    $desc = $_POST['descripcion'];
    $ruta_img = "";
    
    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] == 0) {
        $ruta_img = subirImagenSomos($_FILES['imagen']);
    }
    
    $stmt = $conn->prepare("INSERT INTO tblsomos (vchImagen, txtDescripcion) VALUES (?, ?)");
    $stmt->bind_param("ss", $ruta_img, $desc);
    
    if ($stmt->execute()) {
        header("Location: gestion_somos.php?mensaje=agregado");
        exit;
    } else {
        header("Location: gestion_somos.php?mensaje=error");
        exit;
    }
}

// --- 2. EDITAR ---
if ($accion == 'editar' && $_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = (int)$_POST['id_registro']; // Recibimos el ID oculto
    $desc = $_POST['descripcion'];

    // Actualizamos el texto primero
    $stmt = $conn->prepare("UPDATE tblsomos SET txtDescripcion = ? WHERE intIdSomos = ?");
    $stmt->bind_param("si", $desc, $id);
    
    if ($stmt->execute()) {
        // Verificamos si subieron una NUEVA imagen
        if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] == 0) {
            $nueva_ruta = subirImagenSomos($_FILES['imagen']);
            
            if ($nueva_ruta != "") {
                // Actualizamos la ruta en la base de datos
                $stmtImg = $conn->prepare("UPDATE tblsomos SET vchImagen = ? WHERE intIdSomos = ?");
                $stmtImg->bind_param("si", $nueva_ruta, $id);
                $stmtImg->execute();
            }
        }
        header("Location: gestion_somos.php?mensaje=actualizado");
        exit;
    } else {
        header("Location: gestion_somos.php?mensaje=error");
        exit;
    }
}

// --- 3. ELIMINAR ---
if ($accion == 'eliminar' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    
    
    
    $stmt = $conn->prepare("DELETE FROM tblsomos WHERE intIdSomos = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        header("Location: gestion_somos.php?mensaje=eliminado");
        exit;
    } else {
        header("Location: gestion_somos.php?mensaje=error");
        exit;
    }
}

// Si no entró en ninguna acción válida
header("Location: gestion_somos.php");
exit;
?>