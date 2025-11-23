<?php
session_start();
require_once __DIR__ . '/conexion.php';

// 1. SEGURIDAD: Verificar si el usuario está logueado
if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../archivosHTML/login.html");
    exit();
}

$id_usuario = $_SESSION['usuario_id'];
$mensaje = "";
$tipo_mensaje = ""; // 'success' o 'error'

// 2. LÓGICA PARA ACTUALIZAR DATOS (Cuando se envía el formulario)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre']);
    $ap     = trim($_POST['ap']);
    $am     = trim($_POST['am']);
    $tel    = trim($_POST['telefono']);
    $dir    = trim($_POST['direccion']);
    $pass   = trim($_POST['pass']);

    // Validamos que no estén vacíos los obligatorios
    if (empty($nombre) || empty($ap) || empty($tel)) {
        $mensaje = "Por favor completa los campos obligatorios.";
        $tipo_mensaje = "error";
    } else {
        // Construimos la consulta SQL según si se cambió o no la contraseña
        if (!empty($pass)) {
            // Si escribió algo, actualizamos TODO incluyendo password hasheado
            $pass_hash = password_hash($pass, PASSWORD_DEFAULT);
            $sql = "UPDATE tblusuario SET vchNombres=?, vchApaterno=?, vchAmaterno=?, vchTelefono=?, vchDireccion=?, vchPassword=? WHERE intIdUsuario=?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssssssi", $nombre, $ap, $am, $tel, $dir, $pass_hash, $id_usuario);
        } else {
            // Si NO escribió password, actualizamos todo MENOS el password
            $sql = "UPDATE tblusuario SET vchNombres=?, vchApaterno=?, vchAmaterno=?, vchTelefono=?, vchDireccion=? WHERE intIdUsuario=?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssssi", $nombre, $ap, $am, $tel, $dir, $id_usuario);
        }

        if ($stmt->execute()) {
            $mensaje = "¡Tus datos han sido actualizados correctamente!";
            $tipo_mensaje = "success";
            
            // Actualizamos el nombre en la sesión por si lo cambió
            $_SESSION['usuario'] = $nombre; 
        } else {
            $mensaje = "Error al actualizar: " . $conn->error;
            $tipo_mensaje = "error";
        }
        $stmt->close();
    }
}

// 3. LÓGICA PARA LEER DATOS (Cargar la información actual del usuario)
$sql_leer = "SELECT vchNombres, vchApaterno, vchAmaterno, vchTelefono, vchCorreo, vchDireccion FROM tblusuario WHERE intIdUsuario = ?";
$stmt_leer = $conn->prepare($sql_leer);
$stmt_leer->bind_param("i", $id_usuario);
$stmt_leer->execute();
$res = $stmt_leer->get_result();
$usuario = $res->fetch_assoc();
$stmt_leer->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Mi Cuenta — Cafetería UTHH</title>

  <link rel="stylesheet" href="../archivosCSS/registro.css">
  <link rel="stylesheet" href="../archivosCSS/accesibilidad.css" />
  <link rel="stylesheet" href="../archivosCSS/footer.css" />
  
  <style>
    .alert {
        padding: 15px;
        margin-bottom: 20px;
        border-radius: 5px;
        text-align: center;
        font-weight: bold;
    }
    .alert.success { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
    .alert.error { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
    
    /* Indica que el correo no se puede editar, cuidando la seguridad de la pagina */
    input[readonly] {
        background-color: #e9ecef;
        cursor: not-allowed;
        color: #6c757d;
    }
  </style>
</head>
<body>
  <div class="app">
    <header class="topbar">
      <div class="topbar__left">
        <span class="avatar">👤</span>
        <span style="font-weight:bold; font-size:0.9rem;">Hola, <?php echo htmlspecialchars($_SESSION['usuario']); ?></span>
      </div>
      <h1 class="title">CAFETERIA UTHH</h1>
      
      <div style="margin-left:auto;">
          <a href="../archivosPHP/logout.php" class="login-pill" style="background:#f8d7da; color:#721c24; text-decoration:none; border:none;">Cerrar Sesión</a>
      </div>
    </header>

    <nav class="nav">
      <div class="nav__wrap">
        <a class="pill" href="../archivosPHP/index.php"><span class="ico">🏠</span> HOME</a>
        <a class="pill" href="../archivosPHP/productos.php"><span class="ico">📦</span> PRODUCTOS</a>
        <a class="pill" href="../archivosPHP/menu.php"><span class="ico">🍽️</span> MENÚ</a>
        <a class="pill is-active" href="mi_cuenta.php"><span class="ico">⚙️</span> MI CUENTA</a>
        <?php if (isset($_SESSION['rol_id']) && $_SESSION['rol_id'] == 1) { ?>
                    <a class="pill" href="gestion_productos.php">⚙️ GESTIÓN PROD.</a>
                    <a class="pill" href="usuarios.php">REGISTROS <span class="ico">👤</span></a>
                <?php } ?>
      </div>
    </nav>

    <main class="content">
      <div class="form-container">
        <h2 style="text-align:center; color: var(--brown-b);">Mis Datos Personales</h2>
        <p style="text-align:center; margin-bottom:20px; color:#666;">Actualiza tu información o cambia tu contraseña.</p>

        <?php if ($mensaje): ?>
            <div class="alert <?php echo $tipo_mensaje; ?>">
                <?php echo $mensaje; ?>
            </div>
        <?php endif; ?>

        <form action="mi_cuenta.php" method="post">
          <div class="form-grid">
            <div class="form-column">
              <div class="form-row">
                  <label>Nombre(s)</label>
                  <input type="text" name="nombre" value="<?php echo htmlspecialchars($usuario['vchNombres']); ?>" required />
              </div>
              <div class="form-row">
                  <label>Apellido Paterno</label>
                  <input type="text" name="ap" value="<?php echo htmlspecialchars($usuario['vchApaterno']); ?>" required />
              </div>
              <div class="form-row">
                  <label>Apellido Materno</label>
                  <input type="text" name="am" value="<?php echo htmlspecialchars($usuario['vchAmaterno']); ?>" required />
              </div>
              <div class="form-row">
                  <label>Teléfono</label>
                  <input type="tel" name="telefono" value="<?php echo htmlspecialchars($usuario['vchTelefono']); ?>" required />
              </div>
            </div>

            <div class="form-column">
              <div class="form-row">
                  <label>Correo (Usuario)</label>
                  <input type="email" value="<?php echo htmlspecialchars($usuario['vchCorreo']); ?>" readonly title="El correo no se puede modificar por seguridad" />
              </div>
              <div class="form-row">
                  <label>Dirección</label>
                  <input type="text" name="direccion" value="<?php echo htmlspecialchars($usuario['vchDireccion']); ?>" required />
              </div>
              
              <hr style="border: 0; border-top: 1px solid #ccc; margin: 15px 0;">
              
              <div class="form-row">
                  <label>Nueva Contraseña</label>
                  <input type="password" name="pass" placeholder="(Dejar vacío para no cambiar)" />
              </div>
              <p style="font-size: 0.8rem; color: #666; margin-top:-10px; margin-left: 150px;">
                  * Si no quieres cambiar tu contraseña, deja este campo en blanco.
              </p>

              <div class="actions">
                <button class="btn-action btn-add" type="submit">
                  Guardar Cambios
                </button>
              </div>
            </div>
          </div>
        </form>

      </div>
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
</body>
</html>
<?php
if (isset($conn) && $conn instanceof mysqli) {
  $conn->close();
}
?>