<?php
session_start();
require_once "conexion.php";

// Validar ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "Platillo no encontrado.";
    exit;
}

$id = (int)$_GET['id'];

// Consultar platillo
$stmt = $conn->prepare("SELECT * FROM tblmenu WHERE intIdPlatillo = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$res = $stmt->get_result();
$platillo = $res->fetch_assoc();
$stmt->close();

if (!$platillo) {
    echo "Platillo no encontrado.";
    exit;
}

// Imagen
$imgDb = $platillo['vchImagen'] ?? "";
$rutaImg = "../" . $imgDb;
$tieneImagen = (!empty($imgDb) && file_exists($rutaImg));
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title><?php echo htmlspecialchars($platillo['vchNombre']); ?> – Cafetería UTHH</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <style>
    *{
      box-sizing:border-box;
      margin:0;
      padding:0;
      font-family:'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    body{
      background:#b08b5a; 
      min-height:100vh;
      display:flex;
      align-items:center;
      justify-content:center;
    }

    .overlay-bg{
      position:fixed;
      inset:0;
      background:rgba(0,0,0,0.30);
      z-index:0;
    }

    .detalle-card{
      position:relative;
      z-index:1;
      width:90%;
      max-width:950px;
      min-height:380px;
      background:#fff7ec;
      border-radius:14px;
      overflow:hidden;
      box-shadow:0 12px 40px rgba(0,0,0,0.45);
      display:grid;
      grid-template-columns:1.1fr 1fr;
    }

    .detalle-img{
      background:#ffffff;
      display:flex;
      align-items:center;
      justify-content:center;
      padding:25px;
    }

    .detalle-img img{
      max-width:100%;
      max-height:360px;
      object-fit:contain;
    }

    .detalle-info{
      position:relative;
      padding:32px 36px;
      background:#fff7ec;
    }

    .btn-close{
      position:absolute;
      top:18px;
      right:20px;
      width:36px;
      height:36px;
      border-radius:50%;
      border:none;
      background:#d9d7d2;
      color:#4a3b30;
      font-size:22px;
      cursor:pointer;
      display:flex;
      align-items:center;
      justify-content:center;
    }

    .categoria{
      display:inline-block;
      padding:4px 10px;
      border-radius:999px;
      background:#f1dfc7;
      color:#7a4b23;
      font-size:12px;
      font-weight:600;
      margin-bottom:10px;
    }

    .detalle-titulo{
      font-size:26px;
      text-transform:uppercase;
      letter-spacing:1px;
      font-weight:800;
      color:#7a4b23;
      margin-bottom:18px;
    }

    .detalle-sub{
      font-size:14px;
      color:#8c8c8c;
      margin-bottom:16px;
    }

    .detalle-desc{
      font-size:15px;
      color:#4a4a4a;
      margin-bottom:24px;
    }

    .detalle-precio{
      font-size:26px;
      font-weight:900;
      color:#008b7a;
    }

    .detalle-precio span{
      font-size:15px;
      margin-left:4px;
      font-weight:700;
    }

    .btn-volver{
      margin-top:26px;
      display:inline-block;
      padding:9px 18px;
      border-radius:999px;
      text-decoration:none;
      background:#7a4b23;
      color:#fff;
      font-weight:600;
      font-size:14px;
    }

    @media(max-width:900px){
      .detalle-card{
        grid-template-columns:1fr;
      }
      .detalle-img{
        border-bottom:1px solid #eee;
      }
    }
  </style>
</head>
<body>

<div class="overlay-bg"></div>

<div class="detalle-card">

  <div class="detalle-img">
    <?php if ($tieneImagen): ?>
      <img src="<?php echo $rutaImg; ?>" alt="<?php echo htmlspecialchars($platillo['vchNombre']); ?>">
    <?php else: ?>
      <span>No hay imagen</span>
    <?php endif; ?>
  </div>

  <div class="detalle-info">

    <button class="btn-close" onclick="window.location.href='menu.php';">&times;</button>

    <div class="categoria">
      <?php echo htmlspecialchars($platillo['vchCategoria']); ?>
    </div>

    <h1 class="detalle-titulo">
      <?php echo htmlspecialchars($platillo['vchNombre']); ?>
    </h1>

    <p class="detalle-sub">
      Disponible en la cafetería UTHH
    </p>

    <p class="detalle-desc">
      <?php
        if (!empty($platillo['vchDescripcion'])) {
          echo htmlspecialchars($platillo['vchDescripcion']);
        } else {
          echo "Delicioso platillo preparado en la Cafetería UTHH.";
        }
      ?>
    </p>

    <p class="detalle-precio">
      $<?php echo number_format($platillo['decPrecio'], 2); ?>
      <span>MXN</span>
    </p>

    <a href="menu.php" class="btn-volver">← Regresar al menú</a>

  </div>
</div>

</body>
</html>
<?php $conn->close(); ?>
