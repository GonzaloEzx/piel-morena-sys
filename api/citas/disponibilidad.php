<?php
/**
 * Piel Morena - API: Disponibilidad de turnos
 * GET ?fecha=YYYY-MM-DD&id_servicio=X[&id_empleado=Y][&t=token]
 *   → JSON { success, data: { fecha, servicio, precio, duracion, turnos[] } }
 *
 * Cada turno trae `profesionales`: ids de las profesionales libres en esa
 * franja (vacío si el servicio no tiene profesionales asignadas).
 * Con `t` (token de gestión) la cita de esa clienta no cuenta como ocupada,
 * para que pueda reprogramarla.
 */

require_once __DIR__ . '/../../includes/init.php';
require_once __DIR__ . '/../../includes/agenda.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    responder_json(false, null, 'Método no permitido', 405);
}

$fecha       = trim($_GET['fecha'] ?? '');
$id_servicio = intval($_GET['id_servicio'] ?? 0);
$id_empleado = intval($_GET['id_empleado'] ?? 0) ?: null;
$token       = trim($_GET['t'] ?? '');

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

$excluir = 0;
if (preg_match('/^[0-9a-f]{32}$/', $token)) {
    $stmt = $db->prepare("SELECT id FROM citas WHERE token_gestion = ? LIMIT 1");
    $stmt->execute([$token]);
    $excluir = (int) $stmt->fetchColumn();
}

$dia = pm_turnos_del_dia($db, $servicio, $fecha, [], $id_empleado, $excluir);

$respuesta = [
    'fecha'    => $fecha,
    'servicio' => $servicio['nombre'],
    'precio'   => $servicio['precio'],
    'duracion' => $servicio['duracion_minutos'],
    'turnos'   => array_values(array_map(
        fn($t) => ['hora_inicio' => $t['hora_inicio'], 'hora_fin' => $t['hora_fin'], 'profesionales' => $t['profesionales']],
        array_filter($dia['turnos'], fn($t) => $t['libre'])
    )),
];
if ($dia['mensaje'] !== null) {
    $respuesta['mensaje'] = $dia['mensaje'];
}

responder_json(true, $respuesta);
