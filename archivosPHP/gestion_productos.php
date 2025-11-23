<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: login.html");
    exit;
}
if ($_SESSION['rol_id'] != 1) {
  // Si está logueado pero NO es admin, mándalo al inicio o muestra error
  echo "<script>alert('Acceso denegado: Solo administradores.'); window.location.href='../index.php';</script>";
  exit();
}


// TODOS LOS PHP ESTÁN EN LA MISMA CARPETA:
require_once __DIR__ . '/conexion.php'; 
// --- 1. OBTENER DATOS PARA LISTAS ---
$sql_categorias = "SELECT intIdCategoria, vchCategoria FROM tblcategorias";
$res_categorias = $conn->query($sql_categorias);

$sql_proveedores = "SELECT vchRFC, vchEmpresa FROM tblproveedores";
$res_proveedores = $conn->query($sql_proveedores);

// --- 2. LÓGICA PARA EDICIÓN ---
$modo_edicion = false;
$id_prod_editar = 0;
$nom_val = "";
$desc_val = "";
$stock_val = "";
$pcompra_val = "";
$pventa_val = "";
$cat_val = "";
$prov_val = "";
$img_val = ""; // Variable para la imagen

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
        $img_val = $fila['vchImagen']; // Recuperamos la ruta de la imagen
    }
    $stmt->close();
}

// --- 3. LÓGICA PARA EL LISTADO ---
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
    <title>Gestión de Productos — Cafetería UTHH</title>
    <link rel="stylesheet" href="../archivosCSS/registro.css">
    <link rel="stylesheet" href="../archivosCSS/menu_desplegable.css" />
    <link rel="stylesheet" href="../archivosCSS/gestion_productos.css">
    <link rel="stylesheet" href="../archivosCSS/footer.css" />
   
    </head>

<body>
    <div class="app">
        <header class="topbar">
            <div class="topbar__left">
                <span class="avatar" aria-hidden="true">👤</span>

                <div class="user-dropdown">
                    <span class="user-trigger">
                        Hola, <?php echo htmlspecialchars($_SESSION['usuario']); ?> <span style="font-size:0.8em">▼</span>
                    </span>
                    <div class="dropdown-content">
                        <a href="mi_cuenta.php">⚙️ Mi Cuenta</a>
                        <a href="logout.php" class="logout-link">🚪 Cerrar Sesión</a>
                    </div>
                </div>
            </div>
            <h1 class="title">CAFETERIA UTHH</h1>
            <div class="topbar__right"></div>
        </header>

        <nav class="nav">
            <div class="nav__wrap">
                <a class="pill is-active" href="/archivosPHP/index.php">HOME <span class="ico">🏠</span></a>
                <a class="pill" href="productos.php">PRODUCTOS <span class="ico">📦</span></a>
                <a class="pill" href="menu.php">MENÚ <span class="ico">🍽️</span></a>
                <a class="pill" href="pedidos.php">PEDIDOS <span class="ico">🧾</span></a>
                <?php if (isset($_SESSION['rol_id']) && $_SESSION['rol_id'] == 1) { ?>
                    <a class="pill is-active" href="gestion_productos.php">⚙️ GESTIÓN PROD.</a>
                    <a class="pill" href="usuarios.php">REGISTROS <span class="ico">👤</span></a>
                <?php } ?>
            </div>
        </nav>

        <main class="content">

            <!-- FORMULARIO -->
            <div class="form-container">
                <h2><?php echo $modo_edicion ? 'Modificar Producto' : 'Agregar Nuevo Producto'; ?></h2>

                <!-- IMPORTANTE: enctype="multipart/form-data" es necesario para subir archivos -->
                <form action="<?php echo $accion_form; ?>" method="post" enctype="multipart/form-data">
                    <div class="form-grid">

                        <!-- COLUMNA 1 -->
                        <div class="form-column">
                            <div class="form-row"><label>Nombre</label><input type="text" name="nombre" value="<?php echo $nom_val; ?>" required /></div>
                            <div class="form-row"><label>Descripción</label><input type="text" name="descripcion" value="<?php echo $desc_val; ?>" required /></div>

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

                            <div class="form-row"><label>Stock</label><input type="number" name="stock" value="<?php echo $stock_val; ?>" required /></div>

                            <div class="actions">
                                <button class="btn-action btn-add" type="submit">
                                    <?php echo $modo_edicion ? 'Guardar Cambios' : 'Agregar Producto'; ?>
                                </button>
                                <?php if ($modo_edicion): ?>
                                    <a href="gestion_productos.php" class="btn-action form-cancel-btn">Cancelar</a>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- COLUMNA 2 -->
                        <div class="form-column">
                            <div class="form-row"><label>Precio Compra</label><input type="number" step="0.01" name="precio_compra" value="<?php echo $pcompra_val; ?>" required /></div>
                            <div class="form-row"><label>Precio Venta</label><input type="number" step="0.01" name="precio_venta" value="<?php echo $pventa_val; ?>" required /></div>

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

                            <!-- ÁREA DE IMAGEN (Reemplaza al data-area vacío) -->
                            <div class="image-upload-area">
                                <!-- Previsualización -->
                                <img id="preview" src="<?php echo !empty($img_val) ? '../' . $img_val : 'https://placehold.co/300x200?text=Sin+Imagen'; ?>" alt="Vista previa">

                                <!-- Input para subir archivo -->
                                <label style="color: white; font-size: 0.9rem; margin-bottom: 5px;">Subir Imagen:</label>
                                <input type="file" name="imagen" class="image-input" accept="image/*" onchange="mostrarVistaPrevia(event)">

                                <!-- Input oculto para mantener la imagen anterior al editar si no se sube una nueva -->
                                <input type="hidden" name="imagen_actual" value="<?php echo $img_val; ?>">
                            </div>

                        </div>
                    </div>
                </form>
            </div>

            <!-- LISTA -->
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
                                    <td><?php echo $row['intIdProducto']; ?></td>
                                    <td>
                                        <?php if (!empty($row['vchImagen'])): ?>
                                            <img src="../<?php echo $row['vchImagen']; ?>" class="thumb-img" alt="img">
                                        <?php else: ?>
                                            <span>🚫</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo htmlspecialchars($row['vchNombre']); ?></td>
                                    <td><?php echo htmlspecialchars($row['vchCategoria']); ?></td>
                                    <td style="<?php echo ($row['intStock'] < 10) ? 'color:red; font-weight:bold;' : ''; ?>">
                                        <?php echo $row['intStock']; ?>
                                    </td>
                                    <td>$<?php echo number_format($row['decPrecioVenta'], 2); ?></td>
                                    <td class='action-links'>
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

    <!-- Script simple para vista previa de imagen -->
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