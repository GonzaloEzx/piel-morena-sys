<?php
/**
 * Piel Morena — API Admin: Control de Caja
 * GET  → Movimientos del día + resumen (?fecha=YYYY-MM-DD)
 * POST → Registrar movimiento
 */

require_once __DIR__ . '/../../includes/init.php';

if (!esta_autenticado() || !tiene_rol('admin')) {
    responder_json(false, null, 'No autorizado', 403);
}

$db     = getDB();
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    $fecha_desde = $_GET['fecha_desde'] ?? null;
    $fecha_hasta = $_GET['fecha_hasta'] ?? null;
    $fecha       = $_GET['fecha'] ?? null;

    // Determinar filtro de fecha
    if ($fecha_desde && $fecha_hasta) {
        $where_fecha = 'fecha BETWEEN ? AND ?';
        $params_fecha = [$fecha_desde, $fecha_hasta];
    } elseif ($fecha_desde) {
        $where_fecha = 'fecha >= ?';
        $params_fecha = [$fecha_desde];
    } elseif ($fecha_hasta) {
        $where_fecha = 'fecha <= ?';
        $params_fecha = [$fecha_hasta];
    } else {
        $dia = $fecha ?? date('Y-m-d');
        $where_fecha = 'fecha = ?';
        $params_fecha = [$dia];
    }

    // Movimientos
    $stmt = $db->prepare("SELECT * FROM caja_movimientos WHERE $where_fecha ORDER BY fecha DESC, created_at DESC");
    $stmt->execute($params_fecha);
    $movimientos = $stmt->fetchAll();

    // Resumen
    $stmt = $db->prepare("SELECT COALESCE(SUM(monto), 0) FROM caja_movimientos WHERE $where_fecha AND tipo = 'entrada'");
    $stmt->execute($params_fecha);
    $entradas = (float) $stmt->fetchColumn();

    $stmt = $db->prepare("SELECT COALESCE(SUM(monto), 0) FROM caja_movimientos WHERE $where_fecha AND tipo = 'salida'");
    $stmt->execute($params_fecha);
    $salidas = (float) $stmt->fetchColumn();

    responder_json(true, [
        'movimientos' => $movimientos,
        'resumen' => [
            'entradas' => $entradas,
            'salidas'  => $salidas,
            'saldo'    => $entradas - $salidas
        ]
    ]);
}

if ($method === 'POST') {
    $input     = json_decode(file_get_contents('php://input'), true) ?: [];
    $tipo      = $input['tipo'] ?? '';
    $concepto  = trim($input['concepto'] ?? '');
    $monto     = floatval($input['monto'] ?? 0);
    $metodo    = $input['metodo_pago'] ?? 'efectivo';

    if (!in_array($tipo, ['entrada', 'salida']) || !$concepto || $monto <= 0) {
        responder_json(false, null, 'Tipo, concepto y monto son obligatorios', 400);
    }

    $stmt = $db->prepare(
        "INSERT INTO caja_movimientos (tipo, monto, concepto, metodo_pago, id_usuario, fecha)
         VALUES (?, ?, ?, ?, ?, CURDATE())"
    );
    $stmt->execute([$tipo, $monto, $concepto, $metodo, usuario_actual_id()]);
    responder_json(true, ['id' => $db->lastInsertId()]);
}

responder_json(false, null, 'Método no permitido', 405);
