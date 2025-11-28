<?php
session_start();

// 1. SEGURIDAD: Verificar que sea Administrador
if (!isset($_SESSION['rol_id']) || $_SESSION['rol_id'] != 1) {
    header("Location: ../index.php");
    exit;
}

// 2. CONEXIÓN
require_once __DIR__ . '/conexion.php';

// --- Función auxiliar para enviar notificaciones FCM (VERSIÓN DEBUG) ---
class GoogleTokenGenerator {
    private static function base64UrlEncode($data) {
        return str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($data));
    }

    public static function getAccessToken($jsonKeyFilePath) {
        if (!file_exists($jsonKeyFilePath)) return false;
        $credentials = json_decode(file_get_contents($jsonKeyFilePath), true);
        if (!$credentials || !isset($credentials['private_key']) || !isset($credentials['client_email'])) return false;

        $header = json_encode(['alg' => 'RS256', 'typ' => 'JWT']);
        $now = time();
        $payload = json_encode([
            'iss' => $credentials['client_email'],
            'sub' => $credentials['client_email'],
            'aud' => 'https://oauth2.googleapis.com/token',
            'iat' => $now,
            'exp' => $now + 3600,
            'scope' => 'https://www.googleapis.com/auth/firebase.messaging'
        ]);

        $base64UrlHeader = self::base64UrlEncode($header);
        $base64UrlPayload = self::base64UrlEncode($payload);
        $signatureInput = $base64UrlHeader . "." . $base64UrlPayload;

        $privateKey = $credentials['private_key'];
        if (!openssl_sign($signatureInput, $signature, $privateKey, OPENSSL_ALGO_SHA256)) return false;
        $jwt = $signatureInput . "." . self::base64UrlEncode($signature);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://oauth2.googleapis.com/token');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
            'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
            'assertion' => $jwt
        ]));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 
        $response = curl_exec($ch);
        curl_close($ch);

        $data = json_decode($response, true);
        return isset($data['access_token']) ? $data['access_token'] : false;
    }
}

function enviarNotificacionAFirebase($titulo, $cuerpo, $imagenUrl = null) {
    $rutaArchivoJson = __DIR__ . '/firebase_credenciales.json'; 
    $projectId = "unicafeapp-19698"; 

    $accessToken = GoogleTokenGenerator::getAccessToken($rutaArchivoJson);

    if (!$accessToken) {
        file_put_contents(__DIR__ . '/fcm_debug.txt', "--- ERROR TOKEN ---\n\n", FILE_APPEND);
        return;
    }

    $topic = "nuevos_productos"; 
    $mensajeV1 = [
        'message' => [
            'topic' => $topic,
            'notification' => [
                'title' => $titulo,
                'body'  => $cuerpo
            ],
            'android' => [
                'notification' => [
                    'sound' => 'default',
                    'click_action' => 'FLUTTER_NOTIFICATION_CLICK'
                ]
            ]
        ]
    ];

    $urlV1 = "https://fcm.googleapis.com/v1/projects/$projectId/messages:send";
    
    $headers = [
        'Authorization: Bearer ' . $accessToken, 
        'Content-Type: application/json'
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $urlV1);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true); 
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($mensajeV1));

    $result = curl_exec($ch);
    curl_close($ch);
}

if (!isset($_GET['accion'])) {
    header("Location: gestion_productos.php");
    exit;
}

$accion = $_GET['accion'];

function subirImagen($archivo) {
    $directorio = "../imagenes_productos/";
    if (!file_exists($directorio)) {
        mkdir($directorio, 0777, true);
    }
    $nombre_archivo = basename($archivo["name"]);
    $tipo_archivo = strtolower(pathinfo($nombre_archivo, PATHINFO_EXTENSION));
    
    $check = getimagesize($archivo["tmp_name"]);
    if($check === false) { return null; }

    $nuevo_nombre = "producto_" . uniqid() . "." . $tipo_archivo;
    $ruta_destino = $directorio . $nuevo_nombre;

    if (move_uploaded_file($archivo["tmp_name"], $ruta_destino)) {
        return "imagenes_productos/" . $nuevo_nombre;
    }
    return null;
}

