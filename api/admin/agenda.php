<?php
/**
 * Piel Morena — API Admin: Agenda del día
 * GET ?fecha=YYYY-MM-DD → JSON { success, data: { fecha, profesionales[], citas[], pendientes[], manana[] } }
 *
 * citas      → turnos de esa fecha (sin cancelados), con su profesional.
 * pendientes → turnos cargados sin confirmar, desde hoy.
 * manana     → turnos de mañana, para mandar el recordatorio.
 *
 * Cada turno trae `wa_confirmar` y `wa_recordar`: links de WhatsApp a la
 * clienta con el mensaje armado (vacíos si no tiene teléfono válido).
 * Abrir un link no cambia nada: el estado se marca aparte.
 */

require_once __DIR__ . '/../../includes/init.php';
require_once __DIR__ . '/../../includes/agenda.php';

if (!esta_autenticado() || (!tiene_rol('admin') && !tiene_rol('empleado'))) {
    responder_json(false, null, 'No autorizado', 403);
}
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    responder_json(false, null, 'Método no permitido', 405);
}

$fecha = $_GET['fecha'] ?? date('Y-m-d');
if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha) || !strtotime($fecha)) {
    responder_json(false, null, 'Fecha inválida', 400);
}

$db = getDB();

const PM_DIAS = ['domingo', 'lunes', 'martes', 'miércoles', 'jueves', 'viernes', 'sábado'];
$fecha_texto = function (string $f): string {
    $ts = strtotime($f);
    if ($f === date('Y-m-d')) return 'hoy';
    if ($f === date('Y-m-d', strtotime('+1 day'))) return 'mañana';
    return PM_DIAS[(int) date('w', $ts)] . ' ' . date('j/n', $ts);
};

$turnos = function (string $where, array $params) use ($db, $fecha_texto): array {
    $stmt = $db->prepare(
        "SELECT c.id, c.fecha, c.hora_inicio, c.hora_fin, c.estado, c.id_empleado, c.id_servicio,
                s.nombre AS servicio, s.precio,
                u.nombre AS cliente_nombre, TRIM(CONCAT(u.nombre, ' ', u.apellidos)) AS cliente, u.telefono,
                TRIM(CONCAT(COALESCE(e.nombre, ''), ' ', COALESCE(e.apellidos, ''))) AS profesional
         FROM citas c
         JOIN servicios s ON s.id = c.id_servicio
         JOIN usuarios u ON u.id = c.id_cliente
         LEFT JOIN usuarios e ON e.id = c.id_empleado
         WHERE $where
         ORDER BY c.fecha, c.hora_inicio"
    );
    $stmt->execute($params);

    return array_map(function ($c) use ($fecha_texto) {
        $hora = substr($c['hora_inicio'], 0, 5);
        $dia = $fecha_texto($c['fecha']);
        $cierre = "\nSi necesitás cambiarlo o cancelarlo, respondé este mensaje.";
        $con = $c['profesional'] !== '' ? ' con ' . explode(' ', $c['profesional'])[0] : '';
        return [
            'id'           => (int) $c['id'],
            'fecha'        => $c['fecha'],
            'dia'          => $dia,
            'hora_inicio'  => $hora,
            'hora_fin'     => substr($c['hora_fin'], 0, 5),
            'estado'       => $c['estado'],
            'id_empleado'  => $c['id_empleado'] !== null ? (int) $c['id_empleado'] : null,
            'id_servicio'  => (int) $c['id_servicio'],
            'servicio'     => $c['servicio'],
            'precio'       => $c['precio'],
            'cliente'      => $c['cliente'],
            'telefono'     => $c['telefono'],
            'profesional'  => $c['profesional'] !== '' ? $c['profesional'] : null,
            'wa_confirmar' => pm_link_whatsapp((string) $c['telefono'],
                "Hola {$c['cliente_nombre']}! Te confirmamos tu turno de {$c['servicio']}$con el $dia a las $hora en Piel Morena (Vedia 459). Te esperamos!$cierre"),
            'wa_recordar'  => pm_link_whatsapp((string) $c['telefono'],
                "Hola {$c['cliente_nombre']}! Te recordamos tu turno de {$c['servicio']} $dia a las $hora en Piel Morena (Vedia 459). Si no podés venir, avisanos así lo libera otra clienta.$cierre"),
        ];
    }, $stmt->fetchAll());
};

$profesionales = $db->query(
    "SELECT u.id, TRIM(CONCAT(u.nombre, ' ', u.apellidos)) AS nombre
     FROM usuarios u
     WHERE u.rol = 'empleado' AND u.activo = 1
     ORDER BY u.nombre"
)->fetchAll();

responder_json(true, [
    'fecha'         => $fecha,
    'dia'           => $fecha_texto($fecha),
    'profesionales' => array_map(fn($p) => ['id' => (int) $p['id'], 'nombre' => $p['nombre']], $profesionales),
    'citas'         => $turnos("c.fecha = ? AND c.estado <> 'cancelada'", [$fecha]),
    'pendientes'    => $turnos("c.estado = 'pendiente' AND c.fecha >= CURDATE()", []),
    'manana'        => $turnos("c.fecha = CURDATE() + INTERVAL 1 DAY AND c.estado IN ('pendiente', 'confirmada')", []),
]);
