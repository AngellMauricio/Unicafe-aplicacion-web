<?php
session_start();
require_once 'conexion.php';

// Seguridad: Solo admin puede ver esto
if (!isset($_SESSION['rol_id']) || $_SESSION['rol_id'] != 1) {
    header("Location: ../index.php"); 
    exit;
}

// --- 1. LÓGICA DE MENSAJES ---
$mensaje = "";
$clase_alerta = "";

if (isset($_GET['mensaje'])) {
    switch ($_GET['mensaje']) {
        case 'agregado':
            $mensaje = "✅ ¡Sección agregada exitosamente!";
            $clase_alerta = "success";
            break;
        case 'actualizado':
            $mensaje = "✅ ¡Sección editada correctamente!";
            $clase_alerta = "success";
            break;
        case 'eliminado':
            $mensaje = "🗑️ ¡Sección eliminada exitosamente!";
            $clase_alerta = "success";
            break;
        case 'error':
            $mensaje = "❌ Error al procesar la solicitud.";
            $clase_alerta = "error";
            break;
    }
}

// --- 2. LÓGICA DE EDICIÓN (DETECTAR SI VAMOS A EDITAR) ---
$editando = false;
$id_editar = 0;
$desc_actual = "";
$img_actual = "";

if (isset($_GET['editar'])) {
    $id_editar = (int)$_GET['editar'];
    $sql_edit = "SELECT * FROM tblsomos WHERE intIdSomos = $id_editar";
    $res_edit = $conn->query($sql_edit);
    if ($fila = $res_edit->fetch_assoc()) {
        $editando = true;
        $desc_actual = $fila['txtDescripcion'];
        $img_actual = $fila['vchImagen'];
    }
}

// Consultar lista completa para la tabla
$res = $conn->query("SELECT * FROM tblsomos");
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8" />
  <title>Gestión Somos Unicafe</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  
  <link rel="stylesheet" href="../archivosCSS/layout.css?v=<?php echo time(); ?>">
  <link rel="stylesheet" href="../archivosCSS/gestion_somos.css?v=<?php echo time(); ?>">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

    <?php include 'header.php'; ?>
    <?php include 'barra_navegacion.php'; ?>

    <main class="content">
        
        <?php if (!empty($mensaje)): ?>
            <div class="alert <?php echo $clase_alerta; ?>">
                <?php echo $mensaje; ?>
            </div>
            <script>
                setTimeout(function() {
                    const alert = document.querySelector('.alert');
                    if(alert) alert.style.display = 'none';
                }, 4000);
            </script>
        <?php endif; ?>

        <div class="form-container">
            <h2>
                <?php echo $editando ? 'Editar Sección' : 'Agregar Nueva Sección'; ?>
                <a href="somos.php" target="_blank" class="btn-view-user">
                    <i class="fa-solid fa-eye"></i> Ver como Usuario
                </a>
            </h2>
            
            <form action="procesar_somos.php?accion=<?php echo $editando ? 'editar' : 'agregar'; ?>" method="post" enctype="multipart/form-data">
                
                <?php if ($editando): ?>
                    <input type="hidden" name="id_registro" value="<?php echo $id_editar; ?>">
                <?php endif; ?>

                <div class="form-row">
                    <label>Descripción / Historia</label>
                    <textarea name="descripcion" required placeholder="Escribe aquí el texto..."><?php echo htmlspecialchars($desc_actual); ?></textarea>
                </div>

                <div class="form-row">
                    <label><?php echo $editando ? 'Cambiar Imagen (Opcional)' : 'Imagen de la sección'; ?></label>
                    
                    <?php if($editando && !empty($img_actual)): ?>
                        <div style="margin-bottom:10px; display:flex; align-items:center; gap:10px;">
                            <img src="../<?php echo $img_actual; ?>" width="80" style="border-radius:4px; border:1px solid #ccc;"> 
                            <small style="color:#666;">Imagen actual</small>
                        </div>
                    <?php endif; ?>

                    <input type="file" name="imagen" accept="image/*">
                </div>

                <div class="actions">
                    <button class="btn-action btn-add" type="submit">
                        <?php echo $editando ? 'Guardar Cambios' : 'Agregar Sección'; ?>
                    </button>

                    <?php if ($editando): ?>
                        <a href="gestion_somos.php" class="form-cancel-btn">Cancelar Edición</a>
                    <?php endif; ?>
                </div>
            </form>
        </div>

        <div class="list-container">
            <h2>Secciones Actuales</h2>
            
            <div style="overflow-x: auto;">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th style="width: 15%;">Imagen</th>
                            <th style="width: 65%;">Descripción</th>
                            <th style="width: 20%;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($res && $res->num_rows > 0): ?>
                            <?php while ($row = $res->fetch_assoc()): ?>
                                <tr>
                                    <td>
                                        <?php if(!empty($row['vchImagen'])): ?>
                                            <img src="../<?php echo $row['vchImagen']; ?>" class="thumb-preview">
                                        <?php else: ?>
                                            <span style="font-size:2rem;">📷</span>
                                        <?php endif; ?>
                                    </td>
                                    
                                    <td>
                                        <?php echo nl2br(htmlspecialchars($row['txtDescripcion'])); ?>
                                    </td>
                                    
                                    <td class="action-links">
                                        <a href="gestion_somos.php?editar=<?php echo $row['intIdSomos']; ?>" class="edit-link">
                                            Editar
                                        </a>

                                        <a href="procesar_somos.php?accion=eliminar&id=<?php echo $row['intIdSomos']; ?>" 
                                           class="delete-link" 
                                           onclick="return confirm('¿Seguro que deseas eliminar esta sección?');">
                                            Eliminar
                                        </a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="3" style="text-align:center; padding:20px;">No hay secciones registradas.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </main>
</body>
</html>
<?php 
if(isset($conn)) $conn->close(); 
?>