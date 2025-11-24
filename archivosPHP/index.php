<?php
session_start();
// Validación de sesión
if (!isset($_SESSION['usuario'])) {
    header("Location: login.html");
    exit;
}

// CONEXIÓN A LA BASE DE DATOS
require_once __DIR__ . '/conexion.php';

// 1. IZQUIERDA: CONSULTA PARA "MENÚ" (Platillos preparados)
// Traemos 4 platillos aleatorios de la tabla de menú
$sql_menu = "SELECT * FROM tblmenu ORDER BY RAND() LIMIT 4";
$res_menu = $conn->query($sql_menu);

// 2. DERECHA: CONSULTA PARA "PRODUCTOS" (Inventario/Empaquetados)
// Traemos 3 productos aleatorios de la tabla de productos
$sql_productos = "SELECT * FROM tblproductos ORDER BY RAND() LIMIT 3";
$res_productos = $conn->query($sql_productos);
?>
<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Cafetería UTHH</title>
  
  <link rel="stylesheet" href="../archivosCSS/home.css?v=3.5" />
  <link rel="stylesheet" href="../archivosCSS/menu_desplegable.css" />
  <link rel="stylesheet" href="../archivosCSS/footer.css?v=3.5" />
  <link rel="stylesheet" href="../archivosCSS/accesibilidad.css" />
  
  <style>
    /* Estilo para cuando no hay imagen */
    .no-image-placeholder {
        width: 100%; height: 100%;
        background-color: #efe3cf; color: #8a633b;
        display: flex; align-items: center; justify-content: center;
        font-weight: bold; font-size: 24px; text-align: center;
    }
    .mini__img img, .special__img img {
        width: 100%; height: 100%; object-fit: cover;
    }
    /* --- Estilos para hacer las tarjetas clicables --- */
.card-link {
    text-decoration: none; /* Quita el subrayado del enlace */
    color: inherit;        /* Mantiene el color original del texto */
    display: block;        /* Ocupa todo el espacio */
    transition: transform 0.2s ease; /* Suaviza la animación */
}

