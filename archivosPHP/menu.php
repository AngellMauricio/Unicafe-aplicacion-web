<?php
session_start();

require_once "conexion.php";

/* INICIALIZAR VARIABLES */
$platilloEditar = null;   // evita warning de variable indefinida
$categorias     = [];     // evita foreach(null)

/*  MODO EDICIÓN (si viene ?modo=editar&id=) SOLO PARA ADMIN */
$modo     = $_GET['modo'] ?? "";
$idEditar = $_GET['id'] ?? "";

if (
    isset($_SESSION['rol_id']) && $_SESSION['rol_id'] == 1 &&  // solo admin
    $modo === "editar" && $idEditar !== ""
) {
    $id = (int)$idEditar;
    if ($id > 0) {
        $stmt = $conn->prepare("SELECT * FROM tblmenu WHERE intIdPlatillo = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $resultado = $stmt->get_result();
        $platilloEditar = $resultado->fetch_assoc();
        $stmt->close();
    }
}

/*
   OBTENER TODOS LOS PLATILLOS AGRUPADOS POR CATEGORÍA
   */
$sql = "SELECT * FROM tblmenu ORDER BY vchCategoria, vchNombre";
$res = $conn->query($sql);

if ($res) {
    while ($row = $res->fetch_assoc()) {
        $categorias[$row['vchCategoria']][] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Menú – Cafetería UTHH</title>
  <link rel="stylesheet" href="/archivosCSS/layout.css?v=999.1" />
 
  <style>
    /* Formularios CRUD */
    .form-crud {
      max-width: 600px; margin: 15px auto; padding: 15px;
      background: #fdf5e6; border-radius: 10px; border: 1px solid #d0b38a;
    }
    .form-crud h2 { margin-top: 0; text-align: center; }
    .form-crud label { display: block; margin-top: 6px; font-size: 14px; font-weight: bold; }
    .form-crud input[type="text"], .form-crud input[type="number"] {
      width: 100%; padding: 8px; margin-top: 2px; border-radius: 5px; border: 1px solid #ccc;
    }
    .form-crud button, .form-crud .btn-cancelar {
      margin-top: 15px; padding: 8px 15px; border-radius: 5px; border: none; cursor: pointer; font-weight: 600;
    }
    .form-crud button { background: #28a745; color: #fff; }
    .form-crud .btn-cancelar { background: #6c757d; color: #fff; text-decoration: none; display: inline-block; }

    /* Estilos para previsualización de imagen */
    .img-preview-box {
        margin-top: 10px; text-align: center; background: #fff; padding: 10px; border: 1px dashed #ccc;
    }
    .img-preview-box img { max-height: 150px; object-fit: contain; }

    /* GRID DE CATEGORÍAS: columnas auto-ajustables */
    .menu-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: 20px;
      justify-items: center;
      align-items: stretch;
    }
    /* 🔧 Ajuste para evitar que se encimen las tarjetas */
    .category {
      width: 100%;
      max-width: 260px;
      margin: 0 auto;
    }
    
    .tile-group {
      padding: 10px !important;
    }
    
    .tile-row {
      padding: 6px 0 !important;
      margin-bottom: 6px !important;
    }
    
    .tile__img {
      width: 55px;
      height: 55px;
    }


    /* Panel de categoría (título + tarjeta) */
    .category {
      background: #fff;
      border: 2px solid var(--panel-br, #bfa88a);
      border-radius: 8px;
      padding: 14px;
      box-shadow: 0 1px 2px rgba(0,0,0,.06);
    }
    .category__title {
      text-align: center;
      font-size: 20px;
      font-weight: 700;
      margin: 4px 0 12px;
    }

    /* ===== TARJETA POR CATEGORÍA (CONTENEDOR) ===== */
    .tile-group{
      width: 100%;
      border: 2px solid #a8d6c8;
      border-radius: 10px;
      padding: 12px 14px;
      background: #ffffff;
      box-shadow: 0 2px 4px rgba(0,0,0,.10);
    }

    /* Cada platillo dentro de la tarjeta de la categoría */
    .tile-row{
      display:flex;
      align-items:center;
      gap:12px;
      padding:8px 0;
      border-bottom:1px solid #e4e4e4;
      margin-bottom:8px;
    }
    .tile-row:last-child{
      border-bottom:none;
      margin-bottom:0;
      padding-bottom:0;
    }

    /* Imagen del platillo (más compacta) */
    .tile__img {
        width: 60px; height: 60px; flex-shrink: 0;
        background-color: #eee; border-radius: 5px; overflow: hidden;
        display: flex; align-items: center; justify-content: center;
        border:1px solid #d4d4d4;
    }
    .tile__img img { width: 100%; height: 100%; object-fit: cover; }

    /* Info (nombre + precio) */
    .tile__info{
      flex:1;
      display:flex;
      flex-direction:column;
      gap:4px;
    }
    .tile__info strong{
      font-size:14px;
      line-height:1.2;
      color:#222;
    }
    .price{
      display:inline-block;
      margin-top:4px;
      padding:3px 8px;
      border-radius:8px;
      background:#fff;
      border:1px solid #cfdad6;
      font-weight:700;
      font-size:13px;
      color:#333;
    }

    /* Botones de cada platillo */
    .tile__actions{
      display:flex;
      flex-direction:column;
      gap:4px;
    }
    .btn-crud{
      border:none;
      padding:4px 8px;
      border-radius:4px;
      font-size:11px;
      cursor:pointer;
    }
    .btn-editar{
      background:#699dd4;
      color:#fff;
      text-decoration:none;
      text-align:center;
    }
    .btn-eliminar{
      background:#dd5865;
      color:#fff;
    }
    .btn-detalle{
      background:#ffb347;
      color:#603813;
      font-weight:700;
      text-transform:uppercase;
    }
    .btn-detalle:hover{
      background:#ff9c12;
    }
    
    /* Estilo para cuando no hay imagen (placeholder) */
    .no-image-placeholder {
        width: 100%; height: 100%;
        background-color: #efe3cf; color: #8a633b;
        display: flex; align-items: center; justify-content: center;
        font-weight: bold; font-size: 20px; text-align: center;
    }

    /* Responsive extra (ya casi no se usa por auto-fit) */
    @media (max-width: 1100px){
      .menu-grid{grid-template-columns: 1fr 1fr;}
    }
    @media (max-width: 700px){
      .menu-grid{grid-template-columns: 1fr;}
    }
  </style>
</head>
<body>
  <div class="app">


    <?php include "header.php"; ?>
    <?php include "barra_navegacion.php"; ?>

    <main class="content">

      <!-- ========== FORMULARIO: AGREGAR (SOLO ADMIN) ========== -->
      <?php if (isset($_SESSION['rol_id']) && $_SESSION['rol_id'] == 1): ?>
        <?php if (!$platilloEditar): ?>
        <form class="form-crud" action="menu_acciones.php?accion=agregar" method="post" enctype="multipart/form-data">
          <h2>Agregar nuevo platillo</h2>

          <label>Categoría</label>
          <input type="text" name="categoria" placeholder="Guisados, Tacos, Tortas..." required>

          <label>Nombre del platillo</label>
          <input type="text" name="nombre" placeholder="Ej. Torta de milanesa" required>

          <label>Precio</label>
          <input type="number" step="0.01" name="precio" placeholder="Ej. 40" required>

          <label>Imagen del Platillo</label>
          <input type="file" name="imagen" accept="image/*">

          <button type="submit">Agregar platillo</button>
        </form>
        <?php endif; ?>
      <?php endif; ?>

      <!-- ========== FORMULARIO: EDITAR (SOLO ADMIN) ========== -->
      <?php if (isset($_SESSION['rol_id']) && $_SESSION['rol_id'] == 1): ?>
        <?php if ($platilloEditar): ?>
        <form class="form-crud" action="menu_acciones.php?accion=actualizar&id=<?php echo $platilloEditar['intIdPlatillo']; ?>" method="post" enctype="multipart/form-data">
          <h2>Editar platillo</h2>

          <label>Categoría</label>
          <input type="text" name="categoria" value="<?php echo htmlspecialchars($platilloEditar['vchCategoria']); ?>" required>

          <label>Nombre del platillo</label>
          <input type="text" name="nombre" value="<?php echo htmlspecialchars($platilloEditar['vchNombre']); ?>" required>

          <label>Precio</label>
          <input type="number" step="0.01" name="precio" value="<?php echo htmlspecialchars($platilloEditar['decPrecio']); ?>" required>

          <label>Imagen (Dejar vacío para no cambiar)</label>
          <input type="file" name="imagen" accept="image/*">
          
          <input type="hidden" name="imagen_actual" value="<?php echo htmlspecialchars($platilloEditar['vchImagen']); ?>">

          <?php if(!empty($platilloEditar['vchImagen'])): ?>
              <div class="img-preview-box">
                  <p style="margin:0; font-size:12px; color:#666;">Imagen Actual:</p>
                  <img src="../<?php echo htmlspecialchars($platilloEditar['vchImagen']); ?>" alt="Actual">
              </div>
          <?php endif; ?>

          <button type="submit">Guardar cambios</button>
          <a class="btn-cancelar" href="menu.php">Cancelar</a>
        </form>
        <?php endif; ?>
      <?php endif; ?>

      <center> <br><h2>Nuestro Menú del día</h2><br></center>
      
      <!-- ========== MENÚ DINÁMICO (UNA TARJETA POR CATEGORÍA) ========== -->
      <div class="menu-grid">
        <?php foreach ($categorias as $nombreCat => $items): ?>
          <section class="category">
            <h3 class="category__title"><?php echo htmlspecialchars($nombreCat); ?></h3>

            <article class="tile-group">
              <?php foreach ($items as $p): ?>
                <?php
                    $imgDb = $p['vchImagen'];
                    $rutaFisica = "../" . $imgDb;
                    $mostrarImagen = (!empty($imgDb) && file_exists($rutaFisica));
                ?>
                <div class="tile-row">
                  <div class="tile__img">
                    <?php if ($mostrarImagen): ?>
                        <img src="<?php echo $rutaFisica; ?>" alt="<?php echo htmlspecialchars($p['vchNombre']); ?>">
                    <?php else: ?>
                        <div class="no-image-placeholder">
                            <?php echo strtoupper(substr($p['vchNombre'], 0, 1)); ?>
                        </div>
                    <?php endif; ?>
                  </div>
                  
                  <div class="tile__info">
                    <strong><?php echo htmlspecialchars($p['vchNombre']); ?></strong>
                    <span class="price">$<?php echo number_format($p['decPrecio'], 2); ?></span>
                  </div>
                  
                  <div class="tile__actions">
                    <!-- Ver detalle (usuarios normales y admin) -->
                    <a
                      href="menudetalle.php?id=<?php echo $p['intIdPlatillo']; ?>"
                      class="btn-crud btn-detalle"
                    >
                      Ver detalle
                    </a>

                    <!-- Botones solo para ADMIN -->
                    <?php if (isset($_SESSION['rol_id']) && $_SESSION['rol_id'] == 1): ?>
                      <a class="btn-crud btn-editar" href="menu.php?modo=editar&id=<?php echo $p['intIdPlatillo']; ?>">Editar</a>
                      <form action="menu_acciones.php?accion=eliminar&id=<?php echo $p['intIdPlatillo']; ?>" method="post" onsubmit="return confirm('¿Eliminar este platillo?');">
                        <button type="submit" class="btn-crud btn-eliminar">Eliminar</button>
                      </form>
                    <?php endif; ?>
                  </div>

                </div>
              <?php endforeach; ?>
            </article>

          </section>
        <?php endforeach; ?>
      </div>
    </main>
  </div>

  <?php include "footer.php"; ?>
</body>
</html>
<?php $conn->close(); ?>
