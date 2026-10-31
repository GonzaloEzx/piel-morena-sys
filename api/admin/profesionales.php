<?php
/**
 * Piel Morena — API Admin: servicios y horario de cada profesional
 *
 * GET            → resumen de profesionales y alertas por categoría
 * GET ?id=N      → servicios (agrupados por categoría) y horario semanal
 * PUT {id, servicios:[ids], usa_horario_local:bool, horarios:[{dia_semana, hora_inicio, hora_fin}]}
 *
 * Lo usa includes/agenda.php: una profesional sin horario cargado trabaja
 * en el horario del local; un servicio sin profesional activa cae al modo
 * salón (cualquier cita del local bloquea el horario).
 */

require_once __DIR__ . '/../../includes/init.php';

if (!esta_autenticado() || !tiene_rol('admin')) {
    responder_json(false, null, 'No autorizado', 403);
}

$db = getDB();
$method = $_SERVER['REQUEST_METHOD'];

function pm_prof_local(PDO $db): array {
    $cfg = $db->query("SELECT clave, valor FROM configuracion WHERE clave IN ('horario_apertura', 'horario_cierre', 'dias_laborales')")
              ->fetchAll(PDO::FETCH_KEY_PAIR);
    return [
        'apertura' => substr($cfg['horario_apertura'] ?? '09:00', 0, 5),
        'cierre'   => substr($cfg['horario_cierre'] ?? '20:00', 0, 5),
        'dias'     => array_map('intval', array_filter(explode(',', $cfg['dias_laborales'] ?? '1,2,3,4,5,6'))),
    ];
}