/* Efecto al pasar el mouse: se hace un poquito más grande */
.card-link:hover {
    transform: scale(1.03); 
    cursor: pointer;
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
      <div class="topbar__right"></div>
    </header>
    
    <nav class="nav">
      <div class="nav__wrap">
        <a class="pill is-active" href="index.php">HOME <span class="ico">🏠</span></a>
        <?php if (isset($_SESSION['rol_id']) && $_SESSION['rol_id'] == 3) { ?>
        <a class="pill" href="productos.php">PRODUCTOS <span class="ico">📦</span></a>
        <a class="pill" href="menu.php">MENÚ <span class="ico">🍽️</span></a>
        <a class="pill" href="pedidos.php">PEDIDOS <span class="ico">🧾</span></a>
        <?php } ?>
        <?php if (isset($_SESSION['rol_id']) && $_SESSION['rol_id'] == 1) { ?>
          <a class="pill" href="gestion_productos.php">⚙️ GESTIÓN PROD.</a>
          <a class="pill" href="gestion_terminos.php">⚙️ GESTIÓN TÉRMINOS</a>
          <a class="pill" href="editar_aviso.php">⚙️ GESTIÓN AVISO DE PRIVACIDAD</a>
          <a class="pill" href="usuarios.php">REGISTROS <span class="ico">👤</span></a>
        <?php } ?>
      </div>
    </nav>

    <main class="content">
      <div class="container">
        
        <section>
          <h2 class="section-title">Menú del Día</h2>
          <div class="menu-card">
            <div class="menu-grid">
              
              <?php if ($res_menu && $res_menu->num_rows > 0): ?>
                  <?php while ($row = $res_menu->fetch_assoc()): ?>
                      <?php 
                          // Ajuste de ruta para tblmenu
                          $ruta_img = !empty($row['vchImagen']) ? "../" . $row['vchImagen'] : ""; 
                      ?>
                      <article class="mini">
                        <div class="mini__img">
                          <?php if (!empty($ruta_img) && file_exists($ruta_img)): ?>
                             <img src="<?php echo $ruta_img; ?>" alt="<?php echo htmlspecialchars($row['vchNombre']); ?>">
                          <?php else: ?>
                             <div class="no-image-placeholder">
                                <?php echo strtoupper(substr($row['vchNombre'], 0, 1)); ?>
                             </div>
                          <?php endif; ?>
                        </div>
                        <ul class="mini__lines">
                          <li style="font-weight:bold;"><?php echo htmlspecialchars($row['vchNombre']); ?></li>
                          <li><?php echo htmlspecialchars($row['vchCategoria']); ?></li>
                          <li class="price">$<?php echo number_format($row['decPrecio'], 2); ?> MXN</li>
                        </ul>
                      </article>
                  <?php endwhile; ?>
              <?php else: ?>
                  <p>No hay platillos disponibles.</p>
              <?php endif; ?>

            </div>
          </div>
        </section>

        <section>
          <h2 class="section-title">Productos Disponibles</h2>
          <div class="specials-grid">
            
            <?php if ($res_productos && $res_productos->num_rows > 0): ?>
                <?php while ($prod = $res_productos->fetch_assoc()): ?>
                    <?php 
                        $ruta_img_prod = !empty($prod['vchImagen']) ? "../" . $prod['vchImagen'] : ""; 
                    ?>
                    
                    <a href="productos.php" class="card-link">
                        <article class="special">
                          <div class="special__img">
                              <?php if (!empty($ruta_img_prod) && file_exists($ruta_img_prod)): ?>
                                 <img src="<?php echo $ruta_img_prod; ?>" alt="<?php echo htmlspecialchars($prod['vchNombre']); ?>">
                              <?php else: ?>
                                 <div class="no-image-placeholder">
                                    <?php echo strtoupper(substr($prod['vchNombre'], 0, 1)); ?>
                                 </div>
                              <?php endif; ?>
                          </div>
                          <ul class="special__lines">
                            <li style="font-weight:bold; font-size:1.1em;"><?php echo htmlspecialchars($prod['vchNombre']); ?></li>
                            
                            <li style="font-size:0.9em; color:#666;">
                                <?php echo htmlspecialchars($prod['vchDescripcion']); ?>
                            </li>
                            
                            <li class="price">$<?php echo number_format($prod['decPrecioVenta'], 2); ?> MXN</li>
                          </ul>
                        </article>
                    </a>
                    
                <?php endwhile; ?>
            <?php else: ?>
                <p>No hay productos destacados por el momento.</p>
            <?php endif; ?>

          </div>
        </section>
      </div>
    </main>
  </div>
  
  <footer class="footer">
    <p>Universidad Tecnológica de la Huasteca Hidalguense</p>
    <p>&copy; 2025 Cafetería UTHH. Todos los derechos reservados.</p>

    <div class="footer-links">
      <a href="aviso_privacidad.php">Aviso de Privacidad</a>
      <span class="separator">|</span>
      <a href="terminos.php">Terminos y condiciones</a>
      <span class="separator">|</span>
      <a href="../archivosHTML/somosUnicafe.html">Sobre nosotros</a>
    </div>
  </footer>
  
  <button id="btn-voz" class="voice-btn" aria-label="Escuchar el contenido de la página">
    🔊 Escuchar Contenido
  </button>
  <script src="../archivosJS/lector_voz.js"></script>

  <script src="../archivosJS/accesibilidad.js"></script>

  <div class="accessibility-panel">
    <button id="btn-zoom-in" aria-label="Aumentar tamaño">A+</button>
    <button id="btn-zoom-reset" aria-label="Restablecer tamaño">↺</button>
    <button id="btn-zoom-out" aria-label="Disminuir tamaño">A-</button>

    <button id="btn-contrast" aria-label="Cambiar modo de color" style="margin-top: 5px; border-color: #2a9d8f; color: #2a9d8f">
      🌗
    </button>
  </div>
</body>
</html>
<?php 
if(isset($conn)) $conn->close(); 
?>