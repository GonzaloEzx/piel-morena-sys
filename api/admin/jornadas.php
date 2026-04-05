<?php
/**
 * Piel Morena — API Admin: Gestión de Jornadas
 * GET    → Listar jornadas (filtro: categoria, fecha_desde, fecha_hasta, estado)
 * POST   → Crear jornada(s) — soporta múltiples fechas
 * PUT    → Editar jornada (horarios, notas)
 * PATCH  → Cancelar jornada (con info de citas afectadas)
 */

require_once __DIR__ . '/../../includes/init.php';

if (!esta_autenticado() || !tiene_rol('admin')) {
    responder_json(false, null, 'No autorizado', 403);
}

$db     = getDB();
$method = $_SERVER['REQUEST_METHOD'];

// ── GET ──
if ($method === 'GET') {

    // Detalle de una jornada
    if (!empty($_GET['id'])) {
        $stmt = $db->prepare(
            "SELECT j.*, c.nombre AS categoria
             FROM jornadas j
             JOIN categorias_servicios c ON j.id_categoria = c.id
             WHERE j.id = ?"
        );
        $stmt->execute([$_GET['id']]);
        $jornada = $stmt->fetch();
        if (!$jornada) responder_json(false, null, 'Jornada no encontrada', 404);

        // Contar citas en esa jornada (incluye servicios con id_grupo_jornada)
        $stmt = $db->prepare(
            "SELECT COUNT(*) FROM citas ci
             JOIN servicios s ON ci.id_servicio = s.id
             WHERE (s.id_categoria = ? OR s.id_grupo_jornada = ?) AND ci.fecha = ?
               AND ci.estado NOT IN ('cancelada')"
        );
        $stmt->execute([$jornada['id_categoria'], $jornada['id_categoria'], $jornada['fecha']]);
        $jornada['citas_reservadas'] = (int) $stmt->fetchColumn();

        responder_json(true, $jornada);
    }

    // Listar categorías disponibles para jornadas (todas las activas)
    if (isset($_GET['categorias_jornada'])) {
        $stmt = $db->query(
            "SELECT id, nombre, icono, requiere_jornada FROM categorias_servicios
             WHERE activo = 1
             ORDER BY orden"
        );
        responder_json(true, $stmt->fetchAll());
    }

    // Listado con filtros
    $where  = [];
    $params = [];

    if (!empty($_GET['id_categoria'])) {
        $where[]  = 'j.id_categoria = ?';
        $params[] = intval($_GET['id_categoria']);
    }
    if (!empty($_GET['fecha_desde'])) {
        $where[]  = 'j.fecha >= ?';
        $params[] = $_GET['fecha_desde'];
    }
    if (!empty($_GET['fecha_hasta'])) {
        $where[]  = 'j.fecha <= ?';
        $params[] = $_GET['fecha_hasta'];
    }
    if (!empty($_GET['estado'])) {
        $where[]  = 'j.estado = ?';
        $params[] = $_GET['estado'];
    }

    $sql = "SELECT j.*, c.nombre AS categoria, c.icono AS categoria_icono,
                   (SELECT COUNT(*) FROM citas ci
                    JOIN servicios s ON ci.id_servicio = s.id
                    WHERE (s.id_categoria = j.id_categoria OR s.id_grupo_jornada = j.id_categoria) AND ci.fecha = j.fecha
                      AND ci.estado NOT IN ('cancelada')) AS citas_reservadas
            FROM jornadas j
            JOIN categorias_servicios c ON j.id_categoria = c.id";

    if ($where) {
        $sql .= ' WHERE ' . implode(' AND ', $where);
    }
    $sql .= ' ORDER BY j.fecha ASC, c.nombre ASC';

    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    responder_json(true, $stmt->fetchAll());
}

// ── Body JSON ──
$input = json_decode(file_get_contents('php://input'), true) ?: [];

