<?php
/**
 * Piel Morena — API Admin: Gestión de Galería
 * POST (multipart/form-data) { imagen, slot (1-6) } → sube/reemplaza imagen
 * GET → lista estado de los 6 slots
 *
 * Las imágenes se guardan en assets/img/gallery/ con nombre fijo galeria-0X.jpg
 * Subir una imagen nueva pisa la anterior del mismo slot.
 */

require_once __DIR__ . '/../../includes/init.php';

if (!esta_autenticado() || !tiene_rol('admin')) {
    responder_json(false, null, 'No autorizado', 403);
}

$gallery_dir = ROOT_PATH . 'assets/img/gallery/';

// ── GET: listar estado de slots ──
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $slots = [];
    for ($i = 1; $i <= 6; $i++) {
        $nombre = sprintf('galeria-%02d.jpg', $i);
        $ruta = $gallery_dir . $nombre;
        $existe = file_exists($ruta);
        $slots[] = [
            'slot'      => $i,
            'nombre'    => $nombre,
            'existe'    => $existe,
            'url'       => $existe ? URL_BASE . '/assets/img/gallery/' . $nombre . '?v=' . filemtime($ruta) : null,
            'tamanio'   => $existe ? filesize($ruta) : 0,
            'modificado' => $existe ? date('Y-m-d H:i:s', filemtime($ruta)) : null,
        ];
    }
    responder_json(true, $slots);
}

// ── POST: subir imagen a un slot ──
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $slot = intval($_POST['slot'] ?? 0);

    if ($slot < 1 || $slot > 6) {
        responder_json(false, null, 'Slot inválido. Debe ser entre 1 y 6.', 400);
    }

    if (empty($_FILES['imagen']) || $_FILES['imagen']['error'] !== UPLOAD_ERR_OK) {
        responder_json(false, null, 'No se recibió la imagen correctamente', 400);
    }

    $file = $_FILES['imagen'];

    // Validar extensión
    $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $extensiones_validas = ['jpg', 'jpeg', 'png', 'webp'];
    if (!in_array($extension, $extensiones_validas)) {
        responder_json(false, null, 'Formato no permitido. Usá JPG, PNG o WebP.', 400);
    }

    // Validar MIME
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->file($file['tmp_name']);
    if (!in_array($mime, ['image/jpeg', 'image/png', 'image/webp'])) {
        responder_json(false, null, 'El archivo no es una imagen válida', 400);
    }

    // Validar tamaño (máx 5MB)
    if ($file['size'] > 5 * 1024 * 1024) {
        responder_json(false, null, 'La imagen no puede superar los 5MB', 400);
    }

    // Nombre destino fijo: galeria-0X.jpg (siempre JPG para consistencia)
    $nombre_destino = sprintf('galeria-%02d.jpg', $slot);
    $ruta_destino = $gallery_dir . $nombre_destino;

    // Crear directorio si no existe
    if (!is_dir($gallery_dir)) {
        mkdir($gallery_dir, 0755, true);
    }

    // Eliminar imagen anterior si existe
    if (file_exists($ruta_destino)) {
        unlink($ruta_destino);
    }

    // Procesar: convertir a JPG y optimizar
    $tmp = $file['tmp_name'];

    if (function_exists('imagecreatefromjpeg')) {
        $src = null;
        switch ($mime) {
            case 'image/jpeg': $src = imagecreatefromjpeg($tmp); break;
            case 'image/png':  $src = imagecreatefrompng($tmp); break;
            case 'image/webp': $src = imagecreatefromwebp($tmp); break;
        }

        if ($src) {
            $w = imagesx($src);
            $h = imagesy($src);

            // Redimensionar si > 1400px de ancho
            $max_w = 1400;
            if ($w > $max_w) {
                $ratio = $max_w / $w;
                $new_w = $max_w;
                $new_h = (int)($h * $ratio);
                $dst = imagecreatetruecolor($new_w, $new_h);
                imagecopyresampled($dst, $src, 0, 0, 0, 0, $new_w, $new_h, $w, $h);
                imagedestroy($src);
                $src = $dst;
            }

            imagejpeg($src, $ruta_destino, 85);
            imagedestroy($src);
        } else {
            // Fallback: mover directo
            move_uploaded_file($tmp, $ruta_destino);
        }
    } else {
        move_uploaded_file($tmp, $ruta_destino);
    }

    if (!file_exists($ruta_destino)) {
        responder_json(false, null, 'Error al guardar la imagen', 500);
    }

    responder_json(true, [
        'slot'    => $slot,
        'nombre'  => $nombre_destino,
        'url'     => URL_BASE . '/assets/img/gallery/' . $nombre_destino . '?v=' . time(),
    ]);
}

responder_json(false, null, 'Método no permitido', 405);
