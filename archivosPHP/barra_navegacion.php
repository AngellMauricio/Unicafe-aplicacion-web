<?php
// 1. DETECTAR PÁGINA ACTUAL
$pagina_actual = basename($_SERVER['PHP_SELF']);

// 2. DETECTAR UBICACIÓN
$es_index = ($pagina_actual == 'index.php');

// 3. PREFIJOS DE RUTAS
$p_php  = $es_index ? "archivosPHP/" : "";
$p_html = $es_index ? "archivosHTML/" : "../archivosHTML/";
$p_root = $es_index ? "" : "../";

// 4. OBTENER ROL
$rol = isset($_SESSION['rol_id']) ? $_SESSION['rol_id'] : 0;
?>

<nav class="nav">
  <div class="nav__wrap">
    
    <a class="pill <?php echo ($pagina_actual == 'index.php') ? 'is-active' : ''; ?>" 
       href="<?php echo $p_root; ?>index.php">
       HOME <span class="ico">🏠</span>
    </a>

    <?php if ($rol != 1): ?>
        
        <a class="pill <?php echo ($pagina_actual == 'productos.php') ? 'is-active' : ''; ?>" 
           href="<?php echo $p_php; ?>productos.php">
           PRODUCTOS <span class="ico">📦</span>
        </a>

        <a class="pill <?php echo ($pagina_actual == 'menu.php') ? 'is-active' : ''; ?>" 
           href="<?php echo $p_php; ?>menu.php">
           MENÚ <span class="ico">🍽️</span>
        </a>

    <?php endif; ?>


    <?php if ($rol == 3): ?>
        <a class="pill <?php echo ($pagina_actual == 'pedidos.php') ? 'is-active' : ''; ?>" 
           href="<?php echo $p_php; ?>pedidos.php">
           MIS PEDIDOS <span class="ico">🧾</span>
        </a>
    <?php endif; ?>


    <?php if ($rol == 2): ?>
        <a class="pill <?php echo ($pagina_actual == 'pedidos.php') ? 'is-active' : ''; ?>" 
           href="<?php echo $p_php; ?>pedidos.php" style="border: 1px solid #c59b42;">
           GESTIONAR PEDIDOS <span class="ico">👨‍🍳</span>
        </a>
        
        
    <?php endif; ?>


    <?php if ($rol == 1): ?>
        
        <a class="pill <?php echo ($pagina_actual == 'pedidos.php') ? 'is-active' : ''; ?>" 
           href="<?php echo $p_php; ?>pedidos.php">
           PEDIDOS <span class="ico">🧾</span>
        </a>

        <a class="pill <?php echo ($pagina_actual == 'gestion_productos.php') ? 'is-active' : ''; ?>" 
           href="<?php echo $p_php; ?>gestion_productos.php" title="Editar Productos">
           ⚙️ PRODUCTOS
        </a>
           
        <a class="pill <?php echo ($pagina_actual == 'gestion_terminos.php') ? 'is-active' : ''; ?>" 
           href="<?php echo $p_php; ?>gestion_terminos.php" title="Editar Términos">
           ⚙️ TÉRMINOS
        </a>
           
        <a class="pill <?php echo ($pagina_actual == 'editar_aviso.php') ? 'is-active' : ''; ?>" 
           href="<?php echo $p_php; ?>editar_aviso.php" title="Editar Aviso Privacidad">
           ⚙️ AVISO
        </a>
        <a class="pill <?php echo ($pagina_actual == 'gestion_somos.php') ? 'is-active' : ''; ?>" 
           href="<?php echo $p_php; ?>gestion_somos.php" title="Editar APARTADO SOMOS">
           ⚙️ SOMOS
        </a>
        <a class="pill <?php echo ($pagina_actual == 'usuarios.php') ? 'is-active' : ''; ?>" 
           href="<?php echo $p_php; ?>usuarios.php" title="Gestionar Usuarios">
           REGISTROS <span class="ico">👤</span>
        </a>
    <?php endif; ?>

  </div>
</nav>