// ── POST (crear — soporta múltiples fechas) ──
if ($method === 'POST') {
    $id_categoria = intval($input['id_categoria'] ?? 0);
    $fechas       = $input['fechas'] ?? [];
    $hora_inicio  = trim($input['hora_inicio'] ?? '08:00');
    $hora_fin     = trim($input['hora_fin'] ?? '20:00');
    $notas        = trim($input['notas'] ?? '');

    // Validar categoría
    if (!$id_categoria) {
        responder_json(false, null, 'Categoría requerida', 400);
    }

    $stmt = $db->prepare("SELECT id, nombre FROM categorias_servicios WHERE id = ? AND activo = 1");
    $stmt->execute([$id_categoria]);
    if (!$stmt->fetch()) {
        responder_json(false, null, 'La categoría no existe o no está activa', 400);
    }

    // Aceptar fecha única o array de fechas
    if (!is_array($fechas)) {
        $fechas = !empty($input['fecha']) ? [$input['fecha']] : [];
    }
    if (empty($fechas)) {
        responder_json(false, null, 'Al menos una fecha es requerida', 400);
    }

    // Validar horarios
    if ($hora_inicio >= $hora_fin) {
        responder_json(false, null, 'La hora de inicio debe ser anterior a la hora de fin', 400);
    }

    $creadas    = [];
    $duplicadas = [];

    $stmt_check = $db->prepare(
        "SELECT id FROM jornadas WHERE id_categoria = ? AND fecha = ?"
    );
    $stmt_insert = $db->prepare(
        "INSERT INTO jornadas (id_categoria, fecha, hora_inicio, hora_fin, notas)
         VALUES (?, ?, ?, ?, ?)"
    );

    foreach ($fechas as $fecha) {
        $fecha = trim($fecha);
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha)) continue;

        // Verificar duplicado
        $stmt_check->execute([$id_categoria, $fecha]);
        if ($stmt_check->fetch()) {
            $duplicadas[] = $fecha;
            continue;
        }

        $stmt_insert->execute([$id_categoria, $fecha, $hora_inicio, $hora_fin, $notas ?: null]);
        $creadas[] = $fecha;
    }

    $data = [
        'creadas'    => $creadas,
        'duplicadas' => $duplicadas,
        'total'      => count($creadas),
    ];

    if (empty($creadas) && !empty($duplicadas)) {
        responder_json(false, $data, 'Todas las fechas ya tienen jornada programada para esta categoría', 409);
    }

    responder_json(true, $data);
}

// ── PUT (editar) ──
if ($method === 'PUT') {
    $id          = intval($input['id'] ?? 0);
    $hora_inicio = trim($input['hora_inicio'] ?? '');
    $hora_fin    = trim($input['hora_fin'] ?? '');
    $notas       = $input['notas'] ?? null;

    if (!$id) responder_json(false, null, 'ID requerido', 400);

    $stmt = $db->prepare("SELECT * FROM jornadas WHERE id = ?");
    $stmt->execute([$id]);
    $jornada = $stmt->fetch();
    if (!$jornada) responder_json(false, null, 'Jornada no encontrada', 404);

    // Mantener valores actuales si no se envían
    $hora_inicio = $hora_inicio ?: $jornada['hora_inicio'];
    $hora_fin    = $hora_fin ?: $jornada['hora_fin'];
    if ($notas === null) $notas = $jornada['notas'];

    if ($hora_inicio >= $hora_fin) {
        responder_json(false, null, 'La hora de inicio debe ser anterior a la hora de fin', 400);
    }

    $stmt = $db->prepare(
        "UPDATE jornadas SET hora_inicio = ?, hora_fin = ?, notas = ? WHERE id = ?"
    );
    $stmt->execute([$hora_inicio, $hora_fin, $notas, $id]);
    responder_json(true);
}

// ── PATCH (cancelar) ──
if ($method === 'PATCH') {
    $id = intval($input['id'] ?? 0);
    if (!$id) responder_json(false, null, 'ID requerido', 400);

    $stmt = $db->prepare(
        "SELECT j.*, c.nombre AS categoria
         FROM jornadas j
         JOIN categorias_servicios c ON j.id_categoria = c.id
         WHERE j.id = ?"
    );
    $stmt->execute([$id]);
    $jornada = $stmt->fetch();
    if (!$jornada) responder_json(false, null, 'Jornada no encontrada', 404);

    if ($jornada['estado'] === 'cancelada') {
        responder_json(false, null, 'La jornada ya está cancelada', 400);
    }

    // Buscar citas afectadas (no canceladas) — incluye servicios con id_grupo_jornada
    $stmt = $db->prepare(
        "SELECT ci.id, ci.hora_inicio, ci.hora_fin, ci.estado,
                u.nombre AS cliente_nombre, u.apellidos AS cliente_apellidos,
                s.nombre AS servicio
         FROM citas ci
         JOIN servicios s ON ci.id_servicio = s.id
         JOIN usuarios u ON ci.id_cliente = u.id
         WHERE (s.id_categoria = ? OR s.id_grupo_jornada = ?) AND ci.fecha = ?
           AND ci.estado NOT IN ('cancelada')
         ORDER BY ci.hora_inicio"
    );
    $stmt->execute([$jornada['id_categoria'], $jornada['id_categoria'], $jornada['fecha']]);
    $citas_afectadas = $stmt->fetchAll();

    // Si solo quiere info (confirmacion previa)
    if (!empty($input['solo_info'])) {
        responder_json(true, [
            'jornada'          => $jornada,
            'citas_afectadas'  => $citas_afectadas,
            'total_afectadas'  => count($citas_afectadas),
        ]);
    }

    // Cancelar la jornada
    $stmt = $db->prepare("UPDATE jornadas SET estado = 'cancelada' WHERE id = ?");
    $stmt->execute([$id]);

    responder_json(true, [
        'citas_afectadas' => count($citas_afectadas),
        'mensaje'         => count($citas_afectadas) > 0
            ? 'Jornada cancelada. Hay ' . count($citas_afectadas) . ' cita(s) que deberías gestionar manualmente.'
            : 'Jornada cancelada sin citas afectadas.',
    ]);
}

responder_json(false, null, 'Método no permitido', 405);
