<?php
/**
 * Piel Morena — API Admin: Mensajes de Contacto
 * GET    → Listar / obtener uno (?id=X)
 * PATCH  → Marcar como leído
 * DELETE → Eliminar
 */

require_once __DIR__ . '/../../includes/init.php';

if (!esta_autenticado() || !tiene_rol('admin')) {
    responder_json(false, null, 'No autorizado', 403);
}

$db     = getDB();
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    if (!empty($_GET['id'])) {
        $stmt = $db->prepare("SELECT * FROM contacto_mensajes WHERE id = ?");
        $stmt->execute([$_GET['id']]);
        $msg = $stmt->fetch();
        if (!$msg) responder_json(false, null, 'Mensaje no encontrado', 404);
        responder_json(true, $msg);
    }

    $stmt = $db->query("SELECT * FROM contacto_mensajes ORDER BY leido ASC, created_at DESC");
    responder_json(true, $stmt->fetchAll());
}

$input = json_decode(file_get_contents('php://input'), true) ?: [];

if ($method === 'PATCH') {
    $id    = intval($input['id'] ?? 0);
    $leido = intval($input['leido'] ?? 1);
    if (!$id) responder_json(false, null, 'ID requerido', 400);

    $stmt = $db->prepare("UPDATE contacto_mensajes SET leido = ? WHERE id = ?");
    $stmt->execute([$leido, $id]);
    responder_json(true);
}

if ($method === 'DELETE') {
    $id = intval($input['id'] ?? 0);
    if (!$id) responder_json(false, null, 'ID requerido', 400);

    $stmt = $db->prepare("DELETE FROM contacto_mensajes WHERE id = ?");
    $stmt->execute([$id]);
    responder_json(true);
}

responder_json(false, null, 'Método no permitido', 405);
