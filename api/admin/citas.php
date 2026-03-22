<?php
/**
 * Piel Morena — API Admin: Gestión de Citas
 * GET   → Listar (?fecha=&estado=)
 * PATCH → Cambiar estado
 */

require_once __DIR__ . '/../../includes/init.php';

if (!esta_autenticado() || !tiene_rol('admin')) {
    responder_json(false, null, 'No autorizado', 403);
}

$db     = getDB();
$method = $_SERVER['REQUEST_METHOD'];

// ── GET ──
if ($method === 'GET') {
    $where  = [];
    $params = [];

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
            JOIN usuarios u  ON c.id_cliente = u.id";

    if ($where) {
        $sql .= ' WHERE ' . implode(' AND ', $where);
    }
    $sql .= ' ORDER BY c.fecha DESC, c.hora_inicio ASC';

    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    responder_json(true, $stmt->fetchAll());
}

// ── PATCH (cambiar estado) ──
if ($method === 'PATCH') {
    $input  = json_decode(file_get_contents('php://input'), true) ?: [];
    $id     = intval($input['id'] ?? 0);
    $estado = $input['estado'] ?? '';

    $validos = ['pendiente', 'confirmada', 'en_proceso', 'completada', 'cancelada'];
    if (!$id || !in_array($estado, $validos)) {
        responder_json(false, null, 'ID y estado válido son requeridos', 400);
    }

    $stmt = $db->prepare("UPDATE citas SET estado = ? WHERE id = ?");
    $stmt->execute([$estado, $id]);

    // Obtener datos de la cita (servicio + cliente) — se usa para caja y notificaciones
    $stmt = $db->prepare(
        "SELECT c.fecha, c.hora_inicio, s.nombre AS servicio, s.precio,
                u.id AS id_cliente, u.email AS cliente_email,
                CONCAT(u.nombre, ' ', u.apellidos) AS cliente
         FROM citas c
         JOIN servicios s ON c.id_servicio = s.id
         JOIN usuarios u  ON c.id_cliente = u.id
         WHERE c.id = ?"
    );
    $stmt->execute([$id]);
    $cita = $stmt->fetch();

    $caja_registrada = false;

    // Auto-registro en caja al completar cita
    if ($estado === 'completada' && $cita && $cita['precio'] > 0) {
        $stmt = $db->prepare("SELECT COUNT(*) FROM caja_movimientos WHERE id_cita = ?");
        $stmt->execute([$id]);
        $ya_registrada = (int) $stmt->fetchColumn() > 0;

        if (!$ya_registrada) {
            $concepto = 'Servicio: ' . $cita['servicio'] . ' — Cliente: ' . $cita['cliente'];
            $stmt = $db->prepare(
                "INSERT INTO caja_movimientos (tipo, monto, concepto, metodo_pago, id_cita, id_usuario, fecha)
                 VALUES ('entrada', ?, ?, 'efectivo', ?, ?, ?)"
            );
            $stmt->execute([
                $cita['precio'],
                $concepto,
                $id,
                usuario_actual_id(),
                $cita['fecha']
            ]);
            $caja_registrada = true;
        }
    }

    // Enviar notificaciones por email segun el nuevo estado
    if ($cita) {
        try {
            $fecha_fmt = date('d/m/Y', strtotime($cita['fecha']));
            $vars_email = [
                'cliente_nombre' => $cita['cliente'],
                'servicio'       => $cita['servicio'],
                'fecha'          => $fecha_fmt,
                'hora'           => substr($cita['hora_inicio'], 0, 5),
            ];

            if ($estado === 'confirmada') {
                enviar_email($cita['cliente_email'], 'Tu cita fue confirmada — Piel Morena', 'cita_confirmada_admin', $vars_email);
                registrar_notificacion($cita['id_cliente'], 'sistema', 'Cita confirmada', 'Tu cita de ' . $cita['servicio'] . ' para el ' . $fecha_fmt . ' fue confirmada.');
            } elseif ($estado === 'cancelada') {
                $vars_email['cancelado_por'] = 'salon';
                enviar_email($cita['cliente_email'], 'Tu cita fue cancelada — Piel Morena', 'cancelacion_cita', $vars_email);
                registrar_notificacion($cita['id_cliente'], 'sistema', 'Cita cancelada', 'Tu cita de ' . $cita['servicio'] . ' para el ' . $fecha_fmt . ' fue cancelada por el salon.');
            }
        } catch (Exception $e) {
            error_log('Piel Morena Mail Error (admin citas): ' . $e->getMessage());
        }
    }

    responder_json(true, ['caja_registrada' => $caja_registrada]);
}

responder_json(false, null, 'Método no permitido', 405);
