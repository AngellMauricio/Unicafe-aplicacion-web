<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: login.html");
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Menú – Cafetería UTHH</title>
  <link rel="stylesheet" href="/archivosCSS/home.css" />
  <link rel="stylesheet" href="/archivosCSS/menu_desplegable.css" />
  <link rel="stylesheet" href="/archivosCSS/footer.css" />
  <link rel="stylesheet" href="/archivosCSS/accesibilidad.css" />
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
    .tile__img {
        width: 80px; height: 80px; flex-shrink: 0;
        background-color: #eee; border-radius: 5px; overflow: hidden;
        display: flex; align-items: center; justify-content: center;
    }
    .tile__img img { width: 100%; height: 100%; object-fit: cover; }
    
    /* Estilo para cuando no hay imagen (placeholder SVG) */
    .no-image-placeholder {
        width: 100%; height: 100%;
        background-color: #efe3cf; color: #8a633b;
        display: flex; align-items: center; justify-content: center;
        font-weight: bold; font-size: 20px; text-align: center;
    }
  </style>
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
    </header>
    <!-- NAV -->
    <nav class="nav">
      <div class="nav__wrap">
        <a class="pill" href="/archivosPHP/index.php"><span class="ico">🏠</span> HOME</a>
        <a class="pill" href="productos.php"><span class="ico">📦</span> PRODUCTOS</a>
        <a class="pill is-active" href="menu.php"><span class="ico">🍽️</span> MENÚ</a>
        <a class="pill" href="pedidos.php"><span class="ico">🧾</span> PEDIDOS</a>
        <?php if (isset($_SESSION['rol_id']) && $_SESSION['rol_id'] == 1) { ?>
          <a class="pill" href="gestion_productos.php">⚙️ GESTIÓN PROD.</a>
          <a class="pill" href="usuarios.php">REGISTROS <span class="ico">👤</span></a>
        <?php } ?>
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
                    // CORRECCIÓN DE RUTA DE IMAGEN + FALLBACK
                    // ============================================================
                    $imgDb = $p['vchImagen'];
                    $rutaFisica = "../" . $imgDb;
                    
                    // Determinamos si mostrar la imagen o el placeholder
                    // Usamos una imagen por defecto transparente para activar el 'onerror' si falla
                    $mostrarImagen = false;
                    if (!empty($imgDb) && file_exists($rutaFisica)) {
                        $mostrarImagen = true;
                        $imgSrc = $rutaFisica;
                    }
                ?>
              <article class="tile">
                <div class="tile-row">
                  <div class="tile__img">
                    <?php if ($mostrarImagen): ?>
                        <!-- Si falla la carga (onerror), se oculta la imagen y se muestra el div de fondo -->
                        <img src="<?php echo $imgSrc; ?>" alt="<?php echo htmlspecialchars($p['vchNombre']); ?>" 
                             onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                        <!-- Fallback oculto por defecto -->
                        <div class="no-image-placeholder" style="display:none;">
                            <?php echo strtoupper(substr($p['vchNombre'], 0, 1)); ?>
                        </div>
                    <?php else: ?>
                        <!-- Si no hay imagen en BD, mostramos inicial del nombre -->
                        <div class="no-image-placeholder">
                            <?php echo strtoupper(substr($p['vchNombre'], 0, 1)); ?>
                        </div>
                    <?php endif; ?>
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

      <div class="footer-links">
        <a href="/unicafe/archivosPHP/aviso_privacidad.php"
          >Aviso de Privacidad</a
        >
        <span class="separator">|</span>
        <a href="/archivosPHP/terminos.php">Terminos y condiciones</a>
        <span class="separator">|</span>
        <a href="/unicafe/archivosHTML/somosUnicafe.html">Sobre nosotros</a>
      </div>
    </footer>
    <button
      id="btn-voz"
      class="voice-btn"
      aria-label="Escuchar el contenido de la página"
    >
      🔊 Escuchar Contenido
    </button>
    <script src="/archivosJS/lector_voz.js"></script>

    <script src="/archivosJS/accesibilidad.js"></script>

    <div class="accessibility-panel">
      <button id="btn-zoom-in" aria-label="Aumentar tamaño">A+</button>
      <button id="btn-zoom-reset" aria-label="Restablecer tamaño">↺</button>
      <button id="btn-zoom-out" aria-label="Disminuir tamaño">A-</button>

      <button
        id="btn-contrast"
        aria-label="Cambiar modo de color"
        style="margin-top: 5px; border-color: #2a9d8f; color: #2a9d8f"
      >
        🌗
      </button>
    </div>
</footer>
</body>
</html>
<?php $conn->close(); ?>