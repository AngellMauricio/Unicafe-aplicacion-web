<?php
include 'conexion.php';

if (!isset($_GET['accion'])) {
    header("Location: gestion_productos.php");
    exit;
}

$accion = $_GET['accion'];

// FUNCIÓN AUXILIAR PARA SUBIR IMAGEN
function subirImagen($archivo) {
    // Carpeta destino (relativa a este archivo PHP)
    // Como estamos en 'archivosPHP', salimos uno y entramos a 'imagenes_productos'
    $directorio = "../imagenes_productos/";
    
    // Crear carpeta si no existe (por seguridad)
    if (!file_exists($directorio)) {
        mkdir($directorio, 0777, true);
    }

    // Nombre original y extensión
    $nombre_archivo = basename($archivo["name"]);
    $tipo_archivo = strtolower(pathinfo($nombre_archivo, PATHINFO_EXTENSION));
    
    // Validar que sea imagen
    $check = getimagesize($archivo["tmp_name"]);
    if($check === false) {
        return null; // No es imagen
    }

    // Generar nombre único para evitar sobrescribir (ej: producto_65a1b2c3.jpg)
    $nuevo_nombre = "producto_" . uniqid() . "." . $tipo_archivo;
    $ruta_destino = $directorio . $nuevo_nombre;

    if (move_uploaded_file($archivo["tmp_name"], $ruta_destino)) {
        // Devolvemos la ruta relativa limpia para guardar en BD (ej: imagenes_productos/foto.jpg)
        return "imagenes_productos/" . $nuevo_nombre;
    }
    
    return null; // Falló la subida
}

switch ($accion) {
    // --- AGREGAR ---
    case 'agregar':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nombre = $_POST['nombre'];
            $desc   = $_POST['descripcion'];
            $stock  = (int)$_POST['stock'];
            $cat    = (int)$_POST['categoria'];
            $prov   = $_POST['proveedor'];
            $pcompra = (float)$_POST['precio_compra'];
            $pventa  = (float)$_POST['precio_venta'];
            
            // Manejo de imagen
            $ruta_imagen = null;
            if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] == 0) {
                $ruta_imagen = subirImagen($_FILES['imagen']);
            }

            $sql = "INSERT INTO tblproductos 
                    (vchNombre, vchDescripcion, intStock, intIdCategoria, vchRFCProveedor, decPrecioCompra, decPrecioVenta, vchImagen) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssiisdds", $nombre, $desc, $stock, $cat, $prov, $pcompra, $pventa, $ruta_imagen);
            
            if(!$stmt->execute()) { echo "Error: " . $stmt->error; }
            $stmt->close();
        }
        break;

    // --- ACTUALIZAR ---
    case 'actualizar':
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['id'])) {
            $id = (int)$_GET['id'];
            $nombre = $_POST['nombre'];
            $desc   = $_POST['descripcion'];
            $stock  = (int)$_POST['stock'];
            $cat    = (int)$_POST['categoria'];
            $prov   = $_POST['proveedor'];
            $pcompra = (float)$_POST['precio_compra'];
            $pventa  = (float)$_POST['precio_venta'];
            
            // Recuperar imagen anterior del input hidden
            $ruta_imagen = $_POST['imagen_actual'];

            // Si se subió una NUEVA imagen, la procesamos
            if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] == 0) {
                $nueva_ruta = subirImagen($_FILES['imagen']);
                if ($nueva_ruta) {
                    $ruta_imagen = $nueva_ruta;
                    // Opcional: Aquí podrías borrar la imagen vieja del servidor si quisieras
                }
            }

            $sql = "UPDATE tblproductos SET 
                    vchNombre=?, vchDescripcion=?, intStock=?, intIdCategoria=?, 
                    vchRFCProveedor=?, decPrecioCompra=?, decPrecioVenta=?, vchImagen=? 
                    WHERE intIdProducto=?";
            
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssiisddsi", $nombre, $desc, $stock, $cat, $prov, $pcompra, $pventa, $ruta_imagen, $id);
            
            if(!$stmt->execute()) { echo "Error: " . $stmt->error; }
            $stmt->close();
        }
        break;

    // --- ELIMINAR ---
    case 'eliminar':
        if (isset($_GET['id'])) {
            $id = (int)$_GET['id'];
            
            // Opcional: Primero obtener la ruta de la imagen para borrar el archivo del servidor
            /*
            $res = $conn->query("SELECT vchImagen FROM tblproductos WHERE intIdProducto=$id");
            if ($fila = $res->fetch_assoc()) {
                if (file_exists("../" . $fila['vchImagen'])) { unlink("../" . $fila['vchImagen']); }
            }
            */

            $stmt = $conn->prepare("DELETE FROM tblproductos WHERE intIdProducto = ?");
            $stmt->bind_param("i", $id);
            if(!$stmt->execute()) { echo "Error: " . $stmt->error; }
            $stmt->close();
        }
        break;
}

$conn->close();
header("Location: gestion_productos.php");
exit;
?>