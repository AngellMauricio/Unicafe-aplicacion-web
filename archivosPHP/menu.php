<?php
require_once "conexion.php";

// ==========================
//  MODO EDICIÓN (GET)
// ==========================
$modo     = $_GET['modo'] ?? "";
$idEditar = $_GET['id'] ?? "";

$platilloEditar = null;

if ($modo === "editar" && $idEditar !== "") {
    $id = (int)$idEditar;
    $stmt = $conn->prepare("SELECT * FROM tblmenu WHERE intIdPlatillo = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $resultado = $stmt->get_result();
    $platilloEditar = $resultado->fetch_assoc();
    $stmt->close();
}

// ==========================
//  OBTENER TODOS LOS PLATILLOS
// ==========================
$sql = "SELECT * FROM tblmenu ORDER BY vchCategoria, vchNombre";
$res = $conn->query($sql);

$categorias = [];
while ($row = $res->fetch_assoc()) {
    $cat = $row['vchCategoria'];
    if (!isset($categorias[$cat])) {
        $categorias[$cat] = [];
    }
    $categorias[$cat][] = $row;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Menú – Cafetería UTHH</title>
  <link rel="stylesheet" href="../archivosCSS/menu.css" />
  <link rel="stylesheet" href="../archivosCSS/footer.css" />

  <style>
    /* Formularios CRUD */
    .form-crud {
      max-width: 600px; margin: 15px auto; padding: 15px;
      background: #fdf5e6; border-radius: 10px; border: 1px solid #d0b38a;
    }
    .form-crud h2 { margin-top: 0; text-align: center; }
    .form-crud label { display: block; margin-top: 6px; font-size: 14px; font-weight: bold; }
    .form-crud input[type="text"], .form-crud input[type="number"] {
      width: 100%; padding: 8px; margin-top: 2px; border-radius: 5px; border: 1px solid #ccc;
    }
    .form-crud button, .form-crud .btn-cancelar {
      margin-top: 15px; padding: 8px 15px; border-radius: 5px; border: none; cursor: pointer; font-weight: 600;
    }
    .form-crud button { background: #28a745; color: #fff; }
    .form-crud .btn-cancelar { background: #6c757d; color: #fff; text-decoration: none; display: inline-block; }

    /* Estilos para previsualización de imagen */
    .img-preview-box {
        margin-top: 10px; text-align: center; background: #fff; padding: 10px; border: 1px dashed #ccc;
    }
    .img-preview-box img { max-height: 150px; object-fit: contain; }

    /* Botones de cada platillo */
    .tile-row{ display:flex; align-items:center; justify-content:space-between; gap:8px; }
    .tile__actions{ display:flex; flex-direction:column; gap:4px; }
    .btn-crud{ border:none; padding:4px 8px; border-radius:4px; font-size:11px; cursor:pointer; }
    .btn-editar{ background:#699dd4; color:#fff; text-decoration:none; text-align:center; }
    .btn-eliminar{ background:#dd5865; color:#fff; }
    
    /* Corrección imagen tarjeta */
    .tile__img img { width: 100%; height: 100%; object-fit: cover; }
  </style>
</head>
<body>
  <div class="app">

    <header class="topbar">
      <div class="topbar__left">
        <span class="avatar">👤</span>
        <a class="login-pill" href="/archivosHTML/login.html">Iniciar Sesión</a>
      </div>
      <h1 class="title">CAFETERIA UTHH</h1>
    </header>

    <nav class="nav">
      <div class="nav__wrap">
        <a class="pill" href="../index.html">🏠 HOME</a>
        <a class="pill" href="productos.php">📦 PRODUCTOS</a>
        <a class="pill is-active" href="menu.php">🍽️ MENÚ</a>
        <a class="pill" href="pedidos.php">🧾 PEDIDOS</a>
        <a class="pill" href="usuarios.php">👤 REGISTROS</a>
      </div>
    </nav>

    <main class="content">

      <!-- ========== FORMULARIO: AGREGAR ========== -->
      <?php if (!$platilloEditar): ?>
      <form class="form-crud" action="menu_acciones.php?accion=agregar" method="post" enctype="multipart/form-data">
        <h2>Agregar nuevo platillo</h2>

        <label>Categoría</label>
        <input type="text" name="categoria" placeholder="Guisados, Tacos, Tortas..." required>

        <label>Nombre del platillo</label>
        <input type="text" name="nombre" placeholder="Ej. Torta de milanesa" required>

        <label>Precio</label>
        <input type="number" step="0.01" name="precio" placeholder="Ej. 40" required>

        <label>Imagen del Platillo</label>
        <input type="file" name="imagen" accept="image/*">

        <button type="submit">Agregar platillo</button>
      </form>
      <?php endif; ?>

      <!-- ========== FORMULARIO: EDITAR ========== -->
      <?php if ($platilloEditar): ?>
      <form class="form-crud" action="menu_acciones.php?accion=actualizar&id=<?php echo $platilloEditar['intIdPlatillo']; ?>" method="post" enctype="multipart/form-data">
        <h2>Editar platillo</h2>

        <label>Categoría</label>
        <input type="text" name="categoria" value="<?php echo htmlspecialchars($platilloEditar['vchCategoria']); ?>" required>

        <label>Nombre del platillo</label>
        <input type="text" name="nombre" value="<?php echo htmlspecialchars($platilloEditar['vchNombre']); ?>" required>

        <label>Precio</label>
        <input type="number" step="0.01" name="precio" value="<?php echo htmlspecialchars($platilloEditar['decPrecio']); ?>" required>

        <label>Imagen (Dejar vacío para no cambiar)</label>
        <input type="file" name="imagen" accept="image/*">
        
        <input type="hidden" name="imagen_actual" value="<?php echo htmlspecialchars($platilloEditar['vchImagen']); ?>">

        <?php if(!empty($platilloEditar['vchImagen'])): ?>
            <div class="img-preview-box">
                <p style="margin:0; font-size:12px; color:#666;">Imagen Actual:</p>
                <!-- Aquí también agregamos el ../ -->
                <img src="../<?php echo htmlspecialchars($platilloEditar['vchImagen']); ?>" alt="Actual">
            </div>
        <?php endif; ?>

        <button type="submit">Guardar cambios</button>
        <a class="btn-cancelar" href="menu.php">Cancelar</a>
      </form>
      <?php endif; ?>

      <!-- ========== MENÚ DINÁMICO (TARJETAS) ========== -->
      <div class="menu-grid">
        <?php foreach ($categorias as $nombreCat => $items): ?>
          <section class="category">
            <h3 class="category__title"><?php echo htmlspecialchars($nombreCat); ?></h3>

            <?php foreach ($items as $p): ?>
                <?php 
                    // ============================================================
                    // CORRECCIÓN DE RUTA DE IMAGEN
                    // ============================================================
                    $imgDb = $p['vchImagen'];
                    
                    // 1. Construir la ruta física RELATIVA AL SERVIDOR para checar si existe
                    //    menu.php está en archivosPHP/, así que salimos con ../
                    $rutaFisica = "../" . $imgDb;

                    // 2. Verificar
                    if (!empty($imgDb) && file_exists($rutaFisica)) {
                        // Si existe, usamos la ruta con ../ para que el HTML la encuentre
                        $imgSrc = $rutaFisica;
                    } else {
                        // Si no, placeholder
                        $imgSrc = "https://placehold.co/300x200/efe3cf/8a633b?text=" . urlencode($p['vchNombre']);
                    }
                ?>
              <article class="tile">
                <div class="tile-row">
                  <div class="tile__img">
                    <img src="<?php echo $imgSrc; ?>" alt="<?php echo htmlspecialchars($p['vchNombre']); ?>">
                  </div>
                  <div class="tile__info">
                    <strong><?php echo htmlspecialchars($p['vchNombre']); ?></strong>
                    <span class="price">$<?php echo number_format($p['decPrecio'], 2); ?></span>
                  </div>
                  <div class="tile__actions">
                    <a class="btn-crud btn-editar" href="menu.php?modo=editar&id=<?php echo $p['intIdPlatillo']; ?>">Editar</a>
                    <form action="menu_acciones.php?accion=eliminar&id=<?php echo $p['intIdPlatillo']; ?>" method="post" onsubmit="return confirm('¿Eliminar este platillo?');">
                      <button type="submit" class="btn-crud btn-eliminar">Eliminar</button>
                    </form>
                  </div>
                </div>
              </article>
            <?php endforeach; ?>

          </section>
        <?php endforeach; ?>
      </div>
    </main>
  </div>

  <footer class="footer">
    <p>Universidad Tecnológica de la Huasteca Hidalguense</p>
    <p>&copy; 2025 Cafetería UTHH. Todos los derechos reservados.</p>
    <form action="#contacto.html" method="get">
      <button type="submit" class="btn-contacto">Contáctanos</button>
    </form>
  </footer>
</body>
</html>
<?php $conn->close(); ?>