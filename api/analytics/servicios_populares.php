<?php
/**
 * Piel Morena — API Analytics: Servicios Populares
 * GET ?fecha_desde=&fecha_hasta= → JSON top servicios por consultas de precio
 */

require_once __DIR__ . '/../../includes/init.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    responder_json(false, null, 'Método no permitido', 405);
}

if (!esta_autenticado() || !tiene_rol('admin')) {
    responder_json(false, null, 'No autorizado', 403);
}

$db = getDB();

$fecha_desde = $_GET['fecha_desde'] ?? date('Y-m-d', strtotime('-30 days'));
$fecha_hasta = $_GET['fecha_hasta'] ?? date('Y-m-d');
$limit       = min((int) ($_GET['limit'] ?? 10), 50);

// Top servicios por consultas de precio
$stmt = $db->prepare(
    "SELECT s.id, s.nombre, s.precio, COUNT(cp.id) AS consultas
     FROM consultas_precio cp
     JOIN servicios s ON cp.id_servicio = s.id
     WHERE DATE(cp.created_at) >= ? AND DATE(cp.created_at) <= ?
     GROUP BY cp.id_servicio
     ORDER BY consultas DESC
     LIMIT ?"
);
$stmt->execute([$fecha_desde, $fecha_hasta, $limit]);
$top_servicios = $stmt->fetchAll();

// Total de consultas en el rango
$stmt = $db->prepare(
    "SELECT COUNT(*) FROM consultas_precio
     WHERE DATE(created_at) >= ? AND DATE(created_at) <= ?"
);
$stmt->execute([$fecha_desde, $fecha_hasta]);
$total_consultas = (int) $stmt->fetchColumn();

// Consultas por día (para gráfico de línea)
$stmt = $db->prepare(
    "SELECT DATE(cp.created_at) AS fecha, COUNT(*) AS consultas
     FROM consultas_precio cp
     WHERE DATE(cp.created_at) >= ? AND DATE(cp.created_at) <= ?
     GROUP BY DATE(cp.created_at)
     ORDER BY fecha ASC"
);
$stmt->execute([$fecha_desde, $fecha_hasta]);
$consultas_por_dia = $stmt->fetchAll();

responder_json(true, [
    'top_servicios'    => $top_servicios,
    'total_consultas'  => $total_consultas,
    'consultas_por_dia' => $consultas_por_dia,
    'fecha_desde'      => $fecha_desde,
    'fecha_hasta'      => $fecha_hasta
]);
