<?php
require_once "conexion.php";

if (!isset($_GET['accion'])) {
    header("Location: menu.php");
    exit;
}

$accion = $_GET['accion'];

// Función para subir imagen
function subirImagenMenu($archivo) {
    // Carpeta física donde se guardará (necesitamos ../ para salir de archivosPHP)
    $directorio_fisico = "../imagenes_menu/";
    
    // Crear carpeta si no existe
    if (!file_exists($directorio_fisico)) {
        mkdir($directorio_fisico, 0777, true);
    }

    $nombre_archivo = basename($archivo["name"]);
    $tipo_archivo = strtolower(pathinfo($nombre_archivo, PATHINFO_EXTENSION));
    
    // Validar imagen
    $check = getimagesize($archivo["tmp_name"]);
    if($check === false) {
        return null; // No es imagen
    }

    // Nombre único
    $nuevo_nombre = "platillo_" . uniqid() . "." . $tipo_archivo;
    $ruta_destino_fisica = $directorio_fisico . $nuevo_nombre;

    if (move_uploaded_file($archivo["tmp_name"], $ruta_destino_fisica)) {
        // IMPORTANTE: Guardamos en la BD la ruta "limpia" para poder usarla desde cualquier lado
        // Guardamos: imagenes_menu/platillo_123.jpg
        return "imagenes_menu/" . $nuevo_nombre;
    }
    return null;
}

switch ($accion) {

    // ====== AGREGAR ======
    case 'agregar':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $categoria = trim($_POST['categoria'] ?? '');
            $nombre    = trim($_POST['nombre'] ?? '');
            $precio    = (float)($_POST['precio'] ?? 0);
            
            // Por defecto vacío
            $ruta_imagen = ""; 
            
            if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] == 0) {
                $subido = subirImagenMenu($_FILES['imagen']);
                if ($subido) {
                    $ruta_imagen = $subido;
                }
            }

            if ($categoria === '' || $nombre === '' || $precio <= 0) {
                die("Datos inválidos.");
            }

            $sql = "INSERT INTO tblmenu (vchCategoria, vchNombre, decPrecio, vchImagen)
                    VALUES (?, ?, ?, ?)";

            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssds", $categoria, $nombre, $precio, $ruta_imagen);
            
            if (!$stmt->execute()) { die("Error: " . $stmt->error); }
            $stmt->close();
        }
        break;

    // ====== ACTUALIZAR ======
    case 'actualizar':
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['id'])) {
            $id        = (int)$_GET['id'];
            $categoria = trim($_POST['categoria'] ?? '');
            $nombre    = trim($_POST['nombre'] ?? '');
            $precio    = (float)($_POST['precio'] ?? 0);
            
            // Recuperamos la imagen anterior (que ya debe ser imagenes_menu/foto.jpg)
            $ruta_imagen = $_POST['imagen_actual'] ?? "";

            // Si suben nueva imagen, reemplazamos
            if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] == 0) {
                $nueva = subirImagenMenu($_FILES['imagen']);
                if($nueva) {
                    $ruta_imagen = $nueva;
                }
            }

            if ($id <= 0 || $categoria === '' || $nombre === '' || $precio <= 0) {
                die("Datos inválidos.");
            }

            $sql = "UPDATE tblmenu SET
                        vchCategoria = ?,
                        vchNombre    = ?,
                        decPrecio    = ?,
                        vchImagen    = ?
                    WHERE intIdPlatillo = ?";

            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssdsi", $categoria, $nombre, $precio, $ruta_imagen, $id);
            
            if (!$stmt->execute()) { die("Error: " . $stmt->error); }
            $stmt->close();
        }
        break;

    // ====== ELIMINAR ======
    case 'eliminar':
        if (isset($_GET['id'])) {
            $id = (int)$_GET['id'];
            
            // Borrar archivo físico
            $res = $conn->query("SELECT vchImagen FROM tblmenu WHERE intIdPlatillo=$id");
            if($row = $res->fetch_assoc()){
                // La ruta en BD es imagenes_menu/foto.jpg
                // Para borrarla desde aquí, necesitamos agregarle ../ al principio
                $archivo_fisico = "../" . $row['vchImagen'];
                
                if(!empty($row['vchImagen']) && file_exists($archivo_fisico)){
                    unlink($archivo_fisico);
                }
            }

            $stmt = $conn->prepare("DELETE FROM tblmenu WHERE intIdPlatillo = ?");
            $stmt->bind_param("i", $id);
            if (!$stmt->execute()) { die("Error: " . $stmt->error); }
            $stmt->close();
        }
        break;
}

$conn->close();
header("Location: menu.php");
exit;
?>