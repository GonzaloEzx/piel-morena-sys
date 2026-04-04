<?php
/**
 * Piel Morena — API Pública: Jornadas Disponibles
 * GET → Devuelve próximas fechas con jornada activa para un servicio o categoría
 *
 * Params:
 *   id_servicio   (opcional) → busca la categoría del servicio
 *   id_categoria  (opcional) → busca directamente por categoría
 *   limite        (opcional) → máx fechas a devolver (default: 60 días)
 *
 * Respuesta: array de fechas con datos de la jornada
 */

require_once __DIR__ . '/../../includes/init.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    responder_json(false, null, 'Método no permitido', 405);
}

$db = getDB();

$id_servicio  = intval($_GET['id_servicio'] ?? 0);
$id_categoria = intval($_GET['id_categoria'] ?? 0);

// Resolver categoría desde servicio si no se pasa directamente
if ($id_servicio && !$id_categoria) {
    $stmt = $db->prepare(
        "SELECT s.id_categoria, c.requiere_jornada
         FROM servicios s
         JOIN categorias_servicios c ON s.id_categoria = c.id
         WHERE s.id = ? AND s.activo = 1"
    );
    $stmt->execute([$id_servicio]);
    $srv = $stmt->fetch();

    if (!$srv) {
        responder_json(false, null, 'Servicio no encontrado', 404);
    }
    if (!$srv['requiere_jornada']) {
        responder_json(true, [
            'requiere_jornada' => false,
            'fechas'           => [],
            'mensaje'          => 'Este servicio no requiere jornada. Usar calendario normal.',
        ]);
    }
    $id_categoria = (int) $srv['id_categoria'];
}

if (!$id_categoria) {
    responder_json(false, null, 'Se requiere id_servicio o id_categoria', 400);
}

// Verificar que la categoría requiere jornada
$stmt = $db->prepare(
    "SELECT id, nombre, requiere_jornada FROM categorias_servicios WHERE id = ? AND activo = 1"
);
$stmt->execute([$id_categoria]);
$cat = $stmt->fetch();

if (!$cat) {
    responder_json(false, null, 'Categoría no encontrada', 404);
}
if (!$cat['requiere_jornada']) {
    responder_json(true, [
        'requiere_jornada' => false,
        'fechas'           => [],
        'mensaje'          => 'Esta categoría no requiere jornada.',
    ]);
}

// Buscar jornadas activas desde hoy hasta 60 días
$fecha_hoy   = date('Y-m-d');
$fecha_hasta = date('Y-m-d', strtotime('+60 days'));

$stmt = $db->prepare(
    "SELECT id, fecha, hora_inicio, hora_fin
     FROM jornadas
     WHERE id_categoria = ? AND estado = 'activa'
       AND fecha >= ? AND fecha <= ?
     ORDER BY fecha ASC"
);
$stmt->execute([$id_categoria, $fecha_hoy, $fecha_hasta]);
$jornadas = $stmt->fetchAll();

// Enriquecer con nombre del día en español
$dias_es = ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'];
$meses_es = ['', 'Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];

$fechas = [];
foreach ($jornadas as $j) {
    $ts = strtotime($j['fecha']);
    $fechas[] = [
        'id'          => (int) $j['id'],
        'fecha'       => $j['fecha'],
        'dia_semana'  => $dias_es[(int) date('w', $ts)],
        'dia_numero'  => (int) date('j', $ts),
        'mes'         => $meses_es[(int) date('n', $ts)],
        'mes_numero'  => (int) date('n', $ts),
        'hora_inicio' => $j['hora_inicio'],
        'hora_fin'    => $j['hora_fin'],
    ];
}

responder_json(true, [
    'requiere_jornada' => true,
    'categoria'        => $cat['nombre'],
    'fechas'           => $fechas,
    'total'            => count($fechas),
]);
