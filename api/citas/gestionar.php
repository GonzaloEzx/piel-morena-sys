<?php
/**
 * Piel Morena - API: Gestión del turno por la clienta (sin cuenta)
 * GET  ?t=token → JSON { success, data: { cita } }
 * POST { t, accion: 'cancelar' }
 * POST { t, accion: 'reprogramar', fecha, hora_inicio, id_empleado? }
 *
 * El token llega en el link de la confirmación. Cancelar o cambiar el
 * horario se permite hasta HORAS_LIMITE antes del turno.
 */

require_once __DIR__ . '/../../includes/init.php';
require_once __DIR__ . '/../../includes/agenda.php';

const HORAS_LIMITE = 2;

$db = getDB();
$metodo = $_SERVER['REQUEST_METHOD'];
$input = $metodo === 'POST' ? (json_decode(file_get_contents('php://input'), true) ?: []) : $_GET;

$token = trim($input['t'] ?? '');
if (!preg_match('/^[0-9a-f]{32}$/', $token)) {
    responder_json(false, null, 'Link inválido', 400);
}

$stmt = $db->prepare(
    "SELECT c.id, c.id_servicio, c.id_empleado, c.id_cliente, c.fecha, c.hora_inicio, c.hora_fin, c.estado,
            s.nombre AS servicio, s.precio, s.duracion_minutos,
            u.nombre AS cliente, u.email AS cliente_email,
            e.nombre AS profesional
     FROM citas c
     JOIN servicios s ON s.id = c.id_servicio
     JOIN usuarios u ON u.id = c.id_cliente
     LEFT JOIN usuarios e ON e.id = c.id_empleado
     WHERE c.token_gestion = ? LIMIT 1"
);
$stmt->execute([$token]);
$cita = $stmt->fetch();
if (!$cita) {
    responder_json(false, null, 'No encontramos ese turno', 404);
}

$es_modificable = fn(array $c) => in_array($c['estado'], ['pendiente', 'confirmada'], true)
    && strtotime($c['fecha'] . ' ' . $c['hora_inicio']) - time() >= HORAS_LIMITE * 3600;

$publica = fn(array $c) => [
    'servicio'    => $c['servicio'],
    'id_servicio' => (int) $c['id_servicio'],
    'precio'      => $c['precio'],
    'fecha'       => $c['fecha'],
    'hora_inicio' => substr($c['hora_inicio'], 0, 5),
    'hora_fin'    => substr($c['hora_fin'], 0, 5),
    'estado'      => $c['estado'],
    'profesional' => $c['profesional'],
    'cliente'     => $c['cliente'],
    'modificable' => $es_modificable($c),
    'horas_limite'=> HORAS_LIMITE,
];

if ($metodo === 'GET') {
    responder_json(true, $publica($cita));
}
if ($metodo !== 'POST') {
    responder_json(false, null, 'Método no permitido', 405);
}

if (!$es_modificable($cita)) {
    responder_json(false, null, 'Este turno ya no se puede modificar online. Escribinos por WhatsApp.', 409);
}

$accion = $input['accion'] ?? '';
$fecha_fmt = date('d/m/Y', strtotime($cita['fecha']));

if ($accion === 'cancelar') {
    $db->prepare("UPDATE citas SET estado = 'cancelada' WHERE id = ?")->execute([$cita['id']]);
    try {
        if ($cita['cliente_email']) {
            enviar_email($cita['cliente_email'], 'Cancelaste tu turno — Piel Morena', 'cancelacion_cita', [
                'cliente_nombre' => $cita['cliente'],
                'servicio'       => $cita['servicio'],
                'fecha'          => $fecha_fmt,
                'hora'           => substr($cita['hora_inicio'], 0, 5),
                'cancelado_por'  => 'cliente',
            ]);
        }
    } catch (Throwable $e) {
        error_log('Piel Morena Mail Error (cancelar por clienta): ' . $e->getMessage());
    }
    $cita['estado'] = 'cancelada';
    responder_json(true, $publica($cita));
}

if ($accion === 'reprogramar') {
    $fecha = trim($input['fecha'] ?? '');
    $hora  = trim($input['hora_inicio'] ?? '');
    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha) || !preg_match('/^\d{2}:\d{2}$/', $hora)
        || strtotime($fecha) < strtotime('today') || strtotime($fecha) > strtotime('+60 days')) {
        responder_json(false, null, 'Elegí un día y un horario válidos', 400);
    }

    $r = pm_reservar($db, [
        'id_servicio' => (int) $cita['id_servicio'],
        'fecha'       => $fecha,
        'hora_inicio' => $hora,
        'id_empleado' => intval($input['id_empleado'] ?? 0) ?: null,
        'id_cita'     => (int) $cita['id'],
    ]);
    if (!$r['ok']) {
        responder_json(false, null, $r['error'], $r['codigo']);
    }

    $stmt->execute([$token]);
    $cita = $stmt->fetch();
    registrar_notificacion((int) $cita['id_cliente'], 'sistema', 'Turno reprogramado',
        'Tu turno de ' . $cita['servicio'] . ' pasó al ' . date('d/m/Y', strtotime($fecha)) . ' a las ' . $hora . '. Queda pendiente de confirmación.');
    responder_json(true, $publica($cita));
}

responder_json(false, null, 'Acción inválida', 400);
