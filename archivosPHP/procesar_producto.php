<?php
include 'conexion.php';

if (!isset($_GET['accion'])) {
    header("Location: gestion_productos.php");
    exit;
}

$accion = $_GET['accion'];

switch ($accion) {
    // --- AGREGAR ---
    case 'agregar':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nombre = $_POST['nombre'];
            $desc   = $_POST['descripcion'];
            $stock  = (int)$_POST['stock'];
            $cat    = (int)$_POST['categoria'];
            $prov   = $_POST['proveedor']; // Es varchar (RFC)
            $pcompra = (float)$_POST['precio_compra'];
            $pventa  = (float)$_POST['precio_venta'];

            $sql = "INSERT INTO tblproductos 
                    (vchNombre, vchDescripcion, intStock, intIdCategoria, vchRFCProveedor, decPrecioCompra, decPrecioVenta) 
                    VALUES (?, ?, ?, ?, ?, ?, ?)";
            
            $stmt = $conn->prepare($sql);
            // s=string, i=integer, d=double(decimal)
            $stmt->bind_param("ssiisdd", $nombre, $desc, $stock, $cat, $prov, $pcompra, $pventa);
            
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

            $sql = "UPDATE tblproductos SET 
                    vchNombre=?, vchDescripcion=?, intStock=?, intIdCategoria=?, 
                    vchRFCProveedor=?, decPrecioCompra=?, decPrecioVenta=? 
                    WHERE intIdProducto=?";
            
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssiisddi", $nombre, $desc, $stock, $cat, $prov, $pcompra, $pventa, $id);
            
            if(!$stmt->execute()) { echo "Error: " . $stmt->error; }
            $stmt->close();
        }
        break;

    // --- ELIMINAR ---
    case 'eliminar':
        if (isset($_GET['id'])) {
            $id = (int)$_GET['id'];
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