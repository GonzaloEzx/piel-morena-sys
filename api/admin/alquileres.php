<?php
/**
 * Piel Morena — API Admin: Alquileres de Maquinas
 * GET    → Listar alquileres (filtros: id_maquina, estado, fecha_desde, fecha_hasta)
 *          GET ?id=X → detalle + lista de citas activas afectadas
 *          GET ?maquinas_activas=1 → listar maquinas activas (selector)
 * POST   → Crear alquiler(es) — soporta multiples fechas
 * PUT    → Editar (horarios, notas)
 * DELETE → Eliminar definitivamente + cancelar citas activas asociadas
 */

require_once __DIR__ . '/../../includes/init.php';

if (!esta_autenticado() || !tiene_rol('admin')) {
    responder_json(false, null, 'No autorizado', 403);
}

$db     = getDB();
$method = $_SERVER['REQUEST_METHOD'];

// ── GET ──
if ($method === 'GET') {

    // Detalle de un alquiler
    if (!empty($_GET['id'])) {
        $stmt = $db->prepare(
            "SELECT a.*, m.nombre AS maquina, m.color AS maquina_color
             FROM maquinas_alquileres a
             JOIN maquinas m ON a.id_maquina = m.id
             WHERE a.id = ?"
        );
        $stmt->execute([(int) $_GET['id']]);
        $alquiler = $stmt->fetch();
        if (!$alquiler) responder_json(false, null, 'Alquiler no encontrado', 404);

        // Citas activas afectadas
        $stmt = $db->prepare(
            "SELECT ci.id, ci.hora_inicio, ci.hora_fin, ci.estado,
                    u.nombre AS cliente_nombre, u.apellidos AS cliente_apellidos,
                    s.nombre AS servicio
             FROM citas ci
             JOIN servicios s ON ci.id_servicio = s.id
             JOIN usuarios u ON ci.id_cliente = u.id
             WHERE s.id_maquina = ? AND ci.fecha = ? AND ci.estado NOT IN ('cancelada')
             ORDER BY ci.hora_inicio"
        );
        $stmt->execute([$alquiler['id_maquina'], $alquiler['fecha']]);
        $citas = $stmt->fetchAll();
        $alquiler['citas_reservadas'] = count($citas);
        $alquiler['citas_afectadas']  = $citas;

        responder_json(true, $alquiler);
    }

    // Listar maquinas activas (selector del modal)
    if (isset($_GET['maquinas_activas'])) {
        $stmt = $db->query(
            "SELECT id, nombre, color, icono FROM maquinas WHERE activo = 1 ORDER BY nombre"
        );
        responder_json(true, $stmt->fetchAll());
    }

    // Listado con filtros
    $where  = [];
    $params = [];

    if (!empty($_GET['id_maquina'])) {
        $where[]  = 'a.id_maquina = ?';
        $params[] = (int) $_GET['id_maquina'];
    }
    if (!empty($_GET['estado'])) {
        $where[]  = 'a.estado = ?';
        $params[] = $_GET['estado'];
    }
    if (!empty($_GET['fecha_desde'])) {
        $where[]  = 'a.fecha >= ?';
        $params[] = $_GET['fecha_desde'];
    }
    if (!empty($_GET['fecha_hasta'])) {
        $where[]  = 'a.fecha <= ?';
        $params[] = $_GET['fecha_hasta'];
    }

    $sql = "SELECT a.*, m.nombre AS maquina, m.color AS maquina_color, m.icono AS maquina_icono,
                   (SELECT COUNT(*) FROM citas ci
                    JOIN servicios s ON ci.id_servicio = s.id
                    WHERE s.id_maquina = a.id_maquina AND ci.fecha = a.fecha
                      AND ci.estado NOT IN ('cancelada')) AS citas_reservadas
            FROM maquinas_alquileres a
            JOIN maquinas m ON a.id_maquina = m.id";
    if ($where) $sql .= ' WHERE ' . implode(' AND ', $where);
    $sql .= ' ORDER BY a.fecha ASC, m.nombre ASC';

    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    responder_json(true, $stmt->fetchAll());
}

// ── Body JSON ──
$input = json_decode(file_get_contents('php://input'), true) ?: [];

