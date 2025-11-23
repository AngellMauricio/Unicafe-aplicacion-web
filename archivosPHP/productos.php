<?php
session_start();
if (!isset($_SESSION['usuario'])) {
  header("Location: login.html");
  exit;
}
require_once __DIR__ . '/conexion.php'; 
// 2. Preparamos la consulta SQL
$sql = "SELECT vchNombre, vchDescripcion, intStock, decPrecioVenta, vchImagen 
        FROM tblproductos";

$resultado_productos = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Productos – Cafetería UTHH</title>

  <!-- Estilos Generales -->
  <link rel="stylesheet" href="/archivosCSS/home.css" />
  <link rel="stylesheet" href="/archivosCSS/productos.css" />
  <link rel="stylesheet" href="/archivosCSS/menu_desplegable.css" />
  <link rel="stylesheet" href="/archivosCSS/footer.css" />
  <link rel="stylesheet" href="/archivosCSS/accesibilidad.css" />
  
  <!-- Estilos de la Ventana Emergente (Nuevo archivo) -->
  <link rel="stylesheet" href="../archivosCSS/ventanaEmergente.css" />
</head>

<body>
  <div class="app">

    <!-- TOPBAR -->
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

    <!-- NAV -->
    <nav class="nav">
      <div class="nav__wrap">
        <a class="pill" href="../archivosPHP/index.php"><span class="ico">🏠</span> HOME</a>
        <a class="pill is-active" href="productos.php"><span class="ico">📦</span> PRODUCTOS</a>
        <a class="pill" href="menu.php"><span class="ico">🍽️</span> MENÚ</a>
        <a class="pill" href="pedidos.php"><span class="ico">🧾</span> PEDIDOS</a>
        <?php if (isset($_SESSION['rol_id']) && $_SESSION['rol_id'] == 1) { ?>
          <a class="pill" href="gestion_productos.php">⚙️ GESTIÓN PROD.</a>
          <a class="pill" href="archivosPHP/usuarios.php">REGISTROS <span class="ico">👤</span></a>
        <?php } ?>
      </div>
    </nav>

    <!-- CONTENIDO PRINCIPAL -->
    <main class="content">
      <h2 class="section-title">Productos</h2>
      <p style="text-align: center; margin-bottom: 20px; color: #666;">Haz clic en la imagen de un producto para ver más detalles.</p>

      <div class="products-grid">

        <?php
        if ($resultado_productos && $resultado_productos->num_rows > 0) {
          while ($fila = $resultado_productos->fetch_assoc()) {
            // Preparamos los datos para pasarlos a JavaScript
            $nombre = addslashes($fila['vchNombre']);
            $desc = addslashes($fila['vchDescripcion']);
            $precio = $fila['decPrecioVenta'];
            $stock = $fila['intStock'];

            // Lógica de imagen
            $imgDb = $fila['vchImagen'];
            if (!empty($imgDb) && file_exists("../" . $imgDb)) {
              $imgUrl = "../" . $imgDb;
            } else {
              $imgUrl = "https://placehold.co/600x400/d9cfa8/765433?text=" . urlencode($fila['vchNombre']);
            }
        ?>

            <!-- PRODUCTO INDIVIDUAL -->
            <article class="product">
              <div class="product__img" onclick="abrirModal('<?php echo $nombre; ?>', '<?php echo $desc; ?>', '<?php echo $precio; ?>', '<?php echo $stock; ?>', '<?php echo $imgUrl; ?>')">
                <img src="<?php echo $imgUrl; ?>" alt="<?php echo htmlspecialchars($fila['vchNombre']); ?>">
              </div>

              <ul class="product__text">
                <li><strong><?php echo htmlspecialchars($fila['vchNombre']); ?></strong></li>
                <li><?php echo htmlspecialchars($fila['vchDescripcion']); ?></li>
                <li class="price">$<?php echo number_format($fila['decPrecioVenta'], 2); ?> MXN</li>
              </ul>
            </article>

        <?php
          }
        } else {
          echo "<p>No hay productos disponibles en este momento.</p>";
        }
        ?>

      </div> <!-- .products-grid -->
    </main>
  </div>

  <footer class="footer">
    <p>Universidad Tecnológica de la Huasteca Hidalguense</p>
    <p>&copy; 2025 Cafetería UTHH. Todos los derechos reservados.</p>

    <div class="footer-links">
      <a href="/unicafe/archivosPHP/aviso_privacidad.php">Aviso de Privacidad</a>
      <span class="separator">|</span>
      <a href="/archivosPHP/terminos.php">Terminos y condiciones</a>
      <span class="separator">|</span>
      <a href="/unicafe/archivosHTML/somosUnicafe.html">Sobre nosotros</a>
    </div>
  </footer>
  <button
    id="btn-voz"
    class="voice-btn"
    aria-label="Escuchar el contenido de la página">
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
      style="margin-top: 5px; border-color: #2a9d8f; color: #2a9d8f">
      🌗
    </button>
  </div>

  <!-- ESTRUCTURA DE LA VENTANA MODAL (Oculta por defecto) -->
  <div id="productModal" class="modal">
    <div class="modal-content">
      <span class="close" onclick="cerrarModal()">&times;</span>

      <div class="modal-body">
        <!-- Imagen Grande -->
        <div class="modal-img-container">
          <img id="modalImg" src="" alt="Producto">
        </div>

        <!-- Información -->
        <div class="modal-info">
          <h2 id="modalTitle" class="modal-title">Nombre del Producto</h2>
          <p id="modalStock" class="modal-stock">Stock disponible: 0</p>
          <p id="modalDesc" class="modal-desc">Descripción detallada del producto.</p>
          <div id="modalPrice" class="modal-price">$0.00</div>
        </div>
      </div>
    </div>
  </div>

  <!-- SCRIPT -->
  <script>
    function abrirModal(nombre, descripcion, precio, stock, imgUrl) {
      var modal = document.getElementById("productModal");
      document.getElementById("modalTitle").innerText = nombre;
      document.getElementById("modalDesc").innerText = descripcion;
      document.getElementById("modalPrice").innerText = "$" + parseFloat(precio).toFixed(2) + " MXN";
      document.getElementById("modalStock").innerText = "Disponibles: " + stock + " unidades";
      document.getElementById("modalImg").src = imgUrl;

      modal.style.display = "block";
      document.body.style.overflow = "hidden"; // Bloquear scroll
    }

    function cerrarModal() {
      document.getElementById("productModal").style.display = "none";
      document.body.style.overflow = "auto"; // Reactivar scroll
    }

    window.onclick = function(event) {
      var modal = document.getElementById("productModal");
      if (event.target == modal) {
        cerrarModal();
      }
    }
  </script>

</body>

</html>
<?php
if (isset($conn) && $conn instanceof mysqli) {
  $conn->close();
}