<?php
session_start();
// Si no hay usuario logueado, redirigir al login
if (!isset($_SESSION['usuario'])) {
    header("Location: ../archivosHTML/login.html");
    exit;
}

require_once __DIR__ . '/conexion.php';

// Obtener Rol y Nombre del usuario actual
$rol = isset($_SESSION['rol_id']) ? $_SESSION['rol_id'] : 3; 
$mi_nombre = $_SESSION['usuario']; // El nombre con el que se guardarán/buscarán los pedidos
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Mis Pedidos — Cafetería UTHH</title>
  
  <link rel="stylesheet" href="../archivosCSS/layout.css?v=999.1" />
  <link rel="stylesheet" href="../archivosCSS/pedidos.css?v=999.7" />
  
 
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
  <div class="wrapper">
    
    <?php include 'header.php'; ?>

    <?php include 'barra_navegacion.php'; ?>

    <div class="main-container">
        <div class="card">
            
            <?php if ($rol == 1 || $rol == 2): ?>
                <h2>Historial General de Pedidos</h2>
                
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th class="text-center">ID</th>
                                <th>Cliente</th>
                                <th>Detalle</th>
                                <th>Hora Entrega</th>
                                <th>Total</th>
                                <th class="text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            // Consulta GLOBAL (Sin filtro)
                            $sql = "SELECT * FROM tblhistorial ORDER BY id DESC"; 
                            $result = $conn->query($sql);

                            if ($result && $result->num_rows > 0) {
                                while($row = $result->fetch_assoc()) {
                                    echo "<tr>";
                                    echo "<td class='text-center'>" . $row["id"] . "</td>";
                                    echo "<td><strong>" . htmlspecialchars($row["nombre_cliente"]) . "</strong></td>"; // Resaltamos nombre
                                    echo "<td>" . htmlspecialchars($row["detalle_pedido"]) . "</td>";
                                    echo "<td>" . htmlspecialchars($row["hora_entrega"]) . "</td>";
                                    echo "<td>$" . number_format($row["total"], 2) . "</td>";
                                    echo "<td class='text-center'>";
                                    
                                    if ($rol == 1) {
                                        echo "<a href='eliminar_pedido.php?id=" . $row["id"] . "' class='btn-delete' onclick=\"return confirm('¿Borrar?');\">X</a>";
                                    } else {
                                        echo "<span style='color:#ccc;'>-</span>";
                                    }
                                    
                                    echo "</td>";
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='6' class='text-center'>No hay pedidos registrados en el sistema.</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>

            <?php else: ?>
                <h2>📜 Mi Historial de Compras</h2>
                
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th class="text-center"># Pedido</th>
                                <th>Lo que pediste</th>
                                <th>Hora Entrega</th>
                                <th class="text-center">Artículos</th>
                                <th>Total Pagado</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            // Consulta FILTRADA por el nombre de usuario actual
                            // Usamos prepare() para seguridad
                            $stmt = $conn->prepare("SELECT * FROM tblhistorial WHERE nombre_cliente = ? ORDER BY id DESC");
                            $stmt->bind_param("s", $mi_nombre);
                            $stmt->execute();
                            $result = $stmt->get_result();

                            if ($result && $result->num_rows > 0) {
                                while($row = $result->fetch_assoc()) {
                                    echo "<tr>";
                                    echo "<td class='text-center'>#" . $row["id"] . "</td>";
                                    echo "<td>" . htmlspecialchars($row["detalle_pedido"]) . "</td>";
                                    echo "<td>" . htmlspecialchars($row["hora_entrega"]) . "</td>";
                                    echo "<td class='text-center'>" . $row["cantidad"] . "</td>";
                                    echo "<td style='color:#2A9D8F; font-weight:bold;'>$" . number_format($row["total"], 2) . "</td>";
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='5' class='text-center' style='padding:30px;'>
                                        Aún no has realizado ningún pedido.<br><br>
                                        <a href='menu.php' class='pill' style='background:#c59b42; color:white;'>Ir al Menú</a>
                                      </td></tr>";
                            }
                            $stmt->close();
                            ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>

        </div>
    </div>

  <?php include "footer.php"; ?>

  </div>
</body>
</html>
<?php if(isset($conn)) $conn->close(); ?>