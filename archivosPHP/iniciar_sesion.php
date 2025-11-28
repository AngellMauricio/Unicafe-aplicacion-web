<?php
session_start();

require_once __DIR__ . '/conexion.php';
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Cafetería UTHH – Acceso</title>
  <link rel="stylesheet" href="/archivosCSS/layout.css?v=<?php echo time(); ?>" />
  <link rel="stylesheet" href="/archivosCSS/login.css?v=<?php echo time(); ?>" />
  

</head>
<body>
  <header class="topbar">
    <h1>CAFETERIA UTHH</h1>
  </header>

  <main class="stage" role="main">
    
    <svg class="cup" viewBox="0 0 600 380" aria-hidden="true">
      <path d="M180 70c30 40-30 40 0 80M300 70c30 40-30 40 0 80M420 70c30 40-30 40 0 80"
            fill="none" stroke="#6b5b4b" stroke-width="12" stroke-linecap="round"/>
      <path d="M120 140h300c6 0 10 4 10 10v25c0 76-36 116-160 116s-160-40-160-116v-25c0-6 4-10 10-10z"
            fill="none" stroke="#6b5b4b" stroke-width="12" stroke-linecap="round" stroke-linejoin="round"/>
      <path d="M430 170c46 0 70 20 70 55s-24 55-70 55"
            fill="none" stroke="#6b5b4b" stroke-width="12" stroke-linecap="round"/>
      <path d="M110 320h300c16 0 16 20 0 20H230c-8 14-26 14-34 0H110c-16 0-16-20 0-20z"
            fill="none" stroke="#6b5b4b" stroke-width="12" stroke-linecap="round" stroke-linejoin="round"/>
    </svg>

    <form class="login" action="/archivosPHP/login.php" method="post" autocomplete="on">
      
      <?php if(isset($_SESSION['error_login'])): ?>
        <div style="color: red; text-align: center; font-weight: bold; margin-bottom: 10px;">
            <?php 
                echo $_SESSION['error_login']; 
                unset($_SESSION['error_login']); // Clear message after showing
            ?>
        </div>
      <?php endif; ?>

      <label class="sr-only" for="usuario">Correo Electrónico</label>
      <input 
        id="usuario" 
        name="usuario" 
        type="email" 
        placeholder="CORREO" 
        required 
        style="text-align:center;"
      >

      <label class="sr-only" for="password">Contraseña</label>
      <input 
        id="password" 
        name="password" 
        type="password" 
        placeholder="CONTRASEÑA" 
        required 
        style="text-align:center;"
      >
      
      <button class="btn" type="submit">Entrar</button>
      
      <p style="text-align: center; font-size: 0.9rem; margin-top: 15px; color: #333;">
          <a href="../archivosPHP/Registro.php" style="color: #6b5b4b; font-weight: bold;">
            Crear una cuenta
          </a>
      </p>
    </form>
  </main>

  <?php include "footer.php"; ?>
</body>
</html>