<?php
session_start();
// Si no hay usuario logueado, redirigir al login
if (!isset($_SESSION['usuario'])) {
    header("Location: ../archivosHTML/login.html");
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Pedidos — Cafetería UTHH</title>
  
  <!-- IMPORTANTE: El truco '?v=<?php echo time(); ?>' fuerza al navegador a cargar los estilos nuevos -->
  
  <!-- Estilos generales -->
  <link rel="stylesheet" href="../archivosCSS/home.css?v=<?php echo time(); ?>" />
  
  <!-- Estilos específicos de esta página (LA CLAVE DEL DISEÑO) -->
  <link rel="stylesheet" href="../archivosCSS/pedidos.css?v=<?php echo time(); ?>" />
  
  <!-- Estilos de menús -->
  <link rel="stylesheet" href="../archivosCSS/menu_desplegable.css?v=<?php echo time(); ?>" />
  
  <!-- Tu footer existente -->
  <link rel="stylesheet" href="../archivosCSS/footer.css?v=<?php echo time(); ?>" />
  
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
  <div class="wrapper">
    
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
        <a class="pill" href="../index.php"><span class="ico">🏠</span> HOME</a>
        <a class="pill" href="productos.php"><span class="ico">📦</span> PRODUCTOS</a>
        <a class="pill" href="menu.php"><span class="ico">🍽️</span> MENÚ</a>
        <a class="pill is-active" href="pedidos.php"><span class="ico">🧾</span> PEDIDOS</a>
        <a class="pill" href="usuarios.php"><span class="ico">👤</span> REGISTROS</a>
      </div>
    </nav>

    <!-- Contenedor Principal -->
    <div class="main-container">
        <div class="card">
            <h2>Historial de Pedidos</h2>
            
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th class="text-center">ID</th>
                            <th>Nombre</th>
                            <th>Lista de Pedido</th>
                            <th>Hora de la entrega</th>
                            <th class="text-center">Cantidad</th>
                            <th>Pago Total</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    
                    <tbody>
                        <?php
                        include 'conexion.php'; 

                        $sql = "SELECT * FROM tblhistorial";
                        
                        if ($conn) {
                            $result = $conn->query($sql);

                            if ($result && $result->num_rows > 0) {
                                while($row = $result->fetch_assoc()) {
                                    echo "<tr>";
                                    echo "<td class='text-center'>" . $row["id"] . "</td>";
                                    echo "<td>" . $row["nombre_cliente"] . "</td>";
                                    echo "<td>" . $row["detalle_pedido"] . "</td>";
                                    echo "<td>" . $row["hora_entrega"] . "</td>";
                                    echo "<td class='text-center'>" . $row["cantidad"] . "</td>";
                                    echo "<td>$" . $row["total"] . "</td>";
                                    echo "<td class='text-center'>";
                                    // Botón de eliminar estilizado
                                    echo "<a href='eliminar_pedido.php?id=" . $row["id"] . "' class='btn-delete' onclick=\"return confirm('¿Estás seguro de eliminar este pedido?');\">Eliminar</a>";
                                    echo "</td>";
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='7' class='text-center' style='padding:20px;'>No hay pedidos registrados</td></tr>";
                            }
                            $conn->close();
                        } else {
                            echo "<tr><td colspan='7' class='text-center' style='color:red;'>Error de conexión a la base de datos</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

  <!-- Footer existente -->
  <footer class="footer">
    <p>Universidad Tecnológica de la Huasteca Hidalguense</p>
    <p>&copy; 2025 Cafetería UTHH. Todos los derechos reservados.</p>

    <div class="footer-links">
      <a href="aviso_privacidad.php">Aviso de Privacidad</a>
      <span class="separator">|</span>
      <a href="terminos.php">Términos y condiciones</a>
      <span class="separator">|</span>
      <a href="../archivosHTML/somosUnicafe.html">Sobre nosotros</a>
    </div>
  </footer>

  </div>
</body>
</html>