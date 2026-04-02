<?php
/**
 * Piel Morena — API Admin: CRUD Testimonios
 * GET    → Listar / obtener uno (?id=X)
 * POST   → Crear
 * PUT    → Editar
 * DELETE → Eliminar definitivamente
 */

require_once __DIR__ . '/../../includes/init.php';

if (!esta_autenticado() || !tiene_rol('admin')) {
    responder_json(false, null, 'No autorizado', 403);
}

$db = getDB();
$method = $_SERVER['REQUEST_METHOD'];

function siguiente_orden_testimonio(PDO $db): int {
    $stmt = $db->query("SELECT COALESCE(MAX(orden), 0) + 1 FROM testimonios");
    return (int) $stmt->fetchColumn();
}

if ($method === 'GET') {
    if (!empty($_GET['id'])) {
        $stmt = $db->prepare("SELECT * FROM testimonios WHERE id = ? LIMIT 1");
        $stmt->execute([intval($_GET['id'])]);
        $testimonio = $stmt->fetch();

        if (!$testimonio) {
            responder_json(false, null, 'Testimonio no encontrado', 404);
        }

        responder_json(true, $testimonio);
    }

    $stmt = $db->query("SELECT * FROM testimonios ORDER BY orden ASC, id ASC");
    responder_json(true, $stmt->fetchAll());
}

$input = json_decode(file_get_contents('php://input'), true) ?: [];

if ($method === 'POST') {
    $nombre = trim($input['nombre'] ?? '');
    $rol = trim($input['rol'] ?? '');
    $texto = trim($input['texto'] ?? '');
    $orden = intval($input['orden'] ?? 0);

    if ($nombre === '' || $texto === '') {
        responder_json(false, null, 'Nombre y testimonio son obligatorios', 400);
    }

    if ($orden <= 0) {
        $orden = siguiente_orden_testimonio($db);
    }

    $stmt = $db->prepare(
        "INSERT INTO testimonios (nombre, rol, texto, orden)
         VALUES (?, ?, ?, ?)"
    );
    $stmt->execute([$nombre, $rol ?: null, $texto, $orden]);

    responder_json(true, ['id' => $db->lastInsertId()]);
}

if ($method === 'PUT') {
    $id = intval($input['id'] ?? 0);
    $nombre = trim($input['nombre'] ?? '');
    $rol = trim($input['rol'] ?? '');
    $texto = trim($input['texto'] ?? '');
    $orden = intval($input['orden'] ?? 0);

    if ($id <= 0 || $nombre === '' || $texto === '') {
        responder_json(false, null, 'Datos incompletos', 400);
    }

    if ($orden <= 0) {
        $orden = 1;
    }

    $stmt = $db->prepare("SELECT id FROM testimonios WHERE id = ? LIMIT 1");
    $stmt->execute([$id]);
    if (!$stmt->fetch()) {
        responder_json(false, null, 'Testimonio no encontrado', 404);
    }

    $stmt = $db->prepare(
        "UPDATE testimonios
         SET nombre = ?, rol = ?, texto = ?, orden = ?
         WHERE id = ?"
    );
    $stmt->execute([$nombre, $rol ?: null, $texto, $orden, $id]);

    responder_json(true);
}

if ($method === 'DELETE') {
    $id = intval($input['id'] ?? 0);
    if ($id <= 0) {
        responder_json(false, null, 'ID requerido', 400);
    }

    $stmt = $db->prepare("SELECT id FROM testimonios WHERE id = ? LIMIT 1");
    $stmt->execute([$id]);
    if (!$stmt->fetch()) {
        responder_json(false, null, 'Testimonio no encontrado', 404);
    }

    $stmt = $db->prepare("DELETE FROM testimonios WHERE id = ?");
    $stmt->execute([$id]);

    responder_json(true);
}

responder_json(false, null, 'Método no permitido', 405);
