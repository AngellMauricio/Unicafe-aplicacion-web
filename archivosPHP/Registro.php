<?php
require_once __DIR__ . '/conexion.php';
?>
<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Crear Cuenta — Cafetería UTHH</title>

<link rel="stylesheet" href="../archivosCSS/layout.css?v=<?php echo time(); ?>">
  <link rel="stylesheet" href="../archivosCSS/registro.css?v=<?php echo time(); ?>">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body>
  
  <?php include 'header.php'; ?>

  <?php include 'barra_navegacion.php'; ?>


  <main class="main-container">
    
    <div class="card">
      
      <div class="card-header">
        <h2>¡Bienvenido! Crea tu cuenta</h2>
        <p>Llena tus datos para comenzar a pedir.</p>
      </div>

      <form action="procesar_usuario.php?accion=agregar" method="post">
        
        <input type="hidden" name="rol" value="3">

        <div class="form-content">
          
          <div class="left-col" style="display: grid; gap: 20px;">
            
            <div class="input-group">
              <label>Nombre(s)</label>
              <input type="text" name="nombre" placeholder="Ej. Juan" required />
            </div>
            
            <div class="input-group">
              <label>Apellido Paterno</label>
              <input type="text" name="ap" placeholder="Ej. Pérez" required />
            </div>
            
            <div class="input-group">
              <label>Apellido Materno</label>
              <input type="text" name="am" placeholder="Ej. López" required />
            </div>
            
            <div class="input-group">
              <label>Teléfono</label>
              <input type="tel" name="telefono" placeholder="Ej. 771 123 4567" required />
            </div>

          </div>

          <div class="right-col" style="display: grid; gap: 20px;">
            
            <div class="input-group">
              <label>Correo Electrónico</label>
              <input type="email" name="email" placeholder="correo@ejemplo.com" required />
            </div>
            
            <div class="input-group">
              <label>Dirección</label>
              <input type="text" name="direccion" placeholder="Calle, Número, Colonia" required />
            </div>
            
            <div class="input-group">
              <label>Contraseña</label>
              <input type="password" name="pass" placeholder="Crea una contraseña segura" required />
            </div>

            <div class="btn-submit-container">
              <button class="btn-register" type="submit">
                Registrarme
              </button>
            </div>

          </div> </div> </form>

      <div class="form-footer">
        <p>¿Ya tienes cuenta? <a href="iniciar_sesion.php">Inicia sesión aquí</a></p>
      </div>

    </div> </main>

  <?php include 'footer.php'; ?>

</body>
</html>

<?php
if (isset($conn) && $conn instanceof mysqli) {
  $conn->close();
}
?>