<?php
/**
 * Piel Morena - API: Agenda pública de la landing
 * GET ?id_servicio=X (opcional; sin él, turnos genéricos del local)
 *   → JSON { success, data: { modo, servicio, dias[] } }
 *
 * modo 'vivo'    → cada turno trae `libre`: se cruzaron las citas del
 *                  sistema con el Google Calendar del salón.
 * modo 'horario' → no se pudo verificar la agenda: solo se informa la
 *                  franja de atención de cada día, sin turnos.
 *
 * Nunca devuelve quién reservó: solo horarios.
 */

require_once __DIR__ . '/../../includes/init.php';
require_once __DIR__ . '/../../includes/agenda.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    responder_json(false, null, 'Método no permitido', 405);
}

$id_servicio = intval($_GET['id_servicio'] ?? 0);
$db = getDB();

$servicio = ['id' => 0, 'nombre' => null, 'duracion_minutos' => 0];
if ($id_servicio > 0) {
    $stmt = $db->prepare("SELECT id, nombre, duracion_minutos FROM servicios WHERE id = ? AND activo = 1 LIMIT 1");
    $stmt->execute([$id_servicio]);
    $servicio = $stmt->fetch();
    if (!$servicio) {
        responder_json(false, null, 'Servicio no encontrado', 404);
    }
}

$desde = date('Y-m-d');
$hasta = date('Y-m-d', strtotime('+' . (AGENDA_DIAS - 1) . ' days'));

$ocupados = AGENDA_FUENTE === 'db' ? [] : pm_google_ocupados($desde, $hasta);
$modo = $ocupados === null ? 'horario' : 'vivo';

$dias = [];
for ($i = 0; $i < AGENDA_DIAS; $i++) {
    $fecha = date('Y-m-d', strtotime("$desde +$i days"));
    $dia = pm_turnos_del_dia($db, $servicio, $fecha, $ocupados ?? []);

    $dias[] = [
        'fecha'   => $fecha,
        'abierto' => $dia['turnos'] !== [],
        'franja'  => $dia['franja'] ? $dia['franja'][0] . ' a ' . $dia['franja'][1] : null,
        'turnos'  => $modo === 'vivo'
            ? array_map(fn($t) => ['hora' => $t['hora_inicio'], 'libre' => $t['libre']], $dia['turnos'])
            : [],
    ];
}

responder_json(true, [
    'modo'     => $modo,
    'servicio' => $servicio['id'] ? [
        'id'       => (int) $servicio['id'],
        'nombre'   => $servicio['nombre'],
        'duracion' => (int) $servicio['duracion_minutos'],
    ] : null,
    'dias'     => $dias,
]);
