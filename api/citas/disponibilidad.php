<?php
/**
 * Piel Morena - API: Disponibilidad de turnos
 * GET ?fecha=YYYY-MM-DD&id_servicio=X → JSON { success, data: { fecha, servicio, turnos[] } }
 *
 * Genera slots disponibles según horario del negocio y duración del servicio,
 * excluyendo los ya reservados (pendiente/confirmada/en_proceso).
 */

require_once __DIR__ . '/../../includes/init.php';

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

// Obtener configuración de horarios del negocio
$stmt = $db->prepare("SELECT valor FROM configuracion WHERE clave = ?");

$stmt->execute(['horario_apertura']);
$apertura = $stmt->fetchColumn() ?: '09:00';

$stmt->execute(['horario_cierre']);
$cierre = $stmt->fetchColumn() ?: '20:00';

$stmt->execute(['dias_laborales']);
$dias_laborales = $stmt->fetchColumn() ?: '1,2,3,4,5,6';

$stmt->execute(['intervalo_citas']);
$intervalo = intval($stmt->fetchColumn() ?: 30);

// Verificar si el día de la semana es laboral (1=Lun, 7=Dom)
$dia_semana = intval(date('N', $fecha_ts));
$dias_array = array_map('intval', explode(',', $dias_laborales));

if (!in_array($dia_semana, $dias_array)) {
    responder_json(true, [
        'fecha'    => $fecha,
        'servicio' => $servicio['nombre'],
        'precio'   => $servicio['precio'],
        'duracion' => $servicio['duracion_minutos'],
        'turnos'   => [],
        'mensaje'  => 'No atendemos este día',
    ]);
}

// Obtener citas ya reservadas para esa fecha (estados activos)
$stmt = $db->prepare(
    "SELECT hora_inicio, hora_fin FROM citas
     WHERE fecha = ? AND estado IN ('pendiente', 'confirmada', 'en_proceso')
     ORDER BY hora_inicio"
);
$stmt->execute([$fecha]);
$citas_ocupadas = $stmt->fetchAll();

// Generar slots disponibles
$duracion = max($servicio['duracion_minutos'], $intervalo);
$slots = [];

$hora_actual = strtotime($apertura);
$hora_cierre = strtotime($cierre);

// Si es hoy, no mostrar turnos pasados (con margen de 30 min)
if ($fecha === date('Y-m-d')) {
    $ahora_mas_margen = strtotime('+30 minutes');
    if ($ahora_mas_margen > $hora_actual) {
        // Redondear al próximo intervalo
        $minutos = intval(date('i', $ahora_mas_margen));
        $redondeo = ceil($minutos / $intervalo) * $intervalo;
        $hora_actual = strtotime(date('H', $ahora_mas_margen) . ':00') + ($redondeo * 60);
    }
}

while ($hora_actual + ($duracion * 60) <= $hora_cierre) {
    $inicio = date('H:i', $hora_actual);
    $fin    = date('H:i', $hora_actual + ($duracion * 60));

    // Verificar si el slot se solapa con alguna cita existente
    $disponible = true;
    foreach ($citas_ocupadas as $cita) {
        if ($inicio < $cita['hora_fin'] && $fin > $cita['hora_inicio']) {
            $disponible = false;
            break;
        }
    }

    if ($disponible) {
        $slots[] = [
            'hora_inicio' => $inicio,
            'hora_fin'    => $fin,
        ];
    }

    $hora_actual += $intervalo * 60;
}

responder_json(true, [
    'fecha'    => $fecha,
    'servicio' => $servicio['nombre'],
    'precio'   => $servicio['precio'],
    'duracion' => $servicio['duracion_minutos'],
    'turnos'   => $slots,
]);