// ── POST (crear, soporta multiples fechas) ──
if ($method === 'POST') {
    $id_maquina  = (int) ($input['id_maquina'] ?? 0);
    $fechas      = $input['fechas'] ?? [];
    $hora_inicio = trim($input['hora_inicio'] ?? '08:00');
    $hora_fin    = trim($input['hora_fin'] ?? '20:00');
    $notas       = trim($input['notas'] ?? '');

    if (!$id_maquina) responder_json(false, null, 'Maquina requerida', 400);

    $stmt = $db->prepare("SELECT id, nombre FROM maquinas WHERE id = ? AND activo = 1");
    $stmt->execute([$id_maquina]);
    if (!$stmt->fetch()) {
        responder_json(false, null, 'La maquina no existe o no esta activa', 400);
    }

    if (!is_array($fechas)) {
        $fechas = !empty($input['fecha']) ? [$input['fecha']] : [];
    }
    if (empty($fechas)) {
        responder_json(false, null, 'Al menos una fecha es requerida', 400);
    }

    if ($hora_inicio >= $hora_fin) {
        responder_json(false, null, 'La hora de inicio debe ser anterior a la hora de fin', 400);
    }

    $creadas    = [];
    $duplicadas = [];

    $stmt_check  = $db->prepare("SELECT id FROM maquinas_alquileres WHERE id_maquina = ? AND fecha = ?");
    $stmt_insert = $db->prepare(
        "INSERT INTO maquinas_alquileres (id_maquina, fecha, hora_inicio, hora_fin, notas)
         VALUES (?, ?, ?, ?, ?)"
    );

    foreach ($fechas as $fecha) {
        $fecha = trim($fecha);
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha)) continue;

        $stmt_check->execute([$id_maquina, $fecha]);
        if ($stmt_check->fetch()) {
            $duplicadas[] = $fecha;
            continue;
        }

        $stmt_insert->execute([$id_maquina, $fecha, $hora_inicio, $hora_fin, $notas ?: null]);
        $creadas[] = $fecha;
    }

    $data = [
        'creadas'    => $creadas,
        'duplicadas' => $duplicadas,
        'total'      => count($creadas),
    ];

    if (empty($creadas) && !empty($duplicadas)) {
        responder_json(false, $data, 'Todas las fechas ya tienen alquiler programado para esta maquina', 409);
    }

    responder_json(true, $data);
}

// ── PUT ──
if ($method === 'PUT') {
    $id          = (int) ($input['id'] ?? 0);
    $hora_inicio = trim($input['hora_inicio'] ?? '');
    $hora_fin    = trim($input['hora_fin'] ?? '');
    $notas       = $input['notas'] ?? null;

    if (!$id) responder_json(false, null, 'ID requerido', 400);

    $stmt = $db->prepare("SELECT * FROM maquinas_alquileres WHERE id = ?");
    $stmt->execute([$id]);
    $alquiler = $stmt->fetch();
    if (!$alquiler) responder_json(false, null, 'Alquiler no encontrado', 404);

    $hora_inicio = $hora_inicio ?: $alquiler['hora_inicio'];
    $hora_fin    = $hora_fin ?: $alquiler['hora_fin'];
    if ($notas === null) $notas = $alquiler['notas'];

    if ($hora_inicio >= $hora_fin) {
        responder_json(false, null, 'La hora de inicio debe ser anterior a la hora de fin', 400);
    }

    $stmt = $db->prepare(
        "UPDATE maquinas_alquileres SET hora_inicio = ?, hora_fin = ?, notas = ? WHERE id = ?"
    );
    $stmt->execute([$hora_inicio, $hora_fin, $notas, $id]);
    responder_json(true);
}

// ── DELETE (hard delete + cancelar citas activas) ──
if ($method === 'DELETE') {
    $id = (int) ($input['id'] ?? 0);
    if (!$id) responder_json(false, null, 'ID requerido', 400);

    $stmt = $db->prepare(
        "SELECT a.*, m.nombre AS maquina
         FROM maquinas_alquileres a
         JOIN maquinas m ON a.id_maquina = m.id
         WHERE a.id = ?"
    );
    $stmt->execute([$id]);
    $alquiler = $stmt->fetch();
    if (!$alquiler) responder_json(false, null, 'Alquiler no encontrado', 404);

    // Cancelar citas activas
    $stmt = $db->prepare(
        "UPDATE citas ci
         JOIN servicios s ON ci.id_servicio = s.id
         SET ci.estado = 'cancelada'
         WHERE s.id_maquina = ? AND ci.fecha = ? AND ci.estado NOT IN ('cancelada')"
    );
    $stmt->execute([$alquiler['id_maquina'], $alquiler['fecha']]);
    $citas_canceladas = $stmt->rowCount();

    // Eliminar alquiler
    $stmt = $db->prepare("DELETE FROM maquinas_alquileres WHERE id = ?");
    $stmt->execute([$id]);

    responder_json(true, [
        'citas_canceladas' => $citas_canceladas,
        'mensaje'          => $citas_canceladas > 0
            ? "Alquiler eliminado. Se cancelaron {$citas_canceladas} cita(s) asociadas."
            : 'Alquiler eliminado.',
    ]);
}

responder_json(false, null, 'Metodo no permitido', 405);
