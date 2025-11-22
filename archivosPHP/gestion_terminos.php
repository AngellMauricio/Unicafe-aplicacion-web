<?php
session_start(); // Necesario para verificar $_SESSION
include 'conexion.php';

// Lógica de Edición
$modo_edicion = false;
$id_editar = 0;
$titulo_val = "";
$desc_val = "";
$accion_form = "procesar_terminos.php?accion=agregar";

if (isset($_GET['accion']) && $_GET['accion'] == 'editar' && isset($_GET['id'])) {
    $modo_edicion = true;
    $id_editar = (int)$_GET['id'];
    $accion_form = "procesar_terminos.php?accion=actualizar&id=" . $id_editar;

    $stmt = $conn->prepare("SELECT * FROM tblterminos WHERE intIdTermino = ?");
    $stmt->bind_param("i", $id_editar);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($row = $res->fetch_assoc()) {
        $titulo_val = htmlspecialchars($row['vchTitulo']);
        $desc_val = htmlspecialchars($row['txtDescripcion']);
    }
}

// Listado
$sql_lista = "SELECT * FROM tblterminos";
$res_lista = $conn->query($sql_lista);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8" />
    <title>Gestionar Términos — Cafetería UTHH</title>
    <link rel="stylesheet" href="../archivosCSS/registro.css"> <!-- Reutilizamos estilos de admin -->
    <!-- <link rel="stylesheet" href="../archivosCSS/gestion_terminos.css">  <- Este no existe, usa estilos en línea o registro.css -->
    <style>
        /* Estilos específicos para esta página si no usas gestion_terminos.css */
        .list-container {
            margin-top: 2rem;
            padding: 1.5rem;
            background: #fff;
            border-radius: 8px;
        }

        .user-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
        }

        .user-table th,
        .user-table td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
            vertical-align: top;
        }

        .user-table th {
            background: #f4f4f4;
        }

        .action-links a {
            margin-right: 10px;
            font-weight: bold;
            text-decoration: none;
        }

        .edit-link {
            color: #007bff;
        }

        .delete-link {
            color: #dc3545;
        }

        textarea {
            width: 100%;
            padding: 8px;
            border: 1px solid #c8c8c8;
            border-radius: 4px;
            min-height: 100px;
            font-family: inherit;
        }
    </style>
</head>

<body>
    <div class="app">
        <header class="topbar">
            <div class="topbar__left"><span class="avatar">⚖️</span><a class="login-pill" href="#">Admin</a></div>
            <h1 class="title">CAFETERIA UTHH</h1>
        </header>
        <nav class="nav">
            <div class="nav__wrap">
                <a class="pill" href="../index.html">HOME <span class="ico">🏠</span></a> <!-- Corregida ruta relativa -->
                <a class="pill" href="productos.php">PRODUCTOS <span class="ico">📦</span></a> <!-- Corregida ruta relativa -->
                <a class="pill is-active" href="gestion_productos.php">⚙️ GESTIÓN PROD.</a>
                <a class="pill" href="menu.php">MENÚ <span class="ico">🍽️</span></a>
                <a class="pill" href="pedidos.php">PEDIDOS <span class="ico">🧾</span></a>

                <!-- AQUÍ ESTABA EL ERROR -->
                <?php if (isset($_SESSION['rol_id']) && $_SESSION['rol_id'] == 1) { ?>
                    <a class="pill" href="usuarios.php">USUARIOS <span class="ico">👤</span></a>
                <?php } ?> <!-- ¡ESTA LLAVE FALTABA! -->

            </div>
        </nav>

        <main class="content">
            <div class="form-container">
                <h2><?php echo $modo_edicion ? 'Editar Término' : 'Agregar Nuevo Término'; ?></h2>
                <form action="<?php echo $accion_form; ?>" method="post">
                    <div class="form-grid" style="grid-template-columns: 1fr;">
                        <div class="form-column">
                            <div class="form-row">
                                <label>Título</label>
                                <input type="text" name="titulo" value="<?php echo $titulo_val; ?>" required placeholder="Ej: Uso permitido">
                            </div>
                            <div class="form-row" style="align-items: flex-start;">
                                <label>Descripción</label>
                                <textarea name="descripcion" required><?php echo $desc_val; ?></textarea>
                            </div>
                            <div class="actions" style="justify-content: flex-start;">
                                <button class="btn-action btn-add" type="submit"><?php echo $modo_edicion ? 'Guardar Cambios' : 'Agregar'; ?></button>
                                <?php if ($modo_edicion): ?><a href="gestion_terminos.php" class="btn-action form-cancel-btn">Cancelar</a><?php endif; ?>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <div class="list-container">
                <h2>Listado de Términos y Condiciones</h2>
                <table class="user-table">
                    <thead>
                        <tr>
                            <th style="width: 20%;">Título</th>
                            <th>Descripción</th>
                            <th style="width: 15%;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $res_lista->fetch_assoc()): ?>
                            <tr>
                                <td><strong><?php echo htmlspecialchars($row['vchTitulo']); ?></strong></td>
                                <td><?php echo nl2br(htmlspecialchars($row['txtDescripcion'])); ?></td>
                                <td class="action-links">
                                    <a href="gestion_terminos.php?accion=editar&id=<?php echo $row['intIdTermino']; ?>" class="edit-link">Editar</a>
                                    <a href="procesar_terminos.php?accion=eliminar&id=<?php echo $row['intIdTermino']; ?>" class="delete-link" onclick="return confirm('¿Borrar este punto?');">Eliminar</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
</body>

</html>
<?php $conn->close(); ?>