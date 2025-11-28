<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: login.html");
    exit;
}
if ($_SESSION['rol_id'] != 1) {
    echo "<script>alert('Acceso denegado: Solo administradores.'); window.location.href='../index.php';</script>";
    exit();
}

require_once __DIR__ . '/conexion.php';

// --- 0. LÓGICA DE MENSAJES ---
$mensaje = "";
$clase_alerta = "";

if (isset($_GET['mensaje'])) {
    switch ($_GET['mensaje']) {
        case 'agregado':
            $mensaje = "✅ ¡Producto agregado exitosamente!";
            $clase_alerta = "success";
            break;
        case 'actualizado':
            $mensaje = "✅ ¡Producto modificado exitosamente!";
            $clase_alerta = "success";
            break;
        case 'eliminado':
            $mensaje = "🗑️ ¡Producto eliminado exitosamente!";
            $clase_alerta = "success";
            break;
        case 'error':
            $mensaje = "❌ Ocurrió un error al procesar la solicitud.";
            $clase_alerta = "error";
            break;
    }
}

// --- 1. OBTENER DATOS ---
$sql_categorias = "SELECT intIdCategoria, vchCategoria FROM tblcategorias";
$res_categorias = $conn->query($sql_categorias);

$sql_proveedores = "SELECT vchRFC, vchEmpresa FROM tblproveedores";
$res_proveedores = $conn->query($sql_proveedores);

// --- 2. LÓGICA DE EDICIÓN ---
$modo_edicion = false;
$id_prod_editar = 0;
$nom_val = ""; $desc_val = ""; $stock_val = ""; 
$pcompra_val = ""; $pventa_val = ""; $cat_val = ""; 
$prov_val = ""; $img_val = ""; 

$accion_form = "procesar_producto.php?accion=agregar";

if (isset($_GET['accion']) && $_GET['accion'] == 'editar' && isset($_GET['id'])) {
    $modo_edicion = true;
    $id_prod_editar = (int)$_GET['id'];
    $accion_form = "procesar_producto.php?accion=actualizar&id=" . $id_prod_editar;

    $stmt = $conn->prepare("SELECT * FROM tblproductos WHERE intIdProducto = ?");
    $stmt->bind_param("i", $id_prod_editar);
    $stmt->execute();
    $res_editar = $stmt->get_result();

    if ($fila = $res_editar->fetch_assoc()) {
        $nom_val = htmlspecialchars($fila['vchNombre']);
        $desc_val = htmlspecialchars($fila['vchDescripcion']);
        $stock_val = $fila['intStock'];
        $pcompra_val = $fila['decPrecioCompra'];
        $pventa_val = $fila['decPrecioVenta'];
        $cat_val = $fila['intIdCategoria'];
        $prov_val = $fila['vchRFCProveedor'];
        $img_val = $fila['vchImagen']; 
    }
    $stmt->close();
}

// --- 3. LISTADO ---
$sql_lista = "SELECT P.intIdProducto, P.vchNombre, P.intStock, P.decPrecioVenta, P.vchImagen,
                    C.vchCategoria, PR.vchEmpresa 
            FROM tblproductos P
            JOIN tblcategorias C ON P.intIdCategoria = C.intIdCategoria
            JOIN tblproveedores PR ON P.vchRFCProveedor = PR.vchRFC";
$res_lista = $conn->query($sql_lista);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Gestión Productos — Cafetería UTHH</title>
    
    <link rel="stylesheet" href="../archivosCSS/layout.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="../archivosCSS/registro.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="../archivosCSS/gestion_productos.css?v=<?php echo time(); ?>">
</head>

<body>
    <div class="app">

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
                    }, 5000);
                </script>
            <?php endif; ?>

            <div class="form-container">
                <div class="header-flex">
                    <h2><?php echo $modo_edicion ? 'Modificar Producto' : 'Agregar Nuevo Producto'; ?></h2>

                    <a href="productos.php" target="_blank" class="btn-preview">
                        👁️ Ver Catálogo
                    </a>
                </div>
                
                <form action="<?php echo $accion_form; ?>" method="post" enctype="multipart/form-data">
    <div class="form-grid">

        <div class="form-column">
            <div class="form-row">
                <label>Nombre</label>
                <input type="text" name="nombre" value="<?php echo $nom_val; ?>" required />
            </div>
            <div class="form-row">
                <label>Descripción</label>
                <input type="text" name="descripcion" value="<?php echo $desc_val; ?>" required />
            </div>
            <div class="form-row">
                <label>Categoría</label>
                <select name="categoria" required>
                    <option value="">Seleccionar...</option>
                    <?php
                    $res_categorias->data_seek(0);
                    while ($cat = $res_categorias->fetch_assoc()): ?>
                        <option value="<?php echo $cat['intIdCategoria']; ?>"
                            <?php echo ($cat_val == $cat['intIdCategoria']) ? 'selected' : ''; ?>>
                            <?php echo $cat['vchCategoria']; ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="form-row">
                <label>Stock</label>
                <input type="number" name="stock" value="<?php echo $stock_val; ?>" required min="0" 
       oninput="this.value = Math.abs(this.value)" />
            </div>
            
            <div class="form-row"> 
    <label>Precio Compra</label>
    <input type="number" step="0.01" name="precio_compra" value="<?php echo $pcompra_val; ?>" required min="0" />
