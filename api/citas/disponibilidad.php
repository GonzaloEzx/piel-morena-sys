<?php
/**
 * Piel Morena - API: Disponibilidad de turnos
 * GET ?fecha=YYYY-MM-DD&id_servicio=X → JSON { success, data: { fecha, servicio, turnos[] } }
 *
 * Genera slots disponibles según horario del negocio y duración del servicio,
 * excluyendo los ya reservados (pendiente/confirmada/en_proceso).
 */

require_once __DIR__ . '/../../includes/init.php';
require_once __DIR__ . '/../../includes/agenda.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    responder_json(false, null, 'Método no permitido', 405);
}

$fecha       = trim($_GET['fecha'] ?? '');
$id_servicio = intval($_GET['id_servicio'] ?? 0);

// Validaciones
if (!$fecha || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha)) {
    responder_json(false, null, 'Fecha inválida. Formato: YYYY-MM-DD', 400);
}

$fecha_ts = strtotime($fecha);
if ($fecha_ts === false || $fecha_ts < strtotime('today')) {
    responder_json(false, null, 'La fecha debe ser hoy o posterior', 400);
}

if ($id_servicio <= 0) {
    responder_json(false, null, 'ID de servicio inválido', 400);
}

$db = getDB();

// Obtener servicio
$stmt = $db->prepare("SELECT id, nombre, duracion_minutos, precio FROM servicios WHERE id = ? AND activo = 1 LIMIT 1");
$stmt->execute([$id_servicio]);
$servicio = $stmt->fetch();

if (!$servicio) {
    responder_json(false, null, 'Servicio no encontrado', 404);
}

$dia = pm_turnos_del_dia($db, $servicio, $fecha);

$respuesta = [
    'fecha'    => $fecha,
    'servicio' => $servicio['nombre'],
    'precio'   => $servicio['precio'],
    'duracion' => $servicio['duracion_minutos'],
    'turnos'   => array_values(array_map(
        fn($t) => ['hora_inicio' => $t['hora_inicio'], 'hora_fin' => $t['hora_fin']],
        array_filter($dia['turnos'], fn($t) => $t['libre'])
    )),
];
if ($dia['mensaje'] !== null) {
    $respuesta['mensaje'] = $dia['mensaje'];
}

responder_json(true, $respuesta);
