<?php
session_start();
require_once __DIR__ . '/conexion.php';

// Consultar información
$sql = "SELECT * FROM tblsomos";
$res = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8" />
  <title>Somos Unicafe — Cafetería UTHH</title>
  
  <link rel="stylesheet" href="../archivosCSS/layout.css?v=999.1" />
  
  <style>
    .somos-container {
        max-width: 1000px; margin: 0 auto; padding: 40px 20px;
    }
    .page-title { text-align: center; font-size: 2.5rem; color: #1f1f1f; margin-bottom: 50px; }

    
    .somos-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 60px;
        gap: 40px;
    }

    /* Imagen */
    .somos-img-box {
        flex: 1;
        height: 300px;
        background-color: #ccc; 
        display: flex; justify-content: center; align-items: center;
        overflow: hidden; border-radius: 8px;
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    }
    .somos-img-box img { width: 100%; height: 100%; object-fit: cover; }
    .placeholder-text { font-size: 1.5rem; color: #555; text-align: center; padding: 20px; }

    
    .somos-text {
        flex: 1;
        font-size: 1.2rem;
        color: #333;
        line-height: 1.6;
    }

    
    .somos-row:nth-child(even) {
        flex-direction: row-reverse; 
    }

    
    @media (max-width: 768px) {
        .somos-row, .somos-row:nth-child(even) {
            flex-direction: column; text-align: center;
        }
        .somos-img-box { width: 100%; height: 250px; }
    }
  </style>
</head>
<body>
<div class="app">
    
<?php include 'header.php'; ?>
    <?php include 'barra_navegacion.php'; ?>

    <main class="content">
        <h2 class="page-title">Somos Unicafe</h2>
        
        <div class="somos-container">
            <?php if ($res && $res->num_rows > 0): ?>
                <?php while ($row = $res->fetch_assoc()): ?>
                    <?php $img = !empty($row['vchImagen']) ? "../" . $row['vchImagen'] : ""; ?>
                    
                    <div class="somos-row">
                        <div class="somos-img-box">
                            <?php if (!empty($img) && file_exists($img)): ?>
                                <img src="<?php echo $img; ?>" alt="Foto Somos Unicafe">
                            <?php else: ?>
                                <div class="placeholder-text">fotos de unicafe</div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="somos-text">
                            <?php echo nl2br(htmlspecialchars($row['txtDescripcion'])); ?>
                        </div>
                    </div>

                <?php endwhile; ?>
            <?php else: ?>
                <p style="text-align:center;">Aún no hay información disponible.</p>
            <?php endif; ?>
        </div>
    </main>
</div>

<?php include 'footer.php'; ?>
</body>
</html>
<?php if(isset($conn)) $conn->close(); ?>