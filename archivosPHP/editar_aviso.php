<?php
session_start();
require_once __DIR__ . '/conexion.php';

// 1. SEGURIDAD
if (!isset($_SESSION['rol_id']) || ($_SESSION['rol_id'] != 1 && $_SESSION['rol_id'] != 2)) {
    header("Location: aviso_privacidad.php");
    exit;
}

$mensaje = "";
$tipo_alerta = "";

// 2. GUARDAR CAMBIOS (LÓGICA MEJORADA)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nuevo_contenido = $_POST['contenido'];
    $clave = 'aviso_privacidad';

    // Primero verificamos si ya existe el registro
    $check = $conn->query("SELECT id FROM tblconfiguracion WHERE clave = '$clave'");

    if ($check->num_rows > 0) {
        // SI EXISTE: Actualizamos (UPDATE)
        $stmt = $conn->prepare("UPDATE tblconfiguracion SET contenido = ? WHERE clave = ?");
        $stmt->bind_param("ss", $nuevo_contenido, $clave);
    } else {
        // NO EXISTE: Insertamos (INSERT)
        $stmt = $conn->prepare("INSERT INTO tblconfiguracion (contenido, clave) VALUES (?, ?)");
        $stmt->bind_param("ss", $nuevo_contenido, $clave);
    }

    if ($stmt->execute()) {
        // Redirigimos A LA MISMA PÁGINA con mensaje de éxito
        header("Location: editar_aviso.php?mensaje=guardado");
        exit;
    } else {
        $mensaje = "Error al guardar: " . $conn->error;
        $tipo_alerta = "error";
    }
    $stmt->close();
}

// 3. MENSAJES DE CONFIRMACIÓN
if (isset($_GET['mensaje']) && $_GET['mensaje'] == 'guardado') {
    $mensaje = "✅ ¡El aviso de privacidad se guardó correctamente!";
    $tipo_alerta = "success";
}

// 4. LEER DATOS ACTUALES
$sql = "SELECT contenido FROM tblconfiguracion WHERE clave = 'aviso_privacidad'";
$res = $conn->query($sql);
$fila = $res->fetch_assoc();
$texto_actual = $fila['contenido'] ?? ''; // Si está vacío, pone cadena vacía
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Editar Aviso — Cafetería UTHH</title>

    <link rel="stylesheet" href="../archivosCSS/layout.css?v=<?php echo time(); ?>" />
    
    <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>

    <style>
        .editor-wrapper {
            max-width: 1000px;
            margin: 40px auto;
            background: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .editor-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #eee;
            padding-bottom: 15px;
        }

        .btn-guardar {
            background-color: #2A9D8F;
            color: white;
            border: none;
            padding: 12px 30px;
            font-size: 1rem;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
            transition: background 0.3s;
        }
        .btn-guardar:hover { background-color: #21867a; }

        .btn-cancelar {
            color: #e76f51;
            text-decoration: none;
            font-weight: bold;
            padding: 10px;
        }

        /* Estilo de Alerta */
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
            text-align: center;
            font-weight: bold;
        }
        .alert.success { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .alert.error { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }

        /* Ajustes Summernote */
        .note-editor.note-frame { border: 1px solid #d9cfa8; }
        .note-toolbar { background-color: #f3efe6 !important; border-bottom: 1px solid #d9cfa8 !important; }
    </style>
</head>

<body>

    <div class="app">
        
        <?php include 'header.php'; ?>
        <?php include 'barra_navegacion.php'; ?>

        <main class="content">
            
            <?php if (!empty($mensaje)): ?>
                <div class="alert <?php echo $tipo_alerta; ?>">
                    <?php echo $mensaje; ?>
                </div>
            <?php endif; ?>

            <div class="editor-wrapper">
                <div class="editor-header">
                    <h2 style="margin:0; color:#765433;">Editar Aviso de Privacidad</h2>
                    <a href="Aviso_Privacidad.php" class="btn-cancelar">Cancelar / Volver</a>
                </div>

                <form action="editar_aviso.php" method="post">
                    <textarea id="summernote" name="contenido"><?php echo $texto_actual; ?></textarea>

                    <div style="text-align: right; margin-top: 20px;">
                        <button type="submit" class="btn-guardar">💾 Guardar Cambios</button>
                    </div>
                </form>
            </div>
        </main>

    </div>

    <script>
        $('#summernote').summernote({
            placeholder: 'Escribe aquí el contenido del aviso de privacidad...',
            tabsize: 2,
            height: 400,
            toolbar: [
                ['style', ['style']],
                ['font', ['bold', 'underline', 'clear']],
                ['color', ['color']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['table', ['table']],
                ['insert', ['link', 'hr']],
                ['view', ['fullscreen', 'codeview', 'help']]
            ]
        });
    </script>

</body>
</html>
<?php
if (isset($conn) && $conn instanceof mysqli) {
    $conn->close();
}
?>