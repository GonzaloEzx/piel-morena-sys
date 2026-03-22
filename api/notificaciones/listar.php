<?php
/**
 * Piel Morena — API Notificaciones: Listar
 * GET → Notificaciones del usuario autenticado
 */

require_once __DIR__ . '/../../includes/init.php';

if (!esta_autenticado()) {
    responder_json(false, null, 'No autorizado', 403);
}

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    responder_json(false, null, 'Metodo no permitido', 405);
}

$db = getDB();
$id_usuario = usuario_actual_id();

$limit = min(intval($_GET['limit'] ?? 20), 100);
$solo_no_leidas = !empty($_GET['solo_no_leidas']);

$sql = "SELECT id, tipo, titulo, mensaje, leida, fecha_envio, created_at
        FROM notificaciones
        WHERE id_usuario = ?";

if ($solo_no_leidas) {
    $sql .= " AND leida = 0";
}

$sql .= " ORDER BY created_at DESC LIMIT ?";

$stmt = $db->prepare($sql);
$stmt->execute([$id_usuario, $limit]);
$notificaciones = $stmt->fetchAll();

// Total no leidas
$stmt = $db->prepare("SELECT COUNT(*) FROM notificaciones WHERE id_usuario = ? AND leida = 0");
$stmt->execute([$id_usuario]);
$total_no_leidas = (int) $stmt->fetchColumn();

responder_json(true, [
    'notificaciones'  => $notificaciones,
    'total_no_leidas' => $total_no_leidas,
]);
