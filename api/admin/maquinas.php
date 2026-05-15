<?php
/**
 * Piel Morena — API Admin: Catalogo de Maquinas Alquiladas
 * GET    → Listar (filtros: activo, q) | ?id=X → detalle con counts
 * POST   → Crear maquina
 * PUT    → Editar maquina
 * DELETE → Eliminar maquina (solo si no tiene servicios asociados ni alquileres activos)
 */

require_once __DIR__ . '/../../includes/init.php';

if (!esta_autenticado() || !tiene_rol('admin')) {
    responder_json(false, null, 'No autorizado', 403);
}

$db     = getDB();
$method = $_SERVER['REQUEST_METHOD'];

// ── GET ──
if ($method === 'GET') {

    // Detalle
    if (!empty($_GET['id'])) {
        $stmt = $db->prepare("SELECT * FROM maquinas WHERE id = ?");
        $stmt->execute([(int) $_GET['id']]);
        $maquina = $stmt->fetch();
        if (!$maquina) responder_json(false, null, 'Maquina no encontrada', 404);

        $stmt = $db->prepare("SELECT COUNT(*) FROM servicios WHERE id_maquina = ?");
        $stmt->execute([$maquina['id']]);
        $maquina['servicios_count'] = (int) $stmt->fetchColumn();

        $stmt = $db->prepare(
            "SELECT COUNT(*) FROM maquinas_alquileres WHERE id_maquina = ? AND estado = 'activo' AND fecha >= CURDATE()"
        );
        $stmt->execute([$maquina['id']]);
        $maquina['alquileres_futuros'] = (int) $stmt->fetchColumn();

        responder_json(true, $maquina);
    }

    // Listado con filtros
    $where  = [];
    $params = [];

    if (isset($_GET['activo']) && $_GET['activo'] !== '') {
        $where[]  = 'activo = ?';
        $params[] = (int) $_GET['activo'];
    }
    if (!empty($_GET['q'])) {
        $where[]  = 'nombre LIKE ?';
        $params[] = '%' . $_GET['q'] . '%';
    }

    $sql = "SELECT m.*,
                   (SELECT COUNT(*) FROM servicios WHERE id_maquina = m.id) AS servicios_count,
                   (SELECT COUNT(*) FROM maquinas_alquileres WHERE id_maquina = m.id AND estado = 'activo' AND fecha >= CURDATE()) AS alquileres_futuros
            FROM maquinas m";
    if ($where) $sql .= ' WHERE ' . implode(' AND ', $where);
    $sql .= ' ORDER BY m.nombre ASC';

    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    responder_json(true, $stmt->fetchAll());
}

// ── Body JSON ──
$input = json_decode(file_get_contents('php://input'), true) ?: [];

// ── POST ──
if ($method === 'POST') {
    $nombre      = trim($input['nombre'] ?? '');
    $descripcion = trim($input['descripcion'] ?? '');
    $color       = trim($input['color'] ?? '#8A7650');
    $icono       = trim($input['icono'] ?? '');
    $activo      = isset($input['activo']) ? (int) $input['activo'] : 1;
    $notas       = trim($input['notas'] ?? '');

    if ($nombre === '') responder_json(false, null, 'Nombre requerido', 400);
    if (!preg_match('/^#[0-9A-Fa-f]{6}$/', $color)) $color = '#8A7650';

    // Verificar nombre unico
    $stmt = $db->prepare("SELECT id FROM maquinas WHERE nombre = ?");
    $stmt->execute([$nombre]);
    if ($stmt->fetch()) responder_json(false, null, 'Ya existe una maquina con ese nombre', 409);

    $stmt = $db->prepare(
        "INSERT INTO maquinas (nombre, descripcion, color, icono, activo, notas)
         VALUES (?, ?, ?, ?, ?, ?)"
    );
    $stmt->execute([
        $nombre,
        $descripcion ?: null,
        $color,
        $icono ?: null,
        $activo,
        $notas ?: null,
    ]);

    responder_json(true, ['id' => (int) $db->lastInsertId()]);
}

// ── PUT ──
if ($method === 'PUT') {
    $id = (int) ($input['id'] ?? 0);
    if (!$id) responder_json(false, null, 'ID requerido', 400);

    $stmt = $db->prepare("SELECT * FROM maquinas WHERE id = ?");
    $stmt->execute([$id]);
    $maquina = $stmt->fetch();
    if (!$maquina) responder_json(false, null, 'Maquina no encontrada', 404);

    $nombre      = trim($input['nombre'] ?? $maquina['nombre']);
    $descripcion = array_key_exists('descripcion', $input) ? trim($input['descripcion']) : $maquina['descripcion'];
    $color       = trim($input['color'] ?? $maquina['color']);
    $icono       = array_key_exists('icono', $input) ? trim($input['icono']) : $maquina['icono'];
    $activo      = array_key_exists('activo', $input) ? (int) $input['activo'] : (int) $maquina['activo'];
    $notas       = array_key_exists('notas', $input) ? trim($input['notas']) : $maquina['notas'];

    if ($nombre === '') responder_json(false, null, 'Nombre requerido', 400);
    if (!preg_match('/^#[0-9A-Fa-f]{6}$/', $color)) $color = $maquina['color'];

    // Verificar nombre unico (excluyendo el propio)
    $stmt = $db->prepare("SELECT id FROM maquinas WHERE nombre = ? AND id != ?");
    $stmt->execute([$nombre, $id]);
    if ($stmt->fetch()) responder_json(false, null, 'Ya existe otra maquina con ese nombre', 409);

    $stmt = $db->prepare(
        "UPDATE maquinas SET nombre = ?, descripcion = ?, color = ?, icono = ?, activo = ?, notas = ?
         WHERE id = ?"
    );
    $stmt->execute([
        $nombre,
        $descripcion ?: null,
        $color,
        $icono ?: null,
        $activo,
        $notas ?: null,
        $id,
    ]);

    responder_json(true);
}

// ── DELETE ──
if ($method === 'DELETE') {
    $id = (int) ($input['id'] ?? 0);
    if (!$id) responder_json(false, null, 'ID requerido', 400);

    $stmt = $db->prepare("SELECT * FROM maquinas WHERE id = ?");
    $stmt->execute([$id]);
    $maquina = $stmt->fetch();
    if (!$maquina) responder_json(false, null, 'Maquina no encontrada', 404);

    // Verificar servicios asociados
    $stmt = $db->prepare("SELECT COUNT(*) FROM servicios WHERE id_maquina = ?");
    $stmt->execute([$id]);
    $servicios_count = (int) $stmt->fetchColumn();
    if ($servicios_count > 0) {
        responder_json(false, null, "No se puede eliminar: la maquina tiene {$servicios_count} servicio(s) asociado(s). Desactivala o reasigna esos servicios primero.", 409);
    }

    // Verificar alquileres futuros activos
    $stmt = $db->prepare(
        "SELECT COUNT(*) FROM maquinas_alquileres
         WHERE id_maquina = ? AND estado = 'activo' AND fecha >= CURDATE()"
    );
    $stmt->execute([$id]);
    $alquileres_futuros = (int) $stmt->fetchColumn();
    if ($alquileres_futuros > 0) {
        responder_json(false, null, "No se puede eliminar: la maquina tiene {$alquileres_futuros} alquiler(es) futuro(s). Cancelalos primero.", 409);
    }

    // ON DELETE CASCADE en alquileres se encarga de los historicos
    $stmt = $db->prepare("DELETE FROM maquinas WHERE id = ?");
    $stmt->execute([$id]);

    responder_json(true);
}

responder_json(false, null, 'Metodo no permitido', 405);
