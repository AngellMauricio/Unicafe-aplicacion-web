<?php
// 1. DETECTAR PÁGINA ACTUAL
$pagina_actual = basename($_SERVER['PHP_SELF']);

// 2. DETECTAR UBICACIÓN
$es_index = ($pagina_actual == 'index.php');

// 3. PREFIJOS DE RUTAS
// $p_php: Para ir a otros archivos .php (si estoy en index entro a la carpeta, si no, me quedo)
$p_php  = $es_index ? "archivosPHP/" : "";

// $p_root: Para ir a la raíz (para buscar JS, CSS o Imágenes)
$p_root = $es_index ? "" : "../";
?>

<footer class="footer">
    <p>Universidad Tecnológica de la Huasteca Hidalguense</p>
    <p>&copy; 2025 Cafetería UTHH. Todos los derechos reservados.</p>

    <div class="footer-links">
        <a href="<?php echo $p_php; ?>Aviso_Privacidad.php">Aviso de Privacidad</a>
        <span class="separator">|</span>
        <a href="<?php echo $p_php; ?>terminos.php">Términos y condiciones</a>
        <span class="separator">|</span>
        <a href="<?php echo $p_php; ?>somos.php">Sobre nosotros</a>
    </div>
</footer>

<button id="btn-voz" class="voice-btn" aria-label="Escuchar el contenido de la página">
    🔊 Escuchar Contenido
</button>

<script src="<?php echo $p_root; ?>archivosJS/lector_voz.js"></script>
<script src="<?php echo $p_root; ?>archivosJS/accesibilidad.js"></script>

<div class="accessibility-panel">
    <button id="btn-zoom-in" aria-label="Aumentar tamaño">A+</button>
    <button id="btn-zoom-reset" aria-label="Restablecer tamaño">↺</button>
    <button id="btn-zoom-out" aria-label="Disminuir tamaño">A-</button>

    <button id="btn-contrast" aria-label="Cambiar modo de color" style="margin-top: 5px; border-color: #2a9d8f; color: #2a9d8f">
        🌗
    </button>
</div>