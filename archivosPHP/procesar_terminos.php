<?php
session_start();
require_once __DIR__ . '/conexion.php';

// Validar que se reciba una acción
if (!isset($_GET['accion'])) {
    header("Location: gestion_terminos.php");
    exit;
}

$accion = $_GET['accion'];

try {
    switch ($accion) {
        
        // --- AGREGAR ---
        case 'agregar':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $titulo = $_POST['titulo'];
                $descripcion = $_POST['descripcion'];

                $stmt = $conn->prepare("INSERT INTO tblterminos (vchTitulo, txtDescripcion) VALUES (?, ?)");
                $stmt->bind_param("ss", $titulo, $descripcion);

                if ($stmt->execute()) {
                    header("Location: gestion_terminos.php?mensaje=agregado");
                } else {
                    header("Location: gestion_terminos.php?mensaje=error");
                }
                $stmt->close();
            }
            break;

        // --- ACTUALIZAR ---
        case 'actualizar':
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['id'])) {
                $id = (int)$_GET['id'];
                $titulo = $_POST['titulo'];
                $descripcion = $_POST['descripcion'];

                $stmt = $conn->prepare("UPDATE tblterminos SET vchTitulo = ?, txtDescripcion = ? WHERE intIdTermino = ?");
                $stmt->bind_param("ssi", $titulo, $descripcion, $id);

                if ($stmt->execute()) {
                    header("Location: gestion_terminos.php?mensaje=actualizado");
                } else {
                    header("Location: gestion_terminos.php?mensaje=error");
                }
                $stmt->close();
            }
            break;

        // --- ELIMINAR ---
        case 'eliminar':
            if (isset($_GET['id'])) {
                $id = (int)$_GET['id'];

                $stmt = $conn->prepare("DELETE FROM tblterminos WHERE intIdTermino = ?");
                $stmt->bind_param("i", $id);

                if ($stmt->execute()) {
                    header("Location: gestion_terminos.php?mensaje=eliminado");
                } else {
                    header("Location: gestion_terminos.php?mensaje=error");
                }
                $stmt->close();
            }
            break;

        default:
            header("Location: gestion_terminos.php");
            break;
    }

} catch (Exception $e) {
    header("Location: gestion_terminos.php?mensaje=error");
}

$conn->close();
exit;
?>