</div>

<div class="form-row"> 
    <label>Precio Venta</label>
    <input type="number" step="0.01" name="precio_venta" value="<?php echo $pventa_val; ?>" required min="0" />
</div>
            <div class="form-row">
                <label>Proveedor</label>
                <select name="proveedor" required>
                    <option value="">Seleccionar...</option>
                    <?php
                    $res_proveedores->data_seek(0);
                    while ($prov = $res_proveedores->fetch_assoc()): ?>
                        <option value="<?php echo $prov['vchRFC']; ?>"
                            <?php echo ($prov_val == $prov['vchRFC']) ? 'selected' : ''; ?>>
                            <?php echo $prov['vchEmpresa']; ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="image-upload-area">
                 <img id="preview" src="<?php echo !empty($img_val) ? '../' . $img_val : 'https://placehold.co/300x200?text=Sin+Imagen'; ?>" alt="Vista previa">
                 <label style="color: white; font-size: 0.9rem; margin-bottom: 5px;">Subir Imagen:</label>
                 <input type="file" name="imagen" class="image-input" accept="image/*" onchange="mostrarVistaPrevia(event)">
                 <input type="hidden" name="imagen_actual" value="<?php echo $img_val; ?>">
            </div>

        </div> </div> <div class="actions">
        <button class="btn-action btn-add" type="submit">
            <?php echo $modo_edicion ? 'Guardar Cambios' : 'Agregar Producto'; ?>
        </button>
        <?php if ($modo_edicion): ?>
            <a href="gestion_productos.php" class="btn-action form-cancel-btn">Cancelar</a>
        <?php endif; ?>
    </div>

</form>
            </div>

            <div class="list-container">
                <h2>Inventario Actual</h2>

                <table class="user-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Img</th>
                            <th>Producto</th>
                            <th>Categoría</th>
                            <th>Stock</th>
                            <th>P. Venta</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
    <?php if ($res_lista && $res_lista->num_rows > 0): ?>
        <?php while ($row = $res_lista->fetch_assoc()): ?>
            <tr>
                <td data-label="ID"><?php echo $row['intIdProducto']; ?></td>
                
                <td data-label="Imagen">
                    <?php if (!empty($row['vchImagen'])): ?>
                        <img src="../<?php echo $row['vchImagen']; ?>" class="thumb-img" alt="img">
                    <?php else: ?>
                        <span>🚫</span>
                    <?php endif; ?>
                </td>
                
                <td data-label="Producto"><?php echo htmlspecialchars($row['vchNombre']); ?></td>
                <td data-label="Categoría"><?php echo htmlspecialchars($row['vchCategoria']); ?></td>
                
                <td data-label="Stock" style="<?php echo ($row['intStock'] < 10) ? 'color:red; font-weight:bold;' : ''; ?>">
                    <?php echo $row['intStock']; ?>
                </td>
                
                <td data-label="P. Venta">$<?php echo number_format($row['decPrecioVenta'], 2); ?></td>
                
                <td data-label="Acciones" class='action-links'>
                    <a href="gestion_productos.php?accion=editar&id=<?php echo $row['intIdProducto']; ?>" class="edit-link">Modificar</a>
                    <a href="procesar_producto.php?accion=eliminar&id=<?php echo $row['intIdProducto']; ?>"
                       class="delete-link" onclick="return confirm('¿Eliminar permanentemente?');">Eliminar</a>
                </td>
            </tr>
        <?php endwhile; ?>
    <?php else: ?>
        <tr>
            <td colspan="7">No hay productos registrados.</td>
        </tr>
    <?php endif; ?>
</tbody>
                </table>
            </div>

        </main>
    </div>

    <script>
        function mostrarVistaPrevia(event) {
            var reader = new FileReader();
            reader.onload = function() {
                var output = document.getElementById('preview');
                output.src = reader.result;
            };
            reader.readAsDataURL(event.target.files[0]);
        }
    </script>
</body>

</html>
<?php
if (isset($conn) && $conn instanceof mysqli) {
    $conn->close();
}
?>
