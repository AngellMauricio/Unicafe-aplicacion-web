<?php
// 1. DETECTAR UBICACIÓN
$es_index = (basename($_SERVER['PHP_SELF']) == 'index.php');

// 2. DEFINIR PREFIJOS DE RUTAS
// Si estamos en el index (raíz), entramos a carpetas. Si no, nos quedamos o salimos.
$p_php  = $es_index ? "archivosPHP/" : "";
$p_html = $es_index ? "archivosHTML/" : "../archivosHTML/";
$p_root = $es_index ? "" : "../"; 

// 3. LÓGICA DE SESIÓN (Autónoma)
$h_logueado = isset($_SESSION['usuario']);
$h_usuario  = $h_logueado ? $_SESSION['usuario'] : '';
?>

<header class="topbar">
  <div class="topbar__left">
    <span class="avatar" aria-hidden="true">👤</span>

    <?php if ($h_logueado): ?>
        <div class="user-dropdown">
          <span class="user-trigger">
            Hola, <?php echo htmlspecialchars($h_usuario); ?> <span style="font-size:0.8em">▼</span>
          </span>
          <div class="dropdown-content">
            <a href="<?php echo $p_php; ?>mi_cuenta.php">⚙️ Mi Cuenta</a>
            <a href="<?php echo $p_php; ?>logout.php" class="logout-link">🚪 Cerrar Sesión</a>
          </div>
        </div>
    <?php else: ?>
        <a class="login-pill" href="<?php echo $p_php; ?>iniciar_sesion.php">Iniciar Sesión</a>
    <?php endif; ?>

  </div>
  <h1 class="title">CAFETERIA UTHH</h1>
  <div class="topbar__right"></div>
</header>