<?php
// 1. Incluimos la conexión a la base de datos
// Estamos en archivosHTML, así que subimos un nivel y entramos a archivosPHP
include '../archivosPHP/conexion.php';

// 2. Preparamos la consulta SQL
// IMPORTANTE: Agregamos 'vchImagen' a la selección
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
  <link rel="stylesheet" href="../archivosCSS/productos.css" />
  <link rel="stylesheet" href="../archivosCSS/footer.css" />

</head>

<body>
  <div class="app">

    <!-- TOPBAR -->
    <header class="topbar">
      <div class="topbar__left">
        <span class="avatar" aria-hidden="true">👤</span>
        <a class="login-pill" href="/archivosHTML/login.html">Iniciar Sesión</a>
      </div>
      <h1 class="title">CAFETERIA UTHH</h1>
      <div class="topbar__right"></div>
    </header>

    <!-- NAV -->
    <nav class="nav">
      <div class="nav__wrap">
        <a class="pill" href="../index.html"><span class="ico">🏠</span> HOME</a>
        <a class="pill is-active" href="/archivosHTML/productos.php"><span class="ico">📦</span> PRODUCTOS</a>
        <a class="pill" href="/archivosHTML/menu.html"><span class="ico">🍽️</span> MENÚ</a>
        <a class="pill" href="/archivosHTML/pedidos.html"><span class="ico">🧾</span> PEDIDOS</a>
        <a class="pill" href="/archivosPHP/usuarios.php"><span class="ico">👤</span> REGISTROS</a>
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

            // ==========================================================
            // LÓGICA DE IMAGEN REAL vs PLACEHOLDER
            // ==========================================================
            $imgDb = $fila['vchImagen']; // Ruta guardada en BD (ej: imagenes_productos/foto.jpg)
            
            // Verificamos si hay algo guardado Y si el archivo realmente existe
            // Usamos "../" porque estamos en la carpeta archivosHTML y la carpeta imagenes_productos está fuera
            if (!empty($imgDb) && file_exists("../" . $imgDb)) {
                $imgUrl = "../" . $imgDb; // Usar imagen real
            } else {
                // Usar placeholder si no hay imagen
                $imgUrl = "https://placehold.co/600x400/d9cfa8/765433?text=" . urlencode($fila['vchNombre']);
            }
        ?>

            <!-- PRODUCTO INDIVIDUAL -->
            <article class="product">
              <!-- AL HACER CLIC EN LA IMAGEN: Pasamos la $imgUrl correcta -->
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
    <form action="#contacto.html" method="get">
      <button type="submit" class="btn-contacto">Contáctanos</button>
    </form>
  </footer>

  <!-- ESTRUCTURA DE LA VENTANA MODAL -->
  <div id="productModal" class="modal">
    <div class="modal-content">
      <span class="close" onclick="cerrarModal()">&times;</span>

      <div class="modal-body">
        <!-- Lado Izquierdo: Imagen Grande -->
        <div class="modal-img-container">
          <img id="modalImg" src="" alt="Producto">
        </div>

        <!-- Lado Derecho: Información -->
        <div class="modal-info">
          <h2 id="modalTitle" class="modal-title">Nombre del Producto</h2>
          <p id="modalStock" class="modal-stock">Stock disponible: 0</p>
          <p id="modalDesc" class="modal-desc">Descripción detallada del producto.</p>
          <div id="modalPrice" class="modal-price">$0.00</div>

          <button class="btn-contacto" style="width: 100%; margin-top: auto;">¡Pedir Ahora!</button>
        </div>
      </div>
    </div>
  </div>

  <!-- SCRIPT PARA MANEJAR LA MODAL -->
  <script>
    function abrirModal(nombre, descripcion, precio, stock, imgUrl) {
      var modal = document.getElementById("productModal");
      var mTitle = document.getElementById("modalTitle");
      var mDesc = document.getElementById("modalDesc");
      var mPrice = document.getElementById("modalPrice");
      var mStock = document.getElementById("modalStock");
      var mImg = document.getElementById("modalImg");

      mTitle.innerText = nombre;
      mDesc.innerText = descripcion;
      mPrice.innerText = "$" + parseFloat(precio).toFixed(2) + " MXN";
      mStock.innerText = "Disponibles: " + stock + " unidades";
      mImg.src = imgUrl;

      modal.style.display = "block";
      document.body.style.overflow = "hidden";
    }

    function cerrarModal() {
      var modal = document.getElementById("productModal");
      modal.style.display = "none";
      document.body.style.overflow = "auto";
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
$conn->close();
?>