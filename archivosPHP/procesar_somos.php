<?php
session_start();
require_once 'conexion.php';

// Seguridad
if (!isset($_SESSION['rol_id']) || $_SESSION['rol_id'] != 1) { header("Location: ../index.php"); exit; }

$accion = $_GET['accion'] ?? '';

// --- FUNCIÓN SUBIR IMAGEN ---
function subirImagenSomos($archivo) {
    $dir = "../imagenes_somos/";
    if (!file_exists($dir)) mkdir($dir, 0777, true); // Crear carpeta si no existe
    
    $ext = pathinfo($archivo["name"], PATHINFO_EXTENSION);
    $nombre = "somos_" . uniqid() . "." . $ext;
    $destino = $dir . $nombre;
    
    if (move_uploaded_file($archivo["tmp_name"], $destino)) {
        return "imagenes_somos/" . $nombre;
    }
    return "";
}

// --- AGREGAR ---
if ($accion == 'agregar' && $_SERVER['REQUEST_METHOD'] == 'POST') {
    $desc = $_POST['descripcion'];
    $ruta_img = "";
    
    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] == 0) {
        $ruta_img = subirImagenSomos($_FILES['imagen']);
    }
    
    $stmt = $conn->prepare("INSERT INTO tblsomos (vchImagen, txtDescripcion) VALUES (?, ?)");
    $stmt->bind_param("ss", $ruta_img, $desc);
    $stmt->execute();
}

// --- ELIMINAR ---
if ($accion == 'eliminar' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    
    // Borrar archivo físico
    $res = $conn->query("SELECT vchImagen FROM tblsomos WHERE intIdSomos=$id");
    if ($r = $res->fetch_assoc()) {
        if (!empty($r['vchImagen']) && file_exists("../" . $r['vchImagen'])) {
            unlink("../" . $r['vchImagen']);
        }
    }
    
    $conn->query("DELETE FROM tblsomos WHERE intIdSomos=$id");
}

header("Location: gestion_somos.php");
exit;
?>