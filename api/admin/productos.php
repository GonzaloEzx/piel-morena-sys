<?php
/**
 * Piel Morena — API Admin: CRUD Productos
 */

require_once __DIR__ . '/../../includes/init.php';

if (!esta_autenticado() || !tiene_rol('admin')) {
    responder_json(false, null, 'No autorizado', 403);
}

$db     = getDB();
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    if (!empty($_GET['id'])) {
        $stmt = $db->prepare("SELECT * FROM productos WHERE id = ?");
        $stmt->execute([$_GET['id']]);
        $prod = $stmt->fetch();
        if (!$prod) responder_json(false, null, 'Producto no encontrado', 404);
        responder_json(true, $prod);
    }

    $stmt = $db->query("SELECT * FROM productos ORDER BY id DESC");
    responder_json(true, $stmt->fetchAll());
}

$input = json_decode(file_get_contents('php://input'), true) ?: [];

if ($method === 'POST') {
    $nombre   = trim($input['nombre'] ?? '');
    $desc     = trim($input['descripcion'] ?? '');
    $precio   = floatval($input['precio'] ?? 0);
    $stock    = intval($input['stock'] ?? 0);
    $stockMin = intval($input['stock_minimo'] ?? 5);

    if (!$nombre || $precio <= 0) {
        responder_json(false, null, 'Nombre y precio son obligatorios', 400);
    }

    $stmt = $db->prepare("INSERT INTO productos (nombre, descripcion, precio, stock, stock_minimo) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$nombre, $desc, $precio, $stock, $stockMin]);
    responder_json(true, ['id' => $db->lastInsertId()]);
}

if ($method === 'PUT') {
    $id       = intval($input['id'] ?? 0);
    $nombre   = trim($input['nombre'] ?? '');
    $desc     = trim($input['descripcion'] ?? '');
    $precio   = floatval($input['precio'] ?? 0);
    $stock    = intval($input['stock'] ?? 0);
    $stockMin = intval($input['stock_minimo'] ?? 5);

    if (!$id || !$nombre || $precio <= 0) {
        responder_json(false, null, 'Datos incompletos', 400);
    }

    $stmt = $db->prepare("UPDATE productos SET nombre=?, descripcion=?, precio=?, stock=?, stock_minimo=? WHERE id=?");
    $stmt->execute([$nombre, $desc, $precio, $stock, $stockMin, $id]);
    responder_json(true);
}

if ($method === 'DELETE') {
    $id = intval($input['id'] ?? 0);
    if (!$id) responder_json(false, null, 'ID requerido', 400);

    $stmt = $db->prepare("UPDATE productos SET activo = 0 WHERE id = ?");
    $stmt->execute([$id]);
    responder_json(true);
}

responder_json(false, null, 'Método no permitido', 405);
