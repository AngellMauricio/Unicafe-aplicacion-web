<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
  header("Location: ../archivosHTML/login.html");
  exit();
}

if ($_SESSION['rol_id'] != 1) {
  echo "<script>alert('Acceso denegado: Solo administradores.'); window.location.href='../index.php';</script>";
  exit();
}

require_once __DIR__ . '/conexion.php'; 

// --- LÓGICA DE MENSAJES ---
$mensaje = "";
$clase_alerta = "";

if (isset($_GET['mensaje'])) {
    switch ($_GET['mensaje']) {
        case 'agregado':
            $mensaje = "✅ ¡Usuario registrado exitosamente!";
            $clase_alerta = "success";
            break;
        case 'actualizado':
            $mensaje = "✅ ¡Datos del usuario actualizados!";
            $clase_alerta = "success";
            break;
        case 'eliminado':
            $mensaje = "🗑️ ¡Usuario eliminado exitosamente!";
            $clase_alerta = "success";
            break;
        case 'error':
            $mensaje = "❌ Ocurrió un error al procesar la solicitud.";
            $clase_alerta = "error";
            break;
    }
}

// --- LÓGICA PARA ACTUALIZAR ---
$modo_edicion = false;
$id_usuario_editar = 0;
$nombre_val = ""; $ap_val = ""; $am_val = ""; $tel_val = ""; 
$email_val = ""; $dir_val = ""; $rol_val = "";
$accion_form = "procesar_usuario.php?accion=agregar"; 

if (isset($_GET['accion']) && $_GET['accion'] === 'editar' && isset($_GET['id'])) {
  $modo_edicion = true;
  $id_usuario_editar = (int)$_GET['id'];
  $accion_form = "procesar_usuario.php?accion=actualizar&id=" . $id_usuario_editar;

  $stmt_editar = $conn->prepare("SELECT vchNombres, vchApaterno, vchAmaterno, vchTelefono, vchCorreo, vchDireccion, intIdRol FROM tblusuario WHERE intIdUsuario = ?");
  $stmt_editar->bind_param("i", $id_usuario_editar);
  $stmt_editar->execute();
  $resultado_editar = $stmt_editar->get_result();

  if ($resultado_editar && $resultado_editar->num_rows > 0) {
    $u = $resultado_editar->fetch_assoc();
    $nombre_val = htmlspecialchars($u['vchNombres'] ?? '');
    $ap_val     = htmlspecialchars($u['vchApaterno'] ?? '');
    $am_val     = htmlspecialchars($u['vchAmaterno'] ?? '');
    $tel_val    = htmlspecialchars($u['vchTelefono'] ?? '');
    $email_val  = htmlspecialchars($u['vchCorreo']    ?? '');
    $dir_val    = htmlspecialchars($u['vchDireccion'] ?? '');
    $rol_val    = (int)($u['intIdRol'] ?? 0);
  }
  $stmt_editar->close();
}

// --- LÓGICA PARA LEER ---
$sql_select = "SELECT U.intIdUsuario, U.vchNombres, U.vchApaterno, U.vchAmaterno, U.vchCorreo, R.vchRol 
               FROM tblusuario U 
               JOIN tblroles R ON U.intIdRol = R.intIdRol 
               ORDER BY U.intIdUsuario DESC";
$resultado_lista = $conn->query($sql_select);
?>
<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title><?php echo $modo_edicion ? 'Editar' : 'Registrar'; ?> Usuario — Cafetería UTHH</title>

  <link rel="stylesheet" href="../archivosCSS/registro.css?v=<?php echo time(); ?>">
  <link rel="stylesheet" href="../archivosCSS/usuarios.css?v=<?php echo time(); ?>">
  <link rel="stylesheet" href="../archivosCSS/layout.css?v=<?php echo time(); ?>" />
  
  <style>
    .alert {
        padding: 15px; margin-bottom: 20px; border-radius: 8px;
        text-align: center; font-weight: bold; font-size: 1rem;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1); max-width: 1100px;
        margin-left: auto; margin-right: auto;
    }
    .alert.success { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
    .alert.error { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
  </style>
</head>

<body>
  <div class="app">
    <?php include 'header.php'; ?>
    <?php include 'barra_navegacion.php'; ?>

    <main class="content">
      
      <?php if (!empty($mensaje)): ?>
          <div class="alert <?php echo $clase_alerta; ?>">
              <?php echo $mensaje; ?>
          </div>
          <script>
              setTimeout(function() {
                  const alert = document.querySelector('.alert');
                  if(alert) alert.style.display = 'none';
              }, 4000);
          </script>
      <?php endif; ?>

      <div class="form-container">
        <h2><?php echo $modo_edicion ? 'Editando Usuario' : 'Registrar Nuevo Usuario'; ?></h2>

        <form action="<?php echo htmlspecialchars($accion_form); ?>" method="post" autocomplete="off">
          <div class="form-grid">
            <div class="form-column">
              
              <div class="form-row">
                  <label>Nombre</label>
                  <input type="text" name="nombre" value="<?php echo $nombre_val; ?>" required 
                         oninput="this.value = this.value.replace(/[^a-zA-ZñÑáéíóúÁÉÍÓÚ\s]/g, '')" />
              </div>
              
              <div class="form-row">
                  <label>Apellido paterno</label>
                  <input type="text" name="ap" value="<?php echo $ap_val; ?>" required 
                         oninput="this.value = this.value.replace(/[^a-zA-ZñÑáéíóúÁÉÍÓÚ\s]/g, '')" />
              </div>
              
              <div class="form-row">
                  <label>Apellido materno</label>
                  <input type="text" name="am" value="<?php echo $am_val; ?>" required 
                         oninput="this.value = this.value.replace(/[^a-zA-ZñÑáéíóúÁÉÍÓÚ\s]/g, '')" />
              </div>
              
              <div class="form-row">
                  <label>Teléfono</label>
                  <input type="tel" name="telefono" value="<?php echo $tel_val; ?>" required 
                         oninput="this.value = this.value.replace(/[^0-9]/g, '')" maxlength="10"/>
              </div>

              <div class="actions" style="display:flex; gap:10px; margin-top:10px;">
                <button class="btn-action btn-add" type="submit">
                  <?php echo $modo_edicion ? 'Guardar Cambios' : 'Agregar Usuario'; ?>
                </button>
                <?php if ($modo_edicion): ?>
                  <a href="usuarios.php" class="form-cancel-btn">Cancelar Edición</a>
                <?php endif; ?>
              </div>
            </div>

            <div class="form-column">
              <div class="form-row">
                  <label>Correo</label>
                  <input type="email" name="email" value="<?php echo $email_val; ?>" required autocomplete="new-email" />
              </div>
              
              <div class="form-row">
                  <label>Dirección</label>
                  <input type="text" name="direccion" value="<?php echo $dir_val; ?>" required />
              </div>

              <div class="form-row">
                <label>Contraseña <?php echo $modo_edicion ? '(dejar en blanco para no cambiar)' : ''; ?></label>
                <input type="password" name="pass" <?php echo $modo_edicion ? '' : 'required'; ?> autocomplete="new-password" />
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

      <div class="list-container">
        <h2>Listado de Usuarios</h2>
        <div style="overflow-x:auto;">
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
                }
                ?>
              </tbody>
            </table>
        </div>
      </div>
    </main>
  </div>
</body>
</html>
<?php
if (isset($conn) && $conn instanceof mysqli) {
  $conn->close();
}
?>