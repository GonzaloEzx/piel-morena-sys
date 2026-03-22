<?php
/**
 * Piel Morena — API Caja: Listar Movimientos
 * GET → Lista movimientos con filtros de fecha y tipo
 */

require_once __DIR__ . '/../../includes/init.php';

if (!esta_autenticado() || !tiene_rol('admin')) {
    responder_json(false, null, 'No autorizado', 403);
}

$db     = getDB();
$method = $_SERVER['REQUEST_METHOD'];

if ($method !== 'GET') {
    responder_json(false, null, 'Método no permitido', 405);
}

$where  = [];
$params = [];

// Filtro por rango de fechas
if (!empty($_GET['fecha_desde'])) {
    $where[]  = 'cm.fecha >= ?';
    $params[] = $_GET['fecha_desde'];
}
if (!empty($_GET['fecha_hasta'])) {
    $where[]  = 'cm.fecha <= ?';
    $params[] = $_GET['fecha_hasta'];
}

// Filtro por tipo
if (!empty($_GET['tipo']) && in_array($_GET['tipo'], ['entrada', 'salida'])) {
    $where[]  = 'cm.tipo = ?';
    $params[] = $_GET['tipo'];
}

// Paginacion
$limit  = min(intval($_GET['limit'] ?? 50), 200);
$offset = max(intval($_GET['offset'] ?? 0), 0);

$sql = "SELECT cm.*, CONCAT(u.nombre, ' ', u.apellidos) AS usuario
        FROM caja_movimientos cm
        JOIN usuarios u ON cm.id_usuario = u.id";

if ($where) {
    $sql .= ' WHERE ' . implode(' AND ', $where);
}
$sql .= ' ORDER BY cm.fecha DESC, cm.created_at DESC';
$sql .= " LIMIT $limit OFFSET $offset";

$stmt = $db->prepare($sql);
$stmt->execute($params);
$movimientos = $stmt->fetchAll();

// Total count para paginacion
$sqlCount = "SELECT COUNT(*) FROM caja_movimientos cm";
if ($where) {
    $sqlCount .= ' WHERE ' . implode(' AND ', $where);
}
$stmt = $db->prepare($sqlCount);
$stmt->execute($params);
$total = (int) $stmt->fetchColumn();

responder_json(true, [
    'movimientos' => $movimientos,
    'total'       => $total,
    'limit'       => $limit,
    'offset'      => $offset
]);