// --- PROCESAMIENTO PRINCIPAL ---
try {
    switch ($accion) {
        
        // CASO 1: AGREGAR
        case 'agregar':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                // Obtenemos los valores
                $nombre = $_POST['nombre'];
                $desc   = $_POST['descripcion'];
                $stock  = (int)$_POST['stock'];
                $cat    = (int)$_POST['categoria'];
                $prov   = $_POST['proveedor']; 
                $pcompra = (float)$_POST['precio_compra'];
                $pventa  = (float)$_POST['precio_venta'];
                
              
                if ($stock < 0 || $pcompra < 0 || $pventa < 0) {
                    // Detenemos la ejecución y mandamos error
                    header("Location: gestion_productos.php?mensaje=error&detalle=valores_negativos");
                    exit(); 
                }
           

                $ruta_imagen = null;
                if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] == 0) {
                    $ruta_imagen = subirImagen($_FILES['imagen']);
                }

                $sql = "INSERT INTO tblproductos 
                        (vchNombre, vchDescripcion, intStock, intIdCategoria, vchRFCProveedor, decPrecioCompra, decPrecioVenta, vchImagen) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
                
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ssiisdds", $nombre, $desc, $stock, $cat, $prov, $pcompra, $pventa, $ruta_imagen);
                
                if($stmt->execute()) {
                    $nombreProdNuevo = $_POST['nombre']; 
                    $tituloNotif = "¡Nuevo Producto Disponible!";
                    $cuerpoNotif = "Ya puedes encontrar " . $nombreProdNuevo . " en nuestra tienda.";
                    enviarNotificacionAFirebase($tituloNotif, $cuerpoNotif);

                    header("Location: gestion_productos.php?mensaje=agregado");
                } else {
                    header("Location: gestion_productos.php?mensaje=error");
                }
                $stmt->close();
            }
            break;

        // CASO 2: ACTUALIZAR
        case 'actualizar':
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['id'])) {
                $id = (int)$_GET['id'];
                $nombre = $_POST['nombre'];
                $desc   = $_POST['descripcion'];
                $stock  = (int)$_POST['stock'];
                $cat    = (int)$_POST['categoria'];
                $prov   = $_POST['proveedor'];
                $pcompra = (float)$_POST['precio_compra'];
                $pventa  = (float)$_POST['precio_venta'];
                
               
                if ($stock < 0 || $pcompra < 0 || $pventa < 0) {
                    // Detenemos la ejecución y mandamos error
                    header("Location: gestion_productos.php?mensaje=error&detalle=valores_negativos");
                    exit(); 
                }
      

                $ruta_imagen = $_POST['imagen_actual'];

                if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] == 0) {
                    $nueva_ruta = subirImagen($_FILES['imagen']);
                    if ($nueva_ruta) {
                        $ruta_imagen = $nueva_ruta;
                    }
                }

                $sql = "UPDATE tblproductos SET 
                        vchNombre=?, vchDescripcion=?, intStock=?, intIdCategoria=?, 
                        vchRFCProveedor=?, decPrecioCompra=?, decPrecioVenta=?, vchImagen=? 
                        WHERE intIdProducto=?";
                
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ssiisddsi", $nombre, $desc, $stock, $cat, $prov, $pcompra, $pventa, $ruta_imagen, $id);
                
                if($stmt->execute()) {
                    header("Location: gestion_productos.php?mensaje=actualizado");
                } else {
                    header("Location: gestion_productos.php?mensaje=error");
                }
                $stmt->close();
            }
            break;

        // CASO 3: ELIMINAR
        case 'eliminar':
            if (isset($_GET['id'])) {
                $id = (int)$_GET['id'];
                $stmt = $conn->prepare("DELETE FROM tblproductos WHERE intIdProducto = ?");
                $stmt->bind_param("i", $id);
                
                if($stmt->execute()) {
                    header("Location: gestion_productos.php?mensaje=eliminado");
                } else {
                    header("Location: gestion_productos.php?mensaje=error");
                }
                $stmt->close();
            }
            break;
            
        default:
            header("Location: gestion_productos.php");
            break;
    }

} catch (Exception $e) {
    header("Location: gestion_productos.php?mensaje=error");
}

$conn->close();
exit;
?>