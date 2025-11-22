<?php
require_once "conexion.php";

if (!isset($_GET['accion'])) {
    header("Location: menu.php");
    exit;
}

$accion = $_GET['accion'];

switch ($accion) {

    // ====== AGREGAR NUEVO PLATILLO ======
    case 'agregar':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $categoria = trim($_POST['categoria'] ?? '');
            $nombre    = trim($_POST['nombre'] ?? '');
            $precio    = (float)($_POST['precio'] ?? 0);
            $imagen    = trim($_POST['imagen'] ?? '');

            if ($categoria === '' || $nombre === '' || $precio <= 0) {
                die("Datos inválidos al agregar platillo.");
            }

            $sql = "INSERT INTO tblmenu (vchCategoria, vchNombre, decPrecio, vchImagen)
                    VALUES (?, ?, ?, ?)";

            $stmt = $conn->prepare($sql);
            if (!$stmt) {
                die("Error en prepare: " . $conn->error);
            }

            $stmt->bind_param("ssds", $categoria, $nombre, $precio, $imagen);
            if (!$stmt->execute()) {
                die("Error al insertar: " . $stmt->error);
            }

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
            $imagen    = trim($_POST['imagen'] ?? '');

            if ($id <= 0 || $categoria === '' || $nombre === '' || $precio <= 0) {
                die("Datos inválidos al actualizar platillo.");
            }

            $sql = "UPDATE tblmenu SET
                        vchCategoria = ?,
                        vchNombre    = ?,
                        decPrecio    = ?,
                        vchImagen    = ?
                    WHERE intIdPlatillo = ?";

            $stmt = $conn->prepare($sql);
            if (!$stmt) {
                die("Error en prepare: " . $conn->error);
            }

            $stmt->bind_param("ssdsi", $categoria, $nombre, $precio, $imagen, $id);
            if (!$stmt->execute()) {
                die("Error al actualizar: " . $stmt->error);
            }

            $stmt->close();
        }
        break;

    // ====== ELIMINAR ======
    case 'eliminar':
        if (isset($_GET['id'])) {
            $id = (int)$_GET['id'];
            if ($id <= 0) {
                die("ID inválido para eliminar.");
            }

            $stmt = $conn->prepare("DELETE FROM tblmenu WHERE intIdPlatillo = ?");
            if (!$stmt) {
                die("Error en prepare: " . $conn->error);
            }

            $stmt->bind_param("i", $id);
            if (!$stmt->execute()) {
                die("Error al eliminar: " . $stmt->error);
            }

            $stmt->close();
        }
        break;

    // ====== CANCELAR ======
    case 'cancelar':
        // No modificamos nada, solo regresamos al menú
        break;
}

$conn->close();
header("Location: menu.php");
exit;
?>
