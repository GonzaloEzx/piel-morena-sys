<?php
/**
 * Piel Morena — API Publica: Fechas con alquiler de maquina disponible
 * GET → ?id_servicio=X
 *
 * Respuesta:
 *   - Servicio sin maquina asociada    → { requiere_maquina: false, fechas: [] }
 *   - Servicio con maquina + alquileres → { requiere_maquina: true, maquina, fechas: [...] }
 */

require_once __DIR__ . '/../../includes/init.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    responder_json(false, null, 'Metodo no permitido', 405);
}

$db          = getDB();
$id_servicio = (int) ($_GET['id_servicio'] ?? 0);

if (!$id_servicio) {
    responder_json(false, null, 'Se requiere id_servicio', 400);
}

$stmt = $db->prepare(
    "SELECT s.id_maquina, m.id AS maquina_id, m.nombre AS maquina_nombre,
            m.color AS maquina_color, m.icono AS maquina_icono, m.activo AS maquina_activa
     FROM servicios s
     LEFT JOIN maquinas m ON s.id_maquina = m.id
     WHERE s.id = ? AND s.activo = 1"
);
$stmt->execute([$id_servicio]);
$srv = $stmt->fetch();

if (!$srv) responder_json(false, null, 'Servicio no encontrado', 404);

// Servicio sin maquina: la disponibilidad se resuelve por jornada o calendario libre.
if (!$srv['id_maquina']) {
    responder_json(true, [
        'requiere_maquina' => false,
        'fechas'           => [],
        'mensaje'          => 'Este servicio no depende de una maquina alquilada.',
    ]);
}

// Maquina inactiva: no hay forma de reservar, devolver vacio explicito.
if (!$srv['maquina_activa']) {
    responder_json(true, [
        'requiere_maquina' => true,
        'maquina'          => $srv['maquina_nombre'],
        'fechas'           => [],
        'mensaje'          => 'La maquina ' . $srv['maquina_nombre'] . ' esta inactiva.',
    ]);
}

$fecha_hoy   = date('Y-m-d');
$fecha_hasta = date('Y-m-d', strtotime('+60 days'));

$stmt = $db->prepare(
    "SELECT id, fecha, hora_inicio, hora_fin
     FROM maquinas_alquileres
     WHERE id_maquina = ? AND estado = 'activo'
       AND fecha >= ? AND fecha <= ?
     ORDER BY fecha ASC"
);
$stmt->execute([(int) $srv['id_maquina'], $fecha_hoy, $fecha_hasta]);
$alquileres = $stmt->fetchAll();

$dias_es  = ['Domingo', 'Lunes', 'Martes', 'Miercoles', 'Jueves', 'Viernes', 'Sabado'];
$meses_es = ['', 'Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];

$fechas = [];
foreach ($alquileres as $a) {
    $ts = strtotime($a['fecha']);
    $fechas[] = [
        'id'          => (int) $a['id'],
        'fecha'       => $a['fecha'],
        'dia_semana'  => $dias_es[(int) date('w', $ts)],
        'dia_numero'  => (int) date('j', $ts),
        'mes'         => $meses_es[(int) date('n', $ts)],
        'mes_numero'  => (int) date('n', $ts),
        'hora_inicio' => $a['hora_inicio'],
        'hora_fin'    => $a['hora_fin'],
    ];
}

responder_json(true, [
    'requiere_maquina' => true,
    'maquina'          => $srv['maquina_nombre'],
    'maquina_color'    => $srv['maquina_color'],
    'maquina_icono'    => $srv['maquina_icono'],
    'fechas'           => $fechas,
    'total'            => count($fechas),
]);
