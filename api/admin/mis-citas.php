<?php
/**
 * Piel Morena — API: Citas del empleado logueado
 * GET    → Listar citas asignadas (?fecha=&estado=)
 * PATCH  → Marcar cita como completada o en_proceso
 */

require_once __DIR__ . '/../../includes/init.php';

if (!esta_autenticado() || !tiene_rol('empleado')) {
    // Admin también puede acceder
    if (!tiene_rol('admin')) {
        responder_json(false, null, 'No autorizado', 403);
    }
}

$db     = getDB();
$method = $_SERVER['REQUEST_METHOD'];
$id_empleado = usuario_actual_id();

if ($method === 'GET') {
    $where  = ['c.id_empleado = ?'];
    $params = [$id_empleado];

    if (!empty($_GET['fecha'])) {
        $where[]  = 'c.fecha = ?';
        $params[] = $_GET['fecha'];
    }
    if (!empty($_GET['estado'])) {
        $where[]  = 'c.estado = ?';
        $params[] = $_GET['estado'];
    }

    $sql = "SELECT c.id, c.fecha, c.hora_inicio, c.hora_fin, c.estado, c.notas,
                   s.nombre AS servicio, s.precio,
                   CONCAT(u.nombre, ' ', u.apellidos) AS cliente, u.telefono AS cliente_tel
            FROM citas c
            JOIN servicios s ON c.id_servicio = s.id
            JOIN usuarios u  ON c.id_cliente = u.id
            WHERE " . implode(' AND ', $where) . "
            ORDER BY c.fecha ASC, c.hora_inicio ASC";

    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    responder_json(true, $stmt->fetchAll());
}

if ($method === 'PATCH') {
    $input  = json_decode(file_get_contents('php://input'), true) ?: [];
    $id     = intval($input['id'] ?? 0);
    $estado = $input['estado'] ?? '';

    // Empleados solo pueden pasar a en_proceso o completada
    $validos = ['en_proceso', 'completada'];
    if (!$id || !in_array($estado, $validos)) {
        responder_json(false, null, 'Solo puede marcar como "En proceso" o "Completada"', 400);
    }

    // Verificar que la cita esté asignada a este empleado
    $stmt = $db->prepare("SELECT id, id_servicio FROM citas WHERE id = ? AND id_empleado = ?");
    $stmt->execute([$id, $id_empleado]);
    $cita = $stmt->fetch();
    if (!$cita) {
        responder_json(false, null, 'Cita no encontrada o no asignada a usted', 404);
    }

    $stmt = $db->prepare("UPDATE citas SET estado = ? WHERE id = ?");
    $stmt->execute([$estado, $id]);

    $caja_registrada = false;

    // Auto-registro en caja al completar
    if ($estado === 'completada') {
        $stmt = $db->prepare(
            "SELECT s.precio, s.nombre AS servicio, CONCAT(u.nombre, ' ', u.apellidos) AS cliente, c.fecha
             FROM citas c
             JOIN servicios s ON c.id_servicio = s.id
             JOIN usuarios u ON c.id_cliente = u.id
             WHERE c.id = ?"
        );
        $stmt->execute([$id]);
        $datos = $stmt->fetch();

        if ($datos && $datos['precio'] > 0) {
            $stmt = $db->prepare("SELECT COUNT(*) FROM caja_movimientos WHERE id_cita = ?");
            $stmt->execute([$id]);
            if ((int) $stmt->fetchColumn() === 0) {
                $concepto = 'Servicio: ' . $datos['servicio'] . ' — Cliente: ' . $datos['cliente'];
                $stmt = $db->prepare(
                    "INSERT INTO caja_movimientos (tipo, monto, concepto, metodo_pago, id_cita, id_usuario, fecha)
                     VALUES ('entrada', ?, ?, 'efectivo', ?, ?, ?)"
                );
                $stmt->execute([$datos['precio'], $concepto, $id, $id_empleado, $datos['fecha']]);
                $caja_registrada = true;
            }
        }
    }

    responder_json(true, ['caja_registrada' => $caja_registrada]);
}

responder_json(false, null, 'Método no permitido', 405);
