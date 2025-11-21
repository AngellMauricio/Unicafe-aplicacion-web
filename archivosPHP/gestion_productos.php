<?php
include 'conexion.php';

// --- 1. OBTENER DATOS PARA LISTAS DESPLEGABLES (Comboboxes) ---
$sql_categorias = "SELECT intIdCategoria, vchCategoria FROM tblcategorias";
$res_categorias = $conn->query($sql_categorias);

$sql_proveedores = "SELECT vchRFC, vchEmpresa FROM tblproveedores";
$res_proveedores = $conn->query($sql_proveedores);

// --- 2. LÓGICA PARA EDICIÓN ---
$modo_edicion = false;
$id_prod_editar = 0;
// Variables por defecto vacías
$nom_val = "";
$desc_val = "";
$stock_val = "";
$pcompra_val = "";
$pventa_val = "";
$cat_val = "";
$prov_val = "";

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
    }
    $stmt->close();
}

// --- 3. LÓGICA PARA EL LISTADO (JOIN para ver nombres en vez de IDs) ---
$sql_lista = "SELECT P.intIdProducto, P.vchNombre, P.intStock, P.decPrecioVenta, 
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
    <!-- Ajusta la ruta del CSS según tu estructura (ej: ../archivosCSS/registro.css) -->
    <link rel="stylesheet" href="../archivosCSS/registro.css">
    <link rel="stylesheet" href="../archivosCSS/gestion_productos.css">
    
</head>

<body>
    <div class="app">
        <header class="topbar">
            <div class="topbar__left">
                <span class="avatar">📦</span>
                <a class="login-pill" href="../archivosHTML/login.html">Cerrar Sesión</a>
            </div>
            <h1 class="title">CAFETERIA UTHH</h1>
        </header>

        <nav class="nav">
            <div class="nav__wrap">
                <a class="pill" href="../index.html">🏠 HOME</a>
                <a class="pill" href="/archivosPHP/productos.php">📦 PRODUCTOS (Vista Cliente)</a>
                <a class="pill is-active" href="gestion_productos.php">⚙️ GESTIÓN PROD.</a>
                <a class="pill" href="../archivosHTML/menu.html"><span class="ico">🍽️</span> MENÚ</a>
                <a class="pill" href="../archivosHTML/pedidos.html"><span class="ico">🧾</span> PEDIDOS</a>
                <a class="pill" href="usuarios.php">👤 USUARIOS</a>
            </div>
        </nav>

        <main class="content">

            <!-- CONTENEDOR FORMULARIO -->
            <div class="form-container">
                <h2><?php echo $modo_edicion ? 'Modificar Producto' : 'Agregar Nuevo Producto'; ?></h2>

                <form action="<?php echo $accion_form; ?>" method="post">
                    <div class="form-grid">

                        <!-- COLUMNA 1 -->
                        <div class="form-column">
                            <div class="form-row"><label>Nombre</label><input type="text" name="nombre" value="<?php echo $nom_val; ?>" required /></div>
                            <div class="form-row"><label>Descripción</label><input type="text" name="descripcion" value="<?php echo $desc_val; ?>" required /></div>

                            <!-- Select de Categorías -->
                            <div class="form-row">
                                <label>Categoría</label>
                                <select name="categoria" required>
                                    <option value="">Seleccionar...</option>
                                    <?php
                                    $res_categorias->data_seek(0); // Reiniciar puntero
                                    while ($cat = $res_categorias->fetch_assoc()): ?>
                                        <option value="<?php echo $cat['intIdCategoria']; ?>"
                                            <?php echo ($cat_val == $cat['intIdCategoria']) ? 'selected' : ''; ?>>
                                            <?php echo $cat['vchCategoria']; ?>
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>

                            <div class="form-row"><label>Stock</label><input type="number" name="stock" value="<?php echo $stock_val; ?>" required /></div>

                            <!-- Botones -->
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

                            <!-- Select de Proveedores -->
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

                            <!-- Espaciador gris -->
                            <div class="data-area"></div>
                        </div>
                    </div>
                </form>
            </div>

            <!-- CONTENEDOR LISTA -->
            <div class="list-container">
                <h2>Inventario Actual</h2>

                <table class="user-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Producto</th>
                            <th>Categoría</th>
                            <th>Stock</th>
                            <th>P. Venta</th>
                            <th>Proveedor</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($res_lista && $res_lista->num_rows > 0): ?>
                            <?php while ($row = $res_lista->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo $row['intIdProducto']; ?></td>
                                    <td><?php echo htmlspecialchars($row['vchNombre']); ?></td>
                                    <td><?php echo htmlspecialchars($row['vchCategoria']); ?></td>

                                    <!-- Resaltar si hay poco stock -->
                                    <td style="<?php echo ($row['intStock'] < 10) ? 'color:red; font-weight:bold;' : ''; ?>">
                                        <?php echo $row['intStock']; ?>
                                    </td>

                                    <td>$<?php echo number_format($row['decPrecioVenta'], 2); ?></td>
                                    <td><?php echo htmlspecialchars($row['vchEmpresa']); ?></td>

                                    <td class='action-links'>
                                        <a href="gestion_productos.php?accion=editar&id=<?php echo $row['intIdProducto']; ?>" class="edit-link">Modificar</a>
                                        <a href="procesar_producto.php?accion=eliminar&id=<?php echo $row['intIdProducto']; ?>"
                                            class="delete-link"
                                            onclick="return confirm('¿Eliminar este producto permanentemente?');">Eliminar</a>
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
</body>

</html>
<?php $conn->close(); ?>