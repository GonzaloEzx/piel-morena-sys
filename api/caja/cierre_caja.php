<?php
/**
 * Piel Morena — API Caja: Cierre de Caja Diario
 * GET  → Listar cierres (?fecha_desde=&fecha_hasta=)
 * POST → Crear cierre del día
 */

require_once __DIR__ . '/../../includes/init.php';

if (!esta_autenticado() || !tiene_rol('admin')) {
    responder_json(false, null, 'No autorizado', 403);
}

$db     = getDB();
$method = $_SERVER['REQUEST_METHOD'];

// ── GET: Listar cierres ──
if ($method === 'GET') {
    $where  = [];
    $params = [];

    if (!empty($_GET['fecha_desde'])) {
        $where[]  = 'cc.fecha >= ?';
        $params[] = $_GET['fecha_desde'];
    }
    if (!empty($_GET['fecha_hasta'])) {
        $where[]  = 'cc.fecha <= ?';
        $params[] = $_GET['fecha_hasta'];
    }

    $sql = "SELECT cc.*, CONCAT(u.nombre, ' ', u.apellidos) AS usuario
            FROM cierres_caja cc
            JOIN usuarios u ON cc.id_usuario = u.id";

    if ($where) {
        $sql .= ' WHERE ' . implode(' AND ', $where);
    }
    $sql .= ' ORDER BY cc.fecha DESC LIMIT 30';

    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    responder_json(true, $stmt->fetchAll());
}

// ── POST: Crear cierre ──
if ($method === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true) ?: [];
    $fecha = $input['fecha'] ?? date('Y-m-d');
    $notas = trim($input['notas'] ?? '');

    // Verificar que no exista cierre para esa fecha
    $stmt = $db->prepare("SELECT id FROM cierres_caja WHERE fecha = ?");
    $stmt->execute([$fecha]);
    if ($stmt->fetch()) {
        responder_json(false, null, 'Ya existe un cierre de caja para la fecha ' . $fecha, 409);
    }

    // Calcular totales del día
    $stmt = $db->prepare("SELECT COALESCE(SUM(monto), 0) FROM caja_movimientos WHERE fecha = ? AND tipo = 'entrada'");
    $stmt->execute([$fecha]);
    $total_entradas = (float) $stmt->fetchColumn();

    $stmt = $db->prepare("SELECT COALESCE(SUM(monto), 0) FROM caja_movimientos WHERE fecha = ? AND tipo = 'salida'");
    $stmt->execute([$fecha]);
    $total_salidas = (float) $stmt->fetchColumn();

    $saldo = $total_entradas - $total_salidas;

    // Insertar cierre
    $stmt = $db->prepare(
        "INSERT INTO cierres_caja (fecha, total_entradas, total_salidas, saldo, id_usuario, notas)
         VALUES (?, ?, ?, ?, ?, ?)"
    );
    $stmt->execute([$fecha, $total_entradas, $total_salidas, $saldo, usuario_actual_id(), $notas]);

    responder_json(true, [
        'id'              => $db->lastInsertId(),
        'fecha'           => $fecha,
        'total_entradas'  => $total_entradas,
        'total_salidas'   => $total_salidas,
        'saldo'           => $saldo
    ]);
}

responder_json(false, null, 'Método no permitido', 405);
