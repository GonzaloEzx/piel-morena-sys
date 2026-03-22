<?php
/**
 * Piel Morena — API Admin: CRUD Servicios
 * GET    → Listar / obtener uno (?id=X)
 * POST   → Crear
 * PUT    → Editar
 * DELETE → Soft-delete (activo=0)
 */

require_once __DIR__ . '/../../includes/init.php';

if (!esta_autenticado() || !tiene_rol('admin')) {
    responder_json(false, null, 'No autorizado', 403);
}

$db     = getDB();
$method = $_SERVER['REQUEST_METHOD'];

// ── GET ──
if ($method === 'GET') {
    if (!empty($_GET['id'])) {
        $stmt = $db->prepare("SELECT * FROM servicios WHERE id = ?");
        $stmt->execute([$_GET['id']]);
        $srv = $stmt->fetch();
        if (!$srv) responder_json(false, null, 'Servicio no encontrado', 404);
        responder_json(true, $srv);
    }

    $stmt = $db->query(
        "SELECT s.*, c.nombre AS categoria
         FROM servicios s
         LEFT JOIN categorias_servicios c ON s.id_categoria = c.id
         ORDER BY s.id DESC"
    );
    responder_json(true, $stmt->fetchAll());
}

// ── Leer body JSON para POST/PUT/DELETE ──
$input = json_decode(file_get_contents('php://input'), true) ?: [];

// ── POST (crear) ──
if ($method === 'POST') {
    $nombre   = trim($input['nombre'] ?? '');
    $precio   = floatval($input['precio'] ?? 0);
    $duracion = intval($input['duracion_minutos'] ?? 30);
    $desc     = trim($input['descripcion'] ?? '');
    $cat      = $input['id_categoria'] ?? null;
    $imagen   = trim($input['imagen'] ?? '');

    if (!$nombre || $precio <= 0) {
        responder_json(false, null, 'Nombre y precio son obligatorios', 400);
    }

    $stmt = $db->prepare(
        "INSERT INTO servicios (nombre, descripcion, precio, duracion_minutos, id_categoria, imagen)
         VALUES (?, ?, ?, ?, ?, ?)"
    );
    $stmt->execute([$nombre, $desc, $precio, $duracion, $cat ?: null, $imagen ?: null]);
    responder_json(true, ['id' => $db->lastInsertId()]);
}

// ── PUT (editar) ──
if ($method === 'PUT') {
    $id       = intval($input['id'] ?? 0);
    $nombre   = trim($input['nombre'] ?? '');
    $precio   = floatval($input['precio'] ?? 0);
    $duracion = intval($input['duracion_minutos'] ?? 30);
    $desc     = trim($input['descripcion'] ?? '');
    $cat      = $input['id_categoria'] ?? null;
    $imagen   = trim($input['imagen'] ?? '');

    if (!$id || !$nombre || $precio <= 0) {
        responder_json(false, null, 'Datos incompletos', 400);
    }

    $stmt = $db->prepare(
        "UPDATE servicios SET nombre=?, descripcion=?, precio=?, duracion_minutos=?, id_categoria=?, imagen=?
         WHERE id=?"
    );
    $stmt->execute([$nombre, $desc, $precio, $duracion, $cat ?: null, $imagen ?: null, $id]);
    responder_json(true);
}

// ── DELETE (soft) ──
if ($method === 'DELETE') {
    $id = intval($input['id'] ?? 0);
    if (!$id) responder_json(false, null, 'ID requerido', 400);

    $stmt = $db->prepare("UPDATE servicios SET activo = 0 WHERE id = ?");
    $stmt->execute([$id]);
    responder_json(true);
}

responder_json(false, null, 'Método no permitido', 405);
