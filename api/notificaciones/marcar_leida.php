<?php
/**
 * Piel Morena — API Notificaciones: Marcar como leida
 * PATCH { id } → Marca una notificacion como leida
 * PATCH { todas: true } → Marca todas como leidas
 */

require_once __DIR__ . '/../../includes/init.php';

if (!esta_autenticado()) {
    responder_json(false, null, 'No autorizado', 403);
}

if ($_SERVER['REQUEST_METHOD'] !== 'PATCH') {
    responder_json(false, null, 'Metodo no permitido', 405);
}

$db = getDB();
$id_usuario = usuario_actual_id();
$input = json_decode(file_get_contents('php://input'), true) ?: [];

if (!empty($input['todas'])) {
    // Marcar todas como leidas
    $stmt = $db->prepare("UPDATE notificaciones SET leida = 1 WHERE id_usuario = ? AND leida = 0");
    $stmt->execute([$id_usuario]);
    $actualizadas = $stmt->rowCount();

    responder_json(true, ['actualizadas' => $actualizadas]);
}

$id = intval($input['id'] ?? 0);

if (!$id) {
    responder_json(false, null, 'ID de notificacion requerido', 400);
}

// Marcar una especifica (solo si pertenece al usuario)
$stmt = $db->prepare("UPDATE notificaciones SET leida = 1 WHERE id = ? AND id_usuario = ?");
$stmt->execute([$id, $id_usuario]);

if ($stmt->rowCount() === 0) {
    responder_json(false, null, 'Notificacion no encontrada', 404);
}

responder_json(true, ['id' => $id]);