function pm_prof_horarios(PDO $db, int $id): array {
    $stmt = $db->prepare("SELECT dia_semana, TIME_FORMAT(hora_inicio, '%H:%i') AS hora_inicio, TIME_FORMAT(hora_fin, '%H:%i') AS hora_fin
                          FROM horarios WHERE id_empleado = ? AND activo = 1 ORDER BY dia_semana, hora_inicio");
    $stmt->execute([$id]);
    return array_map(fn($h) => ['dia_semana' => (int) $h['dia_semana']] + $h, $stmt->fetchAll());
}

if ($method === 'GET' && empty($_GET['id'])) {
    $pros = $db->query("SELECT id, nombre, apellidos, activo FROM usuarios WHERE rol = 'empleado' ORDER BY activo DESC, nombre")->fetchAll();

    $cats = $db->query(
        "SELECT es.id_empleado, c.nombre, COUNT(*) AS n
         FROM empleados_servicios es
         JOIN servicios s ON s.id = es.id_servicio AND s.activo = 1
         JOIN categorias_servicios c ON c.id = s.id_categoria
         GROUP BY es.id_empleado, c.id, c.nombre, c.orden ORDER BY c.orden, c.nombre"
    )->fetchAll();

    foreach ($pros as &$p) {
        $p['id'] = (int) $p['id'];
        $p['activo'] = (int) $p['activo'];
        $p['categorias'] = array_values(array_map(fn($c) => $c['nombre'], array_filter($cats, fn($c) => (int) $c['id_empleado'] === $p['id'])));
        $p['horarios'] = pm_prof_horarios($db, $p['id']);
    }
    unset($p);

    // Categorías con servicios activos y sin ninguna profesional activa que los haga
    $alertas = $db->query(
        "SELECT c.id, c.nombre, c.requiere_jornada
         FROM categorias_servicios c
         WHERE c.activo = 1
           AND EXISTS (SELECT 1 FROM servicios s WHERE s.id_categoria = c.id AND s.activo = 1)
           AND NOT EXISTS (
             SELECT 1 FROM servicios s
             JOIN empleados_servicios es ON es.id_servicio = s.id
             JOIN usuarios u ON u.id = es.id_empleado AND u.activo = 1 AND u.rol = 'empleado'
             WHERE s.id_categoria = c.id AND s.activo = 1)
         ORDER BY c.orden, c.nombre"
    )->fetchAll();

    responder_json(true, ['profesionales' => $pros, 'sin_profesional' => $alertas, 'local' => pm_prof_local($db)]);
}

if ($method === 'GET') {
    $id = (int) $_GET['id'];
    $stmt = $db->prepare("SELECT id, nombre, apellidos, activo FROM usuarios WHERE id = ? AND rol = 'empleado'");
    $stmt->execute([$id]);
    $pro = $stmt->fetch();
    if (!$pro) responder_json(false, null, 'Profesional no encontrada', 404);

    $stmt = $db->prepare(
        "SELECT c.id AS id_categoria, c.nombre AS categoria, c.requiere_jornada,
                s.id, s.nombre, s.duracion_minutos, (es.id IS NOT NULL) AS asignado
         FROM servicios s
         JOIN categorias_servicios c ON c.id = s.id_categoria AND c.activo = 1
         LEFT JOIN empleados_servicios es ON es.id_servicio = s.id AND es.id_empleado = ?
         WHERE s.activo = 1
         ORDER BY c.orden, c.nombre, s.nombre"
    );
    $stmt->execute([$id]);
    $categorias = [];
    foreach ($stmt->fetchAll() as $s) {
        $k = (int) $s['id_categoria'];
        $categorias[$k] ??= ['id' => $k, 'nombre' => $s['categoria'], 'requiere_jornada' => (int) $s['requiere_jornada'], 'servicios' => []];
        $categorias[$k]['servicios'][] = [
            'id' => (int) $s['id'], 'nombre' => $s['nombre'],
            'duracion_minutos' => (int) $s['duracion_minutos'], 'asignado' => (bool) $s['asignado'],
        ];
    }

    $horarios = pm_prof_horarios($db, $id);
    responder_json(true, [
        'profesional' => $pro,
        'categorias' => array_values($categorias),
        'horarios' => $horarios,
        'usa_horario_local' => !$horarios,
        'local' => pm_prof_local($db),
    ]);
}

if ($method === 'PUT') {
    $in = json_decode(file_get_contents('php://input'), true) ?: [];
    $id = (int) ($in['id'] ?? 0);
    $stmt = $db->prepare("SELECT id FROM usuarios WHERE id = ? AND rol = 'empleado'");
    $stmt->execute([$id]);
    if (!$stmt->fetch()) responder_json(false, null, 'Profesional no encontrada', 404);

    $servicios = array_values(array_unique(array_map('intval', (array) ($in['servicios'] ?? []))));
    $activos = array_map('intval', $db->query("SELECT id FROM servicios WHERE activo = 1")->fetchAll(PDO::FETCH_COLUMN));
    if (array_diff($servicios, $activos)) responder_json(false, null, 'Hay servicios que no existen o están inactivos', 400);

    $usa_local = !empty($in['usa_horario_local']);
    $horarios = [];
    if (!$usa_local) {
        foreach ((array) ($in['horarios'] ?? []) as $h) {
            $dia = (int) ($h['dia_semana'] ?? 0);
            $ini = (string) ($h['hora_inicio'] ?? '');
            $fin = (string) ($h['hora_fin'] ?? '');
            if ($dia < 1 || $dia > 7 || !preg_match('/^([01]\d|2[0-3]):[0-5]\d$/', $ini) || !preg_match('/^([01]\d|2[0-3]):[0-5]\d$/', $fin)) {
                responder_json(false, null, 'Revisá los días y horarios', 400);
            }
            if ($ini >= $fin) responder_json(false, null, 'La hora de salida tiene que ser posterior a la de entrada', 400);
            $horarios[] = [$dia, $ini, $fin];
        }
        if (!$horarios) {
            responder_json(false, null, 'Marcá al menos un día. Si no va a atender, desactivala desde Empleados.', 400);
        }
        usort($horarios, fn($a, $b) => [$a[0], $a[1]] <=> [$b[0], $b[1]]);
        for ($i = 1; $i < count($horarios); $i++) {
            if ($horarios[$i][0] === $horarios[$i - 1][0] && $horarios[$i][1] < $horarios[$i - 1][2]) {
                responder_json(false, null, 'Hay franjas superpuestas en el mismo día', 400);
            }
        }
    }

    $db->beginTransaction();
    try {
        // Solo se reemplazan las asignaciones de servicios activos; las de inactivos quedan
        $db->prepare("DELETE es FROM empleados_servicios es JOIN servicios s ON s.id = es.id_servicio
                      WHERE es.id_empleado = ? AND s.activo = 1")->execute([$id]);
        $ins = $db->prepare("INSERT INTO empleados_servicios (id_empleado, id_servicio) VALUES (?, ?)");
        foreach ($servicios as $s) $ins->execute([$id, $s]);

        $db->prepare("DELETE FROM horarios WHERE id_empleado = ?")->execute([$id]);
        $ins = $db->prepare("INSERT INTO horarios (id_empleado, dia_semana, hora_inicio, hora_fin, activo) VALUES (?, ?, ?, ?, 1)");
        foreach ($horarios as $h) $ins->execute([$id, ...$h]);
        $db->commit();
    } catch (Throwable $e) {
        $db->rollBack();
        responder_json(false, null, 'No se pudo guardar', 500);
    }
    responder_json(true);
}

responder_json(false, null, 'Método no permitido', 405);
