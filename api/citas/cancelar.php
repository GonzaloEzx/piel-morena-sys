<?php
/**
 * Piel Morena - API: Cancelar cita
 * POST { id_cita } → JSON { success }
 *
 * Solo admin y empleados pueden cancelar citas.
 * Los clientes deben contactar al salón por teléfono.
 */

require_once __DIR__ . '/../../includes/init.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    responder_json(false, null, 'Método no permitido', 405);
}

// Solo staff autenticado puede cancelar
if (!esta_autenticado()) {
    responder_json(false, null, 'Debe iniciar sesión', 401);
}

if (!tiene_rol('admin') && !tiene_rol('empleado')) {
    responder_json(false, null, 'Solo el personal del salón puede cancelar citas. Para cancelar tu cita, contactanos por teléfono.', 403);
}

$input = json_decode(file_get_contents('php://input'), true);

$id_cita = intval($input['id_cita'] ?? 0);

if ($id_cita <= 0) {
    responder_json(false, null, 'ID de cita inválido', 400);
}

$db = getDB();

// Obtener la cita
$stmt = $db->prepare("SELECT id, id_cliente, estado FROM citas WHERE id = ? LIMIT 1");
$stmt->execute([$id_cita]);
$cita = $stmt->fetch();

if (!$cita) {
    responder_json(false, null, 'Cita no encontrada', 404);
}

if ($cita['estado'] === 'cancelada') {
    responder_json(false, null, 'La cita ya está cancelada', 400);
}

if ($cita['estado'] === 'completada') {
    responder_json(false, null, 'No se puede cancelar una cita completada', 400);
}

// Cancelar
$stmt = $db->prepare("UPDATE citas SET estado = 'cancelada', updated_at = NOW() WHERE id = ?");
$stmt->execute([$id_cita]);

// Enviar email de cancelacion (best-effort)
try {
    $stmt = $db->prepare(
        "SELECT c.fecha, c.hora_inicio, s.nombre AS servicio,
                u.id AS id_cliente, u.email, CONCAT(u.nombre, ' ', u.apellidos) AS cliente_nombre
         FROM citas c
         JOIN servicios s ON c.id_servicio = s.id
         JOIN usuarios u  ON c.id_cliente = u.id
         WHERE c.id = ?"
    );
    $stmt->execute([$id_cita]);
    $datos_cita = $stmt->fetch();

    if ($datos_cita && $datos_cita['email']) {
        $fecha_fmt = date('d/m/Y', strtotime($datos_cita['fecha']));
        enviar_email($datos_cita['email'], 'Tu cita fue cancelada — Piel Morena', 'cancelacion_cita', [
            'cliente_nombre' => $datos_cita['cliente_nombre'],
            'servicio'       => $datos_cita['servicio'],
            'fecha'          => $fecha_fmt,
            'hora'           => substr($datos_cita['hora_inicio'], 0, 5),
            'cancelado_por'  => 'staff',
        ]);
        registrar_notificacion($datos_cita['id_cliente'], 'sistema', 'Cita cancelada', 'Tu cita de ' . $datos_cita['servicio'] . ' para el ' . $fecha_fmt . ' fue cancelada.');
    }
} catch (Exception $e) {
    error_log('Piel Morena Mail Error (cancelar cita): ' . $e->getMessage());
}

responder_json(true, ['message' => 'Cita cancelada correctamente']);
