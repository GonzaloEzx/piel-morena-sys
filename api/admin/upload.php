<?php
/**
 * Piel Morena — API Admin: Upload de imágenes
 * POST (multipart/form-data) { imagen, tipo (servicios|productos|equipo|banners) }
 * Devuelve: { success, data: { url, filename } }
 */

require_once __DIR__ . '/../../includes/init.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    responder_json(false, null, 'Método no permitido', 405);
}

if (!esta_autenticado() || (!tiene_rol('admin') && !tiene_rol('empleado'))) {
    responder_json(false, null, 'No autorizado', 403);
}

// Validar que se envió un archivo
if (empty($_FILES['imagen']) || $_FILES['imagen']['error'] !== UPLOAD_ERR_OK) {
    $errores = [
        UPLOAD_ERR_INI_SIZE   => 'El archivo excede el tamaño máximo del servidor',
        UPLOAD_ERR_FORM_SIZE  => 'El archivo excede el tamaño máximo del formulario',
        UPLOAD_ERR_PARTIAL    => 'El archivo se subió parcialmente',
        UPLOAD_ERR_NO_FILE    => 'No se seleccionó ningún archivo',
        UPLOAD_ERR_NO_TMP_DIR => 'Falta el directorio temporal',
        UPLOAD_ERR_CANT_WRITE => 'Error al escribir el archivo',
    ];
    $code = $_FILES['imagen']['error'] ?? UPLOAD_ERR_NO_FILE;
    responder_json(false, null, $errores[$code] ?? 'Error al subir el archivo', 400);
}

$file = $_FILES['imagen'];
$tipo = $_POST['tipo'] ?? '';

// Validar tipo (carpeta destino)
$tipos_validos = ['servicios', 'productos', 'equipo', 'banners'];
if (!in_array($tipo, $tipos_validos)) {
    responder_json(false, null, 'Tipo inválido. Use: ' . implode(', ', $tipos_validos), 400);
}

// Validar extensión
$extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
$extensiones_validas = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
if (!in_array($extension, $extensiones_validas)) {
    responder_json(false, null, 'Formato no permitido. Use: ' . implode(', ', $extensiones_validas), 400);
}

// Validar MIME type real
$finfo = new finfo(FILEINFO_MIME_TYPE);
$mime = $finfo->file($file['tmp_name']);
$mimes_validos = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
if (!in_array($mime, $mimes_validos)) {
    responder_json(false, null, 'El archivo no es una imagen válida', 400);
}

// Validar tamaño (máx 5MB)
$max_size = 5 * 1024 * 1024;
if ($file['size'] > $max_size) {
    responder_json(false, null, 'La imagen no puede superar los 5MB', 400);
}

// Generar nombre único
$filename = $tipo . '_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $extension;
$destino_dir = UPLOADS_PATH . $tipo . '/';
$destino = $destino_dir . $filename;

// Crear directorio si no existe
if (!is_dir($destino_dir)) {
    mkdir($destino_dir, 0755, true);
}

// Mover archivo
if (!move_uploaded_file($file['tmp_name'], $destino)) {
    responder_json(false, null, 'Error al guardar la imagen', 500);
}

// Intentar redimensionar si es jpg/png y > 1200px de ancho
if (in_array($extension, ['jpg', 'jpeg', 'png']) && function_exists('imagecreatefromjpeg')) {
    $max_width = 1200;
    list($w, $h) = getimagesize($destino);

    if ($w > $max_width) {
        $ratio = $max_width / $w;
        $new_w = $max_width;
        $new_h = (int) ($h * $ratio);

        $src = $extension === 'png' ? imagecreatefrompng($destino) : imagecreatefromjpeg($destino);
        $dst = imagecreatetruecolor($new_w, $new_h);

        if ($extension === 'png') {
            imagealphablending($dst, false);
            imagesavealpha($dst, true);
        }

        imagecopyresampled($dst, $src, 0, 0, 0, 0, $new_w, $new_h, $w, $h);

        if ($extension === 'png') {
            imagepng($dst, $destino);
        } else {
            imagejpeg($dst, $destino, 85);
        }

        imagedestroy($src);
        imagedestroy($dst);
    }
}

$url = URL_BASE . '/uploads/' . $tipo . '/' . $filename;

responder_json(true, [
    'url'      => $url,
    'filename' => $filename,
    'tipo'     => $tipo
]);
