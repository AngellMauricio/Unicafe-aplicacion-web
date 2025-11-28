<?php
session_start(); // Iniciamos sesión para comprobar si existe, pero NO forzamos a salir

// 1. DETECTAR ESTADO DEL USUARIO
$usuario_logueado = isset($_SESSION['usuario']);
$nombre_usuario = $usuario_logueado ? $_SESSION['usuario'] : '';

// 2. CONEXIÓN A BD
require_once __DIR__ . '/conexion.php'; 

// 3. CONSULTA DE PRODUCTOS
$sql = "SELECT vchNombre, vchDescripcion, intStock, decPrecioVenta, vchImagen 
        FROM tblproductos 
        WHERE intStock > 0"; // Opcional: Solo mostrar lo que tiene stock

$resultado_productos = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Productos – Cafetería UTHH</title>

  <link rel="stylesheet" href="../archivosCSS/layout.css?v=999.1" />
  <link rel="stylesheet" href="../archivosCSS/productos.css?v=999.1" />

  
  <link rel="stylesheet" href="../archivosCSS/ventanaEmergente.css" />
</head>

<body>
  <div class="app">

     <?php include 'header.php'; ?>

    <?php include 'barra_navegacion.php'; ?>

    <main class="content">
      <h2 class="section-title">Nuestros Productos</h2>
      <p style="text-align: center; margin-bottom: 20px; color: #666;">Haz clic en la imagen para ver detalles.</p>

      <div class="products-grid">

        <?php
        if ($resultado_productos && $resultado_productos->num_rows > 0) {
          while ($fila = $resultado_productos->fetch_assoc()) {
            // Preparamos los datos para pasarlos a JavaScript (Modal)
            $nombre = addslashes($fila['vchNombre']);
            $desc = addslashes($fila['vchDescripcion']);
            $precio = $fila['decPrecioVenta'];
            $stock = $fila['intStock'];

            // Lógica de imagen
            $imgDb = $fila['vchImagen'];
            // Ajuste de ruta: Estamos en archivosPHP, así que subimos un nivel con ../
            if (!empty($imgDb) && file_exists("../" . $imgDb)) {
              $imgUrl = "../" . $imgDb;
            } else {
              // Placeholder si no hay imagen
              $imgUrl = "https://placehold.co/600x400/d9cfa8/765433?text=" . urlencode($fila['vchNombre']);
            }
        ?>

            <article class="product">
              <div class="product__img" onclick="abrirModal('<?php echo $nombre; ?>', '<?php echo $desc; ?>', '<?php echo $precio; ?>', '<?php echo $stock; ?>', '<?php echo $imgUrl; ?>')">
                <img src="<?php echo $imgUrl; ?>" alt="<?php echo htmlspecialchars($fila['vchNombre']); ?>">
              </div>

              <ul class="product__text">
                <li><strong><?php echo htmlspecialchars($fila['vchNombre']); ?></strong></li>
                
                <li><?php echo htmlspecialchars(substr($fila['vchDescripcion'], 0, 50)) . '...'; ?></li>
                
                <li class="price">$<?php echo number_format($fila['decPrecioVenta'], 2); ?> MXN</li>
              </ul>
            </article>

        <?php
          }
        } else {
          echo "<p style='text-align:center; width:100%;'>No hay productos disponibles en este momento.</p>";
        }
        ?>

      </div> </main>
  </div>

  <?php include "footer.php"; ?>

  <div id="productModal" class="modal">
    <div class="modal-content">
      <span class="close" onclick="cerrarModal()">&times;</span>

      <div class="modal-body">
        <div class="modal-img-container">
          <img id="modalImg" src="" alt="Producto">
        </div>

        <div class="modal-info">
          <h2 id="modalTitle" class="modal-title">Nombre del Producto</h2>
          <p id="modalStock" class="modal-stock">Stock disponible: 0</p>
          <p id="modalDesc" class="modal-desc">Descripción detallada del producto.</p>
          <div id="modalPrice" class="modal-price">$0.00</div>
        </div>
      </div>
    </div>
  </div>

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
?>