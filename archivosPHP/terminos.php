<?php
session_start();
require_once __DIR__ . '/conexion.php';

// 1. LÓGICA DE SESIÓN (Para el Header y Nav)
$usuario_logueado = isset($_SESSION['usuario']);
$es_admin = isset($_SESSION['rol_id']) && $_SESSION['rol_id'] == 1;
$es_empleado = isset($_SESSION['rol_id']) && $_SESSION['rol_id'] == 2;
$es_cliente = isset($_SESSION['rol_id']) && $_SESSION['rol_id'] == 3;

// 2. OBTENER TÉRMINOS DE LA BD
$sql = "SELECT * FROM tblterminos ORDER BY intIdTermino ASC";
$resultado = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Términos y Condiciones – Cafetería UTHH</title>

    <link rel="stylesheet" href="../archivosCSS/layout.css?v=999.1" />
    
    
    <style>
        .page-title {
            text-align: center;
            color: #765433;
            font-size: 2.5rem;
            margin-bottom: 40px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .terms-container {
            max-width: 1000px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .terms-grid {
            display: grid;
            gap: 25px;
        }

        .term-card {
            background: #fff;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.05);
            border-left: 5px solid #2A9D8F; /* Detalle de color */
            transition: transform 0.2s;
        }

        .term-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(0,0,0,0.1);
        }

        .term-title {
            font-size: 1.2rem;
            font-weight: 800;
            color: #2d2d2d;
            margin-bottom: 10px;
            text-transform: uppercase;
        }

        .term-desc {
            font-size: 1rem;
            color: #555;
            line-height: 1.6;
            text-align: justify;
        }
        
        body { background-color: #f3efe6; }
    </style>
</head>

<body>
   <?php include 'header.php'; ?>

        
        <?php include 'barra_navegacion.php'; ?>
    
        <main class="content">
            <h2 class="page-title">Términos y Condiciones de Uso</h2>

            <div class="terms-container">
                <div class="terms-grid">
                    <?php
                    if ($resultado && $resultado->num_rows > 0) {
                        while ($row = $resultado->fetch_assoc()) {
                    ?>
                            <div class="term-card">
                                <div class="term-title">
                                    <?php echo htmlspecialchars($row['vchTitulo']); ?>
                                </div>
                                <div class="term-desc">
                                    <?php echo nl2br(htmlspecialchars($row['txtDescripcion'])); ?>
                                </div>
                            </div>
                    <?php
                        }
                    } else {
                        echo "<div class='term-card' style='text-align:center;'>
                                <p>No hay términos y condiciones registrados por el momento.</p>
                              </div>";
                    }
                    ?>
                </div>
            </div>
        </main>
    </div>

    <?php include 'footer.php'; ?>

</body>
</html>
<?php 
if(isset($conn)) $conn->close(); 
?>