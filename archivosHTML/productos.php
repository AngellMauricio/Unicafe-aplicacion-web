<?php
// 1. Incluimos la conexión a la base de datos
include 'conexion.php';

// 2. Preparamos la consulta SQL para obtener todos los productos
//    USANDO LOS NOMBRES DE COLUMNA DE TU TABLA REAL
$sql = "SELECT vchNombre, vchDescripcion, intStock, decPrecioVenta 
        FROM tblproductos";

$resultado_productos = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8" /> <!-- Corregido: era utf-g -->
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Productos – Cafetería UTHH</title>
  <link rel="stylesheet" href="/archivosCSS/productos.css" />
  <link rel="stylesheet" href="/archivosCSS/footer.css" />
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

    <!-- NAV (Rutas corregidas para estar en /archivosPHP/) -->
    <nav class="nav">
      <div class="nav__wrap">
        <a class="pill" href="../index.html"><span class="ico">🏠</span> HOME</a>
        <a class="pill is-active" href="/archivosPHP/productos.php"><span class="ico">📦</span> PRODUCTOS</a>
        <a class="pill" href="/archivosHTML/menu.html"><span class="ico">🍽️</span> MENÚ</a>
        <a class="pill" href="/archivosHTML/pedidos.html"><span class="ico">🧾</span> PEDIDOS</a>
        <a class="pill" href="/archivosPHP/usuarios.php"><span class="ico">👤</span> REGISTROS</a>
      </div>
    </nav>
    
    <!-- CONTENIDO -->
    <main class="content">
      <h2 class="section-title">Productos</h2>

      <div class="products-grid">

        <?php
        // 3. Iniciamos el bucle.
        if ($resultado_productos && $resultado_productos->num_rows > 0) {
          
          while($fila = $resultado_productos->fetch_assoc()) {
            
            // 5. Generamos el HTML con los datos de TU tabla
        ?>
        
        <!-- INICIO DE LA PLANTILLA DE PRODUCTO -->
        <article class="product">
          <div class="product__img">
            <!-- CAMBIO: Usamos un marcador de posición ya que tu tabla no tiene URL de imagen -->
            <img src="https://placehold.co/600x400/d9cfa8/765433?text=<?php echo htmlspecialchars($fila['vchNombre']); ?>" alt="<?php echo htmlspecialchars($fila['vchNombre']); ?>">
          </div>
          <ul class="product__text">
            <!-- CAMBIO: Usamos vchNombre -->
            <li><strong><?php echo htmlspecialchars($fila['vchNombre']); ?></strong></li>
            <!-- CAMBIO: Usamos vchDescripcion -->
            <li><?php echo htmlspecialchars($fila['vchDescripcion']); ?></li>
            <!-- CAMBIO: Usamos intStock -->
            <li>Stock disponible: <?php echo $fila['intStock']; ?></li>
            <!-- CAMBIO: Usamos decPrecioVenta -->
            <li class="price">$<?php echo number_format($fila['decPrecioVenta'], 2); ?> MXN</li>
          </ul>
        </article>
        <!-- FIN DE LA PLANTILLA DE PRODUCTO -->
        
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
  
</body>
</html>

<?php
// 8. Cerramos la conexión a la base de datos
$conn->close();
?>