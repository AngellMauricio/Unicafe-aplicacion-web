<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
  // Si no hay sesión, mándalo al login
  header("Location: ../archivosHTML/login.html");
  exit();
}


if ($_SESSION['rol_id'] != 1) {
  // Si está logueado pero NO es admin, mándalo al inicio o muestra error
  echo "<script>alert('Acceso denegado: Solo administradores.'); window.location.href='../index.php';</script>";
  exit();
}

// TODOS LOS PHP ESTÁN EN LA MISMA CARPETA:
require_once __DIR__ . '/conexion.php'; // ← clave

// --- LÓGICA PARA ACTUALIZAR (EDITAR) ---
$modo_edicion = false;
$id_usuario_editar = 0;
$nombre_val = "";
$ap_val = "";
$am_val = "";
$tel_val = "";
$email_val = "";
$dir_val = "";
$rol_val = "";
$accion_form = "procesar_usuario.php?accion=agregar"; // Acción por defecto: agregar

// Si recibimos ?accion=editar...
if (isset($_GET['accion']) && $_GET['accion'] === 'editar' && isset($_GET['id'])) {
    $modo_edicion = true;
    $id_usuario_editar = (int)$_GET['id'];
    $accion_form = "procesar_usuario.php?accion=actualizar&id=" . $id_usuario_editar;

    $stmt_editar = $conn->prepare("
        SELECT vchNombres, vchApaterno, vchAmaterno, vchTelefono, vchCorreo, vchDireccion, intIdRol
        FROM tblusuario
        WHERE intIdUsuario = ?
    ");
    $stmt_editar->bind_param("i", $id_usuario_editar);
    $stmt_editar->execute();
    $resultado_editar = $stmt_editar->get_result();

    if ($resultado_editar && $resultado_editar->num_rows > 0) {
        $u = $resultado_editar->fetch_assoc();
        $nombre_val = htmlspecialchars($u['vchNombres'] ?? '');
        $ap_val     = htmlspecialchars($u['vchApaterno'] ?? '');
        $am_val     = htmlspecialchars($u['vchAmaterno'] ?? '');
        $tel_val    = htmlspecialchars($u['vchTelefono'] ?? '');
        $email_val  = htmlspecialchars($u['vchCorreo']   ?? '');
        $dir_val    = htmlspecialchars($u['vchDireccion']?? '');
        $rol_val    = (int)($u['intIdRol'] ?? 0);
    }
    $stmt_editar->close();
}
// --- FIN LÓGICA ACTUALIZAR ---

// --- LÓGICA PARA LEER (LISTADO) ---
$sql_select = "
SELECT U.intIdUsuario, U.vchNombres, U.vchApaterno, U.vchAmaterno, U.vchCorreo, R.vchRol
FROM tblusuario U
JOIN tblroles R ON U.intIdRol = R.intIdRol
ORDER BY U.intIdUsuario DESC
";
$resultado_lista = $conn->query($sql_select);
// --- FIN LÓGICA LEER ---
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title><?php echo $modo_edicion ? 'Editar' : 'Registrar'; ?> Usuario — Cafetería UTHH</title>

  <!-- Estás en /unicafe/archivosPHP/  → sube un nivel para llegar a /unicafe/archivosCSS/ -->
  <link rel="stylesheet" href="../archivosCSS/registro.css">
  <link rel="stylesheet" href="../archivosCSS/usuarios.css">
  <link rel="stylesheet" href="../archivosCSS/footer.css" />
</head>
<body>
  <div class="app">
    <header class="topbar">
      <div class="topbar__left">
        <span class="avatar">👤</span>
        <!-- login.html está en /unicafe/archivosHTML/ → desde archivosPHP/ sube un nivel -->
        <a class="login-pill" href="../login.html">Iniciar Sesión</a>
      </div>
      <h1 class="title">CAFETERIA UTHH</h1>
    </header>

    <nav class="nav">
      <div class="nav__wrap">
        <!-- Desde /archivosPHP/ para ir a /archivosHTML/ usa ../ -->
        <a class="pill" href="../index.html"><span class="ico">🏠</span> HOME</a>
        <a class="pill" href="/archivosPHP/productos.php"><span class="ico">📦</span> PRODUCTOS</a>
        <a class="pill" href="../archivosHTML/menu.html"><span class="ico">🍽️</span> MENÚ</a>
        <a class="pill" href="../archivosHTML/pedidos.html"><span class="ico">🧾</span> PEDIDOS</a>
        <!-- Esta página -->
        <?php if (isset($_SESSION['rol_id']) && $_SESSION['rol_id'] == 1) { ?>
                    <a class="pill is-active" href="gestion_productos.php">⚙️ GESTIÓN PROD.</a>
                    <a class="pill" href="archivosPHP/usuarios.php">REGISTROS <span class="ico">👤</span></a>
                <?php } ?>
      </div>
    </nav>

    <main class="content">
      <!-- CONTENEDOR 1: FORMULARIO -->
      <div class="form-container">
        <h2><?php echo $modo_edicion ? 'Editando Usuario' : 'Registrar Nuevo Usuario'; ?></h2>

        <form action="<?php echo htmlspecialchars($accion_form); ?>" method="post">
          <div class="form-grid">
            <!-- COLUMNA 1 -->
            <div class="form-column">
              <div class="form-row"><label>Nombre</label><input type="text" name="nombre" value="<?php echo $nombre_val; ?>" required /></div>
              <div class="form-row"><label>Apellido paterno</label><input type="text" name="ap" value="<?php echo $ap_val; ?>" required /></div>
              <div class="form-row"><label>Apellido materno</label><input type="text" name="am" value="<?php echo $am_val; ?>" required /></div>
              <div class="form-row"><label>Teléfono</label><input type="tel" name="telefono" value="<?php echo $tel_val; ?>" required /></div>

              <div class="actions" style="display:flex; gap:10px; margin-top:10px;">
                <button class="btn-action btn-add" type="submit">
                  <?php echo $modo_edicion ? 'Guardar Cambios' : 'Agregar Usuario'; ?>
                </button>
                <?php if ($modo_edicion): ?>
                  <a href="usuarios.php" class="form-cancel-btn">Cancelar Edición</a>
                <?php endif; ?>
              </div>
            </div>

            <!-- COLUMNA 2 -->
            <div class="form-column">
              <div class="form-row"><label>Correo</label><input type="email" name="email" value="<?php echo $email_val; ?>" required /></div>
              <div class="form-row"><label>Dirección</label><input type="text" name="direccion" value="<?php echo $dir_val; ?>" required /></div>

              <div class="form-row">
                <label>Contraseña <?php echo $modo_edicion ? '(dejar en blanco para no cambiar)' : ''; ?></label>
                <input type="password" name="pass" <?php echo $modo_edicion ? '' : 'required'; ?> />
              </div>

              <div class="form-row">
                <label>Tipo de usuario</label>
                <select name="rol" required>
                  <option value="">Seleccionar...</option>
                  <option value="1" <?php echo ($rol_val == 1) ? 'selected' : ''; ?>>Administrador</option>
                  <option value="2" <?php echo ($rol_val == 2) ? 'selected' : ''; ?>>Empleado</option>
                  <option value="3" <?php echo ($rol_val == 3) ? 'selected' : ''; ?>>Cliente</option>
                </select>
              </div>
            </div>
          </div>
        </form>
      </div>

      <!-- CONTENEDOR 2: LISTA -->
      <div class="list-container">
        <h2>Listado de Usuarios</h2>
        <table class="user-table">
          <thead>
            <tr>
              <th>ID</th>
              <th>Nombre Completo</th>
              <th>Correo</th>
              <th>Rol</th>
              <th>Acciones</th>
            </tr>
          </thead>
          <tbody>
            <?php
            if ($resultado_lista && $resultado_lista->num_rows > 0) {
              while ($fila = $resultado_lista->fetch_assoc()) {
                $id  = (int)$fila['intIdUsuario'];
                $nom = htmlspecialchars(($fila['vchNombres'] ?? '') . ' ' . ($fila['vchApaterno'] ?? ''));
                $cor = htmlspecialchars($fila['vchCorreo'] ?? '');
                $rol = htmlspecialchars($fila['vchRol'] ?? '');
                echo "<tr>";
                echo "<td>{$id}</td>";
                echo "<td>{$nom}</td>";
                echo "<td>{$cor}</td>";
                echo "<td>{$rol}</td>";
                echo "<td class='action-links'>";
                echo "<a href='usuarios.php?accion=editar&id={$id}' class='edit-link'>Editar</a>";
                echo "<a href='procesar_usuario.php?accion=eliminar&id={$id}' class='delete-link' onclick=\"return confirm('¿Estás seguro de que quieres eliminar a este usuario?');\">Eliminar</a>";
                echo "</td>";
                echo "</tr>";
              }
            } else {
              echo "<tr><td colspan='5'>No hay usuarios registrados.</td></tr>";
              if (!$resultado_lista) {
                echo "<tr><td colspan='5'>Error en la consulta: " . htmlspecialchars($conn->error) . "</td></tr>";
              }
            }
            ?>
          </tbody>
        </table>
      </div>
    </main>
  </div>

  <footer class="footer">
    <p>Universidad Tecnológica de la Huasteca Hidalguense</p>
    <p>&copy; 2025 Cafetería UTHH. Todos los derechos reservados.</p>
    <form action="../archivosHTML/contacto.html" method="get">
      <button type="submit" class="btn-contacto">Contáctanos</button>
    </form>
  </footer>
</body>
</html>
<?php
if (isset($conn) && $conn instanceof mysqli) {
  $conn->close();
}
