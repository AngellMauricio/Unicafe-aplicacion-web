<?php
require_once __DIR__ . '/conexion.php';
?>
<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Crear Cuenta — Cafetería UTHH</title>

  <link rel="stylesheet" href="../archivosCSS/registro.css?v=999.1">
  <link rel="stylesheet" href="../archivosCSS/layout.css?v=999.1" />
</head>

<body>
  <div class="app">
    
 <?php include 'header.php'; ?>
 

        <?php include 'barra_navegacion.php'; ?>
     

    <main class="content">
      <!-- ÚNICO CONTENEDOR: FORMULARIO DE REGISTRO -->
      <div class="form-container">
        <h2 style="text-align:center; color:#6b5b4b;">¡Bienvenido! Crea tu cuenta</h2>
        <p style="text-align:center; margin-bottom:20px; color:#666;">Llena tus datos para comenzar a pedir.</p>

        <!-- Enviamos la acción 'agregar' al procesador -->
        <form action="procesar_usuario.php?accion=agregar" method="post">

          <!-- ROL OCULTO: 3 = Cliente (Fijo por seguridad) -->
          <input type="hidden" name="rol" value="3">

          <div class="form-grid">
            <!-- COLUMNA 1 -->
            <div class="form-column">
              <div class="form-row">
                <label>Nombre(s)</label>
                <input type="text" name="nombre" placeholder="Ej. Juan" required />
              </div>
              <div class="form-row">
                <label>Apellido Paterno</label>
                <input type="text" name="ap" placeholder="Ej. Pérez" required />
              </div>
              <div class="form-row">
                <label>Apellido Materno</label>
                <input type="text" name="am" placeholder="Ej. López" required />
              </div>
              <div class="form-row">
                <label>Teléfono</label>
                <input type="tel" name="telefono" placeholder="Ej. 771 123 4567" required />
              </div>
            </div>

            <!-- COLUMNA 2 -->
            <div class="form-column">
              <div class="form-row">
                <label>Correo Electrónico</label>
                <input type="email" name="email" placeholder="correo@ejemplo.com" required />
              </div>
              <div class="form-row">
                <label>Dirección</label>
                <input type="text" name="direccion" placeholder="Calle, Número, Colonia" required />
              </div>
              <div class="form-row">
                <label>Contraseña</label>
                <input type="password" name="pass" placeholder="Crea una contraseña segura" required />
              </div>

              <!-- Botón de Registro -->
              <div class="actions">
                <button class="btn-action btn-add" type="submit">
                  Registrarme
                </button>
              </div>
            </div>
          </div>
        </form>
        <p style="text-align:center; margin-top:20px; font-size:0.9rem;">
          ¿Ya tienes cuenta? <a href="../archivosHTML/login.html" style="color:#6b5b4b; font-weight:bold;">Inicia sesión aquí</a>
        </p>
      </div>
    </main>
  </div>

   <?php include 'footer.php'; ?>
</body>

</html>
<?php
if (isset($conn) && $conn instanceof mysqli) {
  $conn->close();
}
?>