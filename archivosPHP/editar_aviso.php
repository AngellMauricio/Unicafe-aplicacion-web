<?php
session_start();
require_once __DIR__ . '/conexion.php';

// SEGURIDAD: Solo Admin (1) o Empleado (2)
if (!isset($_SESSION['rol_id']) || ($_SESSION['rol_id'] != 1 && $_SESSION['rol_id'] != 2)) {
    echo "<script>alert('No tienes permisos.'); window.location='aviso_privacidad.php';</script>";
    exit;
}

// GUARDAR CAMBIOS
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // El editor envía el texto ya con el HTML generado
    $nuevo_contenido = $_POST['contenido'];

    $stmt = $conn->prepare("UPDATE tblconfiguracion SET contenido = ? WHERE clave = 'aviso_privacidad'");
    $stmt->bind_param("s", $nuevo_contenido);

    if ($stmt->execute()) {
        // Redirigir de vuelta al aviso para ver cómo quedó
        echo "<script>window.location='aviso_privacidad.php';</script>";
        exit;
    } else {
        $error = "Error: " . $conn->error;
    }
    $stmt->close();
}

// LEER DATOS ACTUALES
$sql = "SELECT contenido FROM tblconfiguracion WHERE clave = 'aviso_privacidad'";
$res = $conn->query($sql);
$fila = $res->fetch_assoc();
$texto_actual = $fila['contenido'] ?? '';
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Editar Aviso — Cafetería UTHH</title>

    <link rel="stylesheet" href="../archivosCSS/layout.css?v=999.1" />
    <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>

    <style>
        /* Estilos específicos del contenedor del editor */
        .editor-wrapper {
            max-width: 1000px;
            margin: 40px auto;
            /* Centrado con margen */
            background: #fff;
            padding: 20px;
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
        }

        .btn-guardar:hover {
            background-color: #21867a;
        }

        .btn-cancelar {
            color: #e76f51;
            text-decoration: none;
            font-weight: bold;
        }

        /* Ajustes para que Summernote combine con el diseño café */
        .note-editor.note-frame {
            border: 1px solid #d9cfa8;
        }

        .note-toolbar {
            background-color: #f3efe6 !important;
            border-bottom: 1px solid #d9cfa8 !important;
        }

        /* Botón de Vista Previa */
        .btn-preview {
            background-color: #2A9D8F;
            color: white;
            text-decoration: none;
            padding: 8px 15px;
            border-radius: 5px;
            font-weight: bold;
            font-size: 0.9rem;
            margin-right: 15px;
            display: inline-flex;
            align-items: center;
        }

        .btn-preview:hover {
            background-color: #21867a;
        }
    </style>
</head>

<body>

    <div class="app">
        
        <?php include 'header.php'; ?>
        <?php include 'barra_navegacion.php'; ?>

        <main class="content">
            <div class="editor-wrapper">
                <div class="editor-header">
                    <h2 style="margin:0; color:#765433;">Editar Aviso de Privacidad</h2>

                    <div style="display: flex; align-items: center;">
                        <a href="Aviso_Privacidad.php" target="_blank" class="btn-preview">
                            👁️ Ver como Usuario
                        </a>
                        <a href="Aviso_Privacidad.php" class="btn-cancelar">Cancelar</a>
                    </div>
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
            placeholder: 'Escribe aquí el aviso de privacidad...',
            tabsize: 2,
            height: 400, // Altura del editor
            lang: 'es-ES', // Idioma español
            toolbar: [
                ['style', ['style']],
                ['font', ['bold', 'underline', 'clear']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['insert', ['link', 'hr']],
                ['view', ['fullscreen', 'help']]
            ],
            styleTags: [{
                    title: 'Párrafo Normal',
                    tag: 'p',
                    value: 'p'
                },
                {
                    title: 'TITULO SECCIÓN (I, II, III)',
                    tag: 'h3',
                    value: 'h3'
                },
                {
                    title: 'Subtítulo',
                    tag: 'h4',
                    value: 'h4'
                }
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