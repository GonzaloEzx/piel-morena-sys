<?php
/**
 * Piel Morena — API Analytics: Ingresos
 * GET ?fecha_desde=&fecha_hasta=&agrupar=(dia|semana|mes) → JSON ingresos agrupados
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
$agrupar     = $_GET['agrupar'] ?? 'dia';

// Expresión de agrupación SQL
switch ($agrupar) {
    case 'semana':
        $group_expr = "YEARWEEK(fecha, 1)";
        $select_fecha = "DATE(DATE_ADD(fecha, INTERVAL(1 - DAYOFWEEK(fecha)) DAY)) AS periodo";
        break;
    case 'mes':
        $group_expr = "DATE_FORMAT(fecha, '%Y-%m')";
        $select_fecha = "DATE_FORMAT(fecha, '%Y-%m') AS periodo";
        break;
    default: // dia
        $group_expr = "fecha";
        $select_fecha = "fecha AS periodo";
        break;
}

// Ingresos por período (entradas de caja)
$stmt = $db->prepare(
    "SELECT {$select_fecha},
            SUM(CASE WHEN tipo = 'entrada' THEN monto ELSE 0 END) AS ingresos,
            SUM(CASE WHEN tipo = 'salida' THEN monto ELSE 0 END) AS egresos,
            COUNT(*) AS movimientos
     FROM caja_movimientos
     WHERE fecha >= ? AND fecha <= ?
     GROUP BY {$group_expr}
     ORDER BY periodo ASC"
);
$stmt->execute([$fecha_desde, $fecha_hasta]);
$por_periodo = $stmt->fetchAll();

// Totales del rango
$stmt = $db->prepare(
    "SELECT COALESCE(SUM(CASE WHEN tipo = 'entrada' THEN monto ELSE 0 END), 0) AS total_ingresos,
            COALESCE(SUM(CASE WHEN tipo = 'salida' THEN monto ELSE 0 END), 0) AS total_egresos,
            COUNT(*) AS total_movimientos
     FROM caja_movimientos
     WHERE fecha >= ? AND fecha <= ?"
);
$stmt->execute([$fecha_desde, $fecha_hasta]);
$totales = $stmt->fetch();

// Ingresos por método de pago
$stmt = $db->prepare(
    "SELECT metodo_pago, SUM(monto) AS total
     FROM caja_movimientos
     WHERE tipo = 'entrada' AND fecha >= ? AND fecha <= ?
     GROUP BY metodo_pago
     ORDER BY total DESC"
);
$stmt->execute([$fecha_desde, $fecha_hasta]);
$por_metodo = $stmt->fetchAll();

// Ingresos por servicio (citas completadas)
$stmt = $db->prepare(
    "SELECT s.nombre, COUNT(c.id) AS citas, SUM(s.precio) AS total
     FROM citas c
     JOIN servicios s ON c.id_servicio = s.id
     WHERE c.estado = 'completada' AND c.fecha >= ? AND c.fecha <= ?
     GROUP BY c.id_servicio
     ORDER BY total DESC
     LIMIT 10"
);
$stmt->execute([$fecha_desde, $fecha_hasta]);
$por_servicio = $stmt->fetchAll();

responder_json(true, [
    'por_periodo'   => $por_periodo,
    'totales'       => $totales,
    'por_metodo'    => $por_metodo,
    'por_servicio'  => $por_servicio,
    'fecha_desde'   => $fecha_desde,
    'fecha_hasta'   => $fecha_hasta,
    'agrupar'       => $agrupar
]);
