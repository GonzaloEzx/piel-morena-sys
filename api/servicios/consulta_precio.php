<?php
/**
 * Piel Morena - API: Registrar consulta de precio
 * POST { id_servicio } → JSON { success }
 * Cada clic en el tooltip $ de un servicio registra +1 para analytics
 */

require_once __DIR__ . '/../../includes/init.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    responder_json(false, null, 'Método no permitido', 405);
}

$input = json_decode(file_get_contents('php://input'), true);

$id_servicio = intval($input['id_servicio'] ?? 0);

if ($id_servicio <= 0) {
    responder_json(false, null, 'ID de servicio inválido', 400);
}

$db = getDB();

// Verificar que el servicio existe y está activo
$stmt = $db->prepare("SELECT id FROM servicios WHERE id = ? AND activo = 1 LIMIT 1");
$stmt->execute([$id_servicio]);
if (!$stmt->fetch()) {
    responder_json(false, null, 'Servicio no encontrado', 404);
}

// Registrar la consulta
$ip = $_SERVER['REMOTE_ADDR'] ?? '';
$ua = substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 500);

$stmt = $db->prepare("INSERT INTO consultas_precio (id_servicio, ip_visitante, user_agent) VALUES (?, ?, ?)");
$stmt->execute([$id_servicio, $ip, $ua]);

responder_json(true);
