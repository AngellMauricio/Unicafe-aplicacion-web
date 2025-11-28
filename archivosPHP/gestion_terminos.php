<?php
session_start();
require_once __DIR__ . '/conexion.php';

// --- LÓGICA PHP ---
$modo_edicion = false;
$id_editar = 0;
$titulo_val = "";
$desc_val = "";
$accion_form = "procesar_terminos.php?accion=agregar";

if (isset($_GET['accion']) && $_GET['accion'] == 'editar' && isset($_GET['id'])) {
    $modo_edicion = true;
    $id_editar = (int)$_GET['id'];
    $accion_form = "procesar_terminos.php?accion=actualizar&id=" . $id_editar;

    $stmt = $conn->prepare("SELECT * FROM tblterminos WHERE intIdTermino = ?");
    $stmt->bind_param("i", $id_editar);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($row = $res->fetch_assoc()) {
        $titulo_val = htmlspecialchars($row['vchTitulo']);
        $desc_val = htmlspecialchars($row['txtDescripcion']);
    }
}

// Mensaje de alerta
$mensaje = "";
$clase_alerta = "";
if (isset($_GET['mensaje'])) {
    switch ($_GET['mensaje']) {
        case 'agregado': $mensaje = "✅ ¡Término agregado!"; $clase_alerta = "success"; break;
        case 'actualizado': $mensaje = "✅ ¡Término actualizado!"; $clase_alerta = "success"; break;
        case 'eliminado': $mensaje = "🗑️ ¡Término eliminado!"; $clase_alerta = "success"; break;
        case 'error': $mensaje = "❌ Error en el proceso"; $clase_alerta = "error"; break;
    }
}

$sql_lista = "SELECT * FROM tblterminos";
$res_lista = $conn->query($sql_lista);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8" />
    <title>Gestionar Términos — Cafetería UTHH</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    
    <link rel="stylesheet" href="../archivosCSS/layout.css?v=<?php echo time(); ?>">
     <link rel="stylesheet" href="../archivosCSS/gestion_terminos.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

   
</head>

<body>
    <div class="app">
        <?php include '../archivosPHP/header.php'; ?>
        <?php include '../archivosPHP/barra_navegacion.php'; ?>
        
        <main class="content">
            
            <?php if (!empty($mensaje)): ?>
                <div class="alert <?php echo $clase_alerta; ?>">
                    <?php echo $mensaje; ?>
                </div>
            <?php endif; ?>

            <div class="admin-card">
                <h2><i class="fa-solid fa-pen-to-square"></i> <?php echo $modo_edicion ? 'Editar Término' : 'Agregar Nuevo Término'; ?></h2>

                <form action="<?php echo $accion_form; ?>" method="post">
                    
                    <div class="form-group">
                        <label>Título</label>
                        <input type="text" name="titulo" value="<?php echo $titulo_val; ?>" required placeholder="Ej: Uso permitido">
                    </div>
                    
                    <div class="form-group">
                        <label>Descripción</label>
                        <textarea name="descripcion" rows="5" required placeholder="Escribe el contenido..."><?php echo $desc_val; ?></textarea>
                    </div>

                    <div class="btn-toolbar">
                        <button class="btn-primary" type="submit">
                            <?php echo $modo_edicion ? 'Guardar Cambios' : 'Agregar'; ?>
                        </button>

                        <a href="terminos.php" target="_blank" class="btn-secondary">
                            <i class="fa-solid fa-eye"></i> Ver como Usuario
                        </a>

                        <?php if ($modo_edicion): ?>
                            <a href="gestion_terminos.php" class="btn-cancel">Cancelar</a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>

            <div class="admin-card">
                <h2>Listado de Términos</h2>
                <div style="overflow-x: auto;">
                    <table>
                        <thead>
                            <tr>
                                <th style="width: 25%;">Título</th>
                                <th style="width: 55%;">Descripción</th>
                                <th style="width: 20%;">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $res_lista->fetch_assoc()): ?>
                                <tr>
                                    <td><strong><?php echo htmlspecialchars($row['vchTitulo']); ?></strong></td>
                                    <td><?php echo nl2br(htmlspecialchars($row['txtDescripcion'])); ?></td>
                                    <td class="actions-cell">
                                        <a href="gestion_terminos.php?accion=editar&id=<?php echo $row['intIdTermino']; ?>" class="text-edit">Editar</a>
                                        <a href="procesar_terminos.php?accion=eliminar&id=<?php echo $row['intIdTermino']; ?>" 
                                           class="text-del" 
                                           onclick="return confirm('¿Borrar este registro?');">Eliminar</a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
<?php 
if (isset($stmt)) $stmt->close();
$conn->close(); 
?>