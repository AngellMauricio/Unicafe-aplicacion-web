<?php
session_start();
require_once __DIR__ . '/conexion.php'; 

// 1. LÓGICA DE SESIÓN SUAVE (No expulsamos a nadie)
$usuario_logueado = isset($_SESSION['usuario']);
$nombre_usuario = $usuario_logueado ? $_SESSION['usuario'] : '';
$rol_usuario = isset($_SESSION['rol_id']) ? $_SESSION['rol_id'] : 0;

// 2. PERMISOS DE EDICIÓN (Solo Admin o Empleado ven el botón)
$es_staff = ($rol_usuario == 1 || $rol_usuario == 2);

// 3. OBTENER CONTENIDO DE LA BD
$sql = "SELECT contenido FROM tblconfiguracion WHERE clave = 'aviso_privacidad' LIMIT 1";
$res = $conn->query($sql);
$fila = $res->fetch_assoc();
$contenido_aviso = $fila['contenido'] ?? '<p>No hay información disponible.</p>';
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Aviso de Privacidad — Cafetería UTHH</title>
  
  <link rel="stylesheet" href="../archivosCSS/home.css?v=3.5" />
  <link rel="stylesheet" href="../archivosCSS/menu_desplegable.css" />
  <link rel="stylesheet" href="../archivosCSS/footer.css?v=3.6" />
  <link rel="stylesheet" href="../archivosCSS/accesibilidad.css" />
  
  <style>
    .privacy-container {
        max-width: 900px; margin: 40px auto; background-color: #ffffff;
        padding: 60px 40px; border-radius: 8px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.05); text-align: center;
        border: 1px solid #e0e0e0; position: relative;
    }
    .privacy-title { font-size: 2rem; color: #1f1f1f; margin-bottom: 50px; font-weight: normal; }
    
    /* Estilos para contenido dinámico */
    .privacy-content h3 { font-size: 1.3rem; color: #333; margin-bottom: 15px; text-transform: uppercase; font-weight: 500; margin-top: 40px; }
    .privacy-content p { font-size: 1rem; color: #666; line-height: 1.6; max-width: 80%; margin: 0 auto; }

    body { background-color: #f3efe6; }

    /* Botón Editar Flotante */
    .btn-editar-aviso {
        position: absolute; top: 20px; right: 20px;
        background-color: #2A9D8F; color: white; padding: 10px 15px;
        text-decoration: none; border-radius: 5px; font-weight: bold;
        font-size: 0.9rem; box-shadow: 0 2px 5px rgba(0,0,0,0.2);
    }
    .btn-editar-aviso:hover { background-color: #21867a; }
  </style>
</head>
<body>
  <div class="app">
    
    <header class="topbar">
      <div class="topbar__left">
        <span class="avatar">👤</span>
        
        <?php if ($usuario_logueado): ?>
            <div class="user-dropdown">
                <span class="user-trigger">Hola, <?php echo htmlspecialchars($nombre_usuario); ?> ▼</span>
                <div class="dropdown-content">
                    <a href="mi_cuenta.php">⚙️ Mi Cuenta</a>
                    <a href="logout.php">🚪 Cerrar Sesión</a>
                </div>
            </div>
        <?php else: ?>
            <a class="login-pill" href="../archivosHTML/login.html">Iniciar Sesión</a>
        <?php endif; ?>
        
      </div>
      <h1 class="title">CAFETERIA UTHH</h1>
      <div class="topbar__right"></div>
    </header>

    <?php include 'barra_navegacion.php'; ?>

    <main class="content">
        <div class="privacy-container">
            
            <?php if ($es_staff): ?>
                <a href="editar_aviso.php" class="btn-editar-aviso">✏️ Editar Texto</a>
            <?php endif; ?>

            <h2 class="privacy-title">Aviso de Privacidad</h2>

            <div class="privacy-content">
                <?php echo $contenido_aviso; ?>
            </div>
        </div>
    </main>
  </div>

  <?php include 'footer.php'; ?>
  
  <button id="btn-voz" class="voice-btn" aria-label="Escuchar contenido">🔊 Escuchar Contenido</button>
  
  <script src="../archivosJS/lector_voz.js"></script>
  <script src="../archivosJS/accesibilidad.js"></script>

  <div class="accessibility-panel">
    <button id="btn-zoom-in">A+</button>
    <button id="btn-zoom-reset">↺</button>
    <button id="btn-zoom-out">A-</button>
    <button id="btn-contrast" style="margin-top: 5px; border-color: #2a9d8f; color: #2a9d8f">🌗</button>
  </div>
</body>
</html>