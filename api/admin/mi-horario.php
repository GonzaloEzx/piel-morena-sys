<?php
/**
 * Piel Morena — API: Horario del empleado logueado
 * GET → Devuelve horarios semanales y servicios asignados
 */

require_once __DIR__ . '/../../includes/init.php';

if (!esta_autenticado()) {
    responder_json(false, null, 'No autorizado', 403);
}

if (!tiene_rol('empleado') && !tiene_rol('admin')) {
    responder_json(false, null, 'No autorizado', 403);
}

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    responder_json(false, null, 'Método no permitido', 405);
}

$db = getDB();
$id_empleado = usuario_actual_id();

// Horarios semanales
$stmt = $db->prepare(
    "SELECT id, dia_semana, hora_inicio, hora_fin, activo
     FROM horarios
     WHERE id_empleado = ?
     ORDER BY dia_semana, hora_inicio"
);
$stmt->execute([$id_empleado]);
$horarios = $stmt->fetchAll();

// Servicios asignados
$stmt = $db->prepare(
    "SELECT s.id, s.nombre, s.precio, s.duracion_minutos
     FROM empleados_servicios es
     JOIN servicios s ON es.id_servicio = s.id
     WHERE es.id_empleado = ? AND s.activo = 1
     ORDER BY s.nombre"
);
$stmt->execute([$id_empleado]);
$servicios = $stmt->fetchAll();

responder_json(true, [
    'horarios'  => $horarios,
    'servicios' => $servicios
]);
