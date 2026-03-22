<?php
/**
 * Piel Morena — API Caja: Resumen por Período
 * GET → Totales, desglose por método de pago y por día
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

$fecha_desde = $_GET['fecha_desde'] ?? date('Y-m-d');
$fecha_hasta = $_GET['fecha_hasta'] ?? date('Y-m-d');

$params = [$fecha_desde, $fecha_hasta];

// Totales generales
$stmt = $db->prepare(
    "SELECT COALESCE(SUM(CASE WHEN tipo = 'entrada' THEN monto ELSE 0 END), 0) AS total_entradas,
            COALESCE(SUM(CASE WHEN tipo = 'salida'  THEN monto ELSE 0 END), 0) AS total_salidas,
            COUNT(*) AS cantidad_movimientos
     FROM caja_movimientos
     WHERE fecha BETWEEN ? AND ?"
);
$stmt->execute($params);
$totales = $stmt->fetch();
$totales['saldo'] = $totales['total_entradas'] - $totales['total_salidas'];

// Desglose por método de pago
$stmt = $db->prepare(
    "SELECT metodo_pago,
            COALESCE(SUM(CASE WHEN tipo = 'entrada' THEN monto ELSE 0 END), 0) AS entradas,
            COALESCE(SUM(CASE WHEN tipo = 'salida'  THEN monto ELSE 0 END), 0) AS salidas,
            COUNT(*) AS cantidad
     FROM caja_movimientos
     WHERE fecha BETWEEN ? AND ?
     GROUP BY metodo_pago
     ORDER BY entradas DESC"
);
$stmt->execute($params);
$por_metodo = $stmt->fetchAll();

// Desglose por día
$stmt = $db->prepare(
    "SELECT fecha,
            COALESCE(SUM(CASE WHEN tipo = 'entrada' THEN monto ELSE 0 END), 0) AS entradas,
            COALESCE(SUM(CASE WHEN tipo = 'salida'  THEN monto ELSE 0 END), 0) AS salidas,
            COUNT(*) AS cantidad
     FROM caja_movimientos
     WHERE fecha BETWEEN ? AND ?
     GROUP BY fecha
     ORDER BY fecha DESC"
);
$stmt->execute($params);
$por_dia = $stmt->fetchAll();

responder_json(true, [
    'periodo' => [
        'desde' => $fecha_desde,
        'hasta' => $fecha_hasta
    ],
    'totales'    => $totales,
    'por_metodo' => $por_metodo,
    'por_dia'    => $por_dia
]);
