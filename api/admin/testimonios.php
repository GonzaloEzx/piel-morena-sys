<?php
/**
 * Piel Morena — API Admin: Testimonios
 * GET → Listar / obtener uno (?id=X)
 * PUT → Editar slot fijo
 */

require_once __DIR__ . '/../../includes/init.php';

if (!esta_autenticado() || !tiene_rol('admin')) {
    responder_json(false, null, 'No autorizado', 403);
}

$db = getDB();
$method = $_SERVER['REQUEST_METHOD'];

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

if ($method === 'PUT') {
    $id = intval($input['id'] ?? 0);
    $nombre = trim($input['nombre'] ?? '');
    $rol = trim($input['rol'] ?? '');
    $texto = trim($input['texto'] ?? '');
    $orden = intval($input['orden'] ?? 0);

    if ($id <= 0 || $nombre === '' || $texto === '') {
        responder_json(false, null, 'Datos incompletos', 400);
    }

    if ($orden < 1 || $orden > 6) {
        responder_json(false, null, 'Slot inválido', 400);
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

responder_json(false, null, 'Método no permitido', 405);
