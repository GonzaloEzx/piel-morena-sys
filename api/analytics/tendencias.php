<?php
/**
 * Piel Morena — API Analytics: Tendencias
 * GET ?fecha_desde=&fecha_hasta= → JSON comparativa período actual vs anterior,
 *     tasa cancelación, ticket promedio, clientes nuevos vs recurrentes, horarios pico
 */

require_once __DIR__ . '/../../includes/init.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    responder_json(false, null, 'Método no permitido', 405);
}

if (!esta_autenticado() || !tiene_rol('admin')) {
    responder_json(false, null, 'No autorizado', 403);
}

$db = getDB();

$fecha_desde = $_GET['fecha_desde'] ?? date('Y-m-d', strtotime('-30 days'));
$fecha_hasta = $_GET['fecha_hasta'] ?? date('Y-m-d');

// Calcular período anterior de igual duración
$dias = (int) ((strtotime($fecha_hasta) - strtotime($fecha_desde)) / 86400) + 1;
$anterior_hasta = date('Y-m-d', strtotime($fecha_desde . ' -1 day'));
$anterior_desde = date('Y-m-d', strtotime($anterior_hasta . " -{$dias} days +1 day"));

// ── Comparativa de citas: actual vs anterior ──
$stmt = $db->prepare(
    "SELECT COUNT(*) AS total,
            SUM(estado = 'completada') AS completadas,
            SUM(estado = 'cancelada') AS canceladas,
            SUM(estado = 'pendiente') AS pendientes,
            SUM(estado = 'confirmada') AS confirmadas
     FROM citas
     WHERE fecha >= ? AND fecha <= ?"
);

$stmt->execute([$fecha_desde, $fecha_hasta]);
$citas_actual = $stmt->fetch();

$stmt->execute([$anterior_desde, $anterior_hasta]);
$citas_anterior = $stmt->fetch();

// Tasa de cancelación
$tasa_cancelacion_actual = $citas_actual['total'] > 0
    ? round(($citas_actual['canceladas'] / $citas_actual['total']) * 100, 1)
    : 0;
$tasa_cancelacion_anterior = $citas_anterior['total'] > 0
    ? round(($citas_anterior['canceladas'] / $citas_anterior['total']) * 100, 1)
    : 0;

// ── Ticket promedio (ingreso promedio por cita completada) ──
$stmt = $db->prepare(
    "SELECT COALESCE(AVG(s.precio), 0) AS ticket_promedio
     FROM citas c
     JOIN servicios s ON c.id_servicio = s.id
     WHERE c.estado = 'completada' AND c.fecha >= ? AND c.fecha <= ?"
);
$stmt->execute([$fecha_desde, $fecha_hasta]);
$ticket_actual = (float) $stmt->fetchColumn();

$stmt->execute([$anterior_desde, $anterior_hasta]);
$ticket_anterior = (float) $stmt->fetchColumn();

// ── Ingresos comparativa ──
$stmt = $db->prepare(
    "SELECT COALESCE(SUM(CASE WHEN tipo='entrada' THEN monto ELSE 0 END), 0) AS ingresos
     FROM caja_movimientos
     WHERE fecha >= ? AND fecha <= ?"
);
$stmt->execute([$fecha_desde, $fecha_hasta]);
$ingresos_actual = (float) $stmt->fetchColumn();

$stmt->execute([$anterior_desde, $anterior_hasta]);
$ingresos_anterior = (float) $stmt->fetchColumn();

// ── Clientes nuevos vs recurrentes ──
$stmt = $db->prepare(
    "SELECT COUNT(DISTINCT c.id_cliente) AS total_clientes
     FROM citas c
     WHERE c.fecha >= ? AND c.fecha <= ? AND c.estado != 'cancelada'"
);
$stmt->execute([$fecha_desde, $fecha_hasta]);
$total_clientes = (int) $stmt->fetchColumn();

// Clientes nuevos: su primera cita está dentro del rango
$stmt = $db->prepare(
    "SELECT COUNT(*) FROM (
         SELECT c.id_cliente, MIN(c.fecha) AS primera_cita
         FROM citas c
         WHERE c.estado != 'cancelada'
         GROUP BY c.id_cliente
         HAVING primera_cita >= ? AND primera_cita <= ?
     ) AS nuevos"
);
$stmt->execute([$fecha_desde, $fecha_hasta]);
$clientes_nuevos = (int) $stmt->fetchColumn();
$clientes_recurrentes = $total_clientes - $clientes_nuevos;

// ── Horarios pico (hora con más citas) ──
$stmt = $db->prepare(
    "SELECT HOUR(hora_inicio) AS hora, COUNT(*) AS total
     FROM citas
     WHERE fecha >= ? AND fecha <= ? AND estado != 'cancelada'
     GROUP BY HOUR(hora_inicio)
     ORDER BY total DESC
     LIMIT 10"
);
$stmt->execute([$fecha_desde, $fecha_hasta]);
$horarios_pico = $stmt->fetchAll();

// ── Citas por día de la semana ──
$stmt = $db->prepare(
    "SELECT DAYOFWEEK(fecha) AS dia, COUNT(*) AS total
     FROM citas
     WHERE fecha >= ? AND fecha <= ? AND estado != 'cancelada'
     GROUP BY DAYOFWEEK(fecha)
     ORDER BY dia"
);
$stmt->execute([$fecha_desde, $fecha_hasta]);
$citas_por_dia_semana = $stmt->fetchAll();

// Función helper para calcular variación porcentual
function variacion(float $actual, float $anterior): ?float {
    if ($anterior == 0) return $actual > 0 ? 100.0 : null;
    return round((($actual - $anterior) / $anterior) * 100, 1);
}

responder_json(true, [
    'periodo_actual'  => ['desde' => $fecha_desde, 'hasta' => $fecha_hasta, 'dias' => $dias],
    'periodo_anterior' => ['desde' => $anterior_desde, 'hasta' => $anterior_hasta],
    'comparativa' => [
        'citas' => [
            'actual'    => (int) $citas_actual['total'],
            'anterior'  => (int) $citas_anterior['total'],
            'variacion' => variacion($citas_actual['total'], $citas_anterior['total'])
        ],
        'ingresos' => [
            'actual'    => $ingresos_actual,
            'anterior'  => $ingresos_anterior,
            'variacion' => variacion($ingresos_actual, $ingresos_anterior)
        ],
        'ticket_promedio' => [
            'actual'    => round($ticket_actual, 2),
            'anterior'  => round($ticket_anterior, 2),
            'variacion' => variacion($ticket_actual, $ticket_anterior)
        ],
        'tasa_cancelacion' => [
            'actual'    => $tasa_cancelacion_actual,
            'anterior'  => $tasa_cancelacion_anterior
        ]
    ],
    'clientes' => [
        'total'       => $total_clientes,
        'nuevos'      => $clientes_nuevos,
        'recurrentes' => $clientes_recurrentes
    ],
    'horarios_pico'       => $horarios_pico,
    'citas_por_dia_semana' => $citas_por_dia_semana
]);
