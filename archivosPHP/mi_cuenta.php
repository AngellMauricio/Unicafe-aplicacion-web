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

// 2. LÓGICA PARA ACTUALIZAR DATOS
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre']);
    $ap     = trim($_POST['ap']);
    $am     = trim($_POST['am']);
    $tel    = trim($_POST['telefono']);
    $dir    = trim($_POST['direccion']);
    $pass   = trim($_POST['pass']);

    if (empty($nombre) || empty($ap) || empty($tel)) {
        $mensaje = "Por favor completa los campos obligatorios.";
        $tipo_mensaje = "error";
    } else {
        if (!empty($pass)) {
            $pass_hash = password_hash($pass, PASSWORD_DEFAULT);
            $sql = "UPDATE tblusuario SET vchNombres=?, vchApaterno=?, vchAmaterno=?, vchTelefono=?, vchDireccion=?, vchPassword=? WHERE intIdUsuario=?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssssssi", $nombre, $ap, $am, $tel, $dir, $pass_hash, $id_usuario);
        } else {
            $sql = "UPDATE tblusuario SET vchNombres=?, vchApaterno=?, vchAmaterno=?, vchTelefono=?, vchDireccion=? WHERE intIdUsuario=?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssssi", $nombre, $ap, $am, $tel, $dir, $id_usuario);
        }

        if ($stmt->execute()) {
            $mensaje = "¡Tus datos han sido actualizados correctamente!";
            $tipo_mensaje = "success";
            $_SESSION['usuario'] = $nombre; 
        } else {
            $mensaje = "Error al actualizar: " . $conn->error;
            $tipo_mensaje = "error";
        }
        $stmt->close();
    }
}

// 3. LÓGICA PARA LEER DATOS
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

  <link rel="stylesheet" href="../archivosCSS/layout.css" />
  <link rel="stylesheet" href="../archivosCSS/registro.css?v=<?php echo time(); ?>">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  
  <style>
    /* Estilos para las alertas (Mensajes de éxito/error) */
    .alert {
        padding: 15px;
        margin-bottom: 20px;
        border-radius: 5px;
        text-align: center;
        font-weight: bold;
    }
    .alert.success { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
    .alert.error { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
    
    /* Estilo para el input de solo lectura (correo) */
    input[readonly] {
        background-color: #e9ecef !important; /* Forzamos el gris */
        cursor: not-allowed;
        color: #6c757d;
    }
  </style>
</head>
<body>

  <?php include 'header.php'; ?>
  <?php include 'barra_navegacion.php'; ?>

  <main class="main-container">
    
    <div class="card">
      
      <div class="card-header">
        <h2>Mis Datos Personales</h2>
        <p>Actualiza tu información o cambia tu contraseña.</p>
      </div>

      <?php if ($mensaje): ?>
         <div class="alert <?php echo $tipo_mensaje; ?>">
             <?php echo $mensaje; ?>
         </div>
      <?php endif; ?>

      <form action="mi_cuenta.php" method="post">
        
        <div class="form-content">
          
          <div class="left-col" style="display: grid; gap: 20px;">
             
             <div class="input-group">
                 <label>Nombre(s)</label>
                 <input type="text" name="nombre" value="<?php echo htmlspecialchars($usuario['vchNombres']); ?>" required />
             </div>

             <div class="input-group">
                 <label>Apellido Paterno</label>
                 <input type="text" name="ap" value="<?php echo htmlspecialchars($usuario['vchApaterno']); ?>" required />
             </div>

             <div class="input-group">
                 <label>Apellido Materno</label>
                 <input type="text" name="am" value="<?php echo htmlspecialchars($usuario['vchAmaterno']); ?>" required />
             </div>

             <div class="input-group">
                 <label>Teléfono</label>
                 <input type="tel" name="telefono" value="<?php echo htmlspecialchars($usuario['vchTelefono']); ?>" required />
             </div>

          </div>

          <div class="right-col" style="display: grid; gap: 20px;">
             
             <div class="input-group">
                 <label>Correo (Usuario)</label>
                 <input type="email" value="<?php echo htmlspecialchars($usuario['vchCorreo']); ?>" readonly title="El correo no se puede modificar" />
             </div>

             <div class="input-group">
                 <label>Dirección</label>
                 <input type="text" name="direccion" value="<?php echo htmlspecialchars($usuario['vchDireccion']); ?>" required />
             </div>

             <div class="input-group">
                 <label>Nueva Contraseña</label>
                 <input type="password" name="pass" placeholder="(Dejar vacío para no cambiar)" />
             </div>
             
             <div class="btn-submit-container">
                 <button class="btn-register" type="submit">
                    Guardar Cambios
                 </button>
             </div>

          </div> </div> </form>

    </div> </main>

  <?php include 'footer.php'; ?>

</body>
</html>
<?php
if (isset($conn) && $conn instanceof mysqli) {
  $conn->close();
}
?>