<?php
/**
 * Piel Morena — API Admin: CRUD Promociones/Packs
 * GET    → Listar / obtener una (?id=X)
 * POST   → Crear promo + auto-generar servicio-pack + pivot
 * PUT    → Editar promo + actualizar servicio + sincronizar pivot
 * DELETE → Desactivar promo y servicio asociado
 */

require_once __DIR__ . '/../../includes/init.php';

if (!esta_autenticado() || !tiene_rol('admin')) {
    responder_json(false, null, 'No autorizado', 403);
}

$db     = getDB();
$method = $_SERVER['REQUEST_METHOD'];

// ── GET ──
if ($method === 'GET') {

    // Detalle de una promo con sus servicios componentes
    if (!empty($_GET['id'])) {
        $stmt = $db->prepare(
            "SELECT p.*, s.nombre AS servicio_nombre, s.activo AS servicio_activo
             FROM promociones p
             LEFT JOIN servicios s ON p.id_servicio_generado = s.id
             WHERE p.id = ?"
        );
        $stmt->execute([$_GET['id']]);
        $promo = $stmt->fetch();
        if (!$promo) responder_json(false, null, 'Promoción no encontrada', 404);

        // Servicios componentes
        $stmt = $db->prepare(
            "SELECT ps.id_servicio, ps.cantidad, s.nombre, s.precio, s.duracion_minutos,
                    c.nombre AS categoria
             FROM promocion_servicios ps
             JOIN servicios s ON ps.id_servicio = s.id
             LEFT JOIN categorias_servicios c ON s.id_categoria = c.id
             WHERE ps.id_promocion = ?
             ORDER BY s.nombre"
        );
        $stmt->execute([$promo['id']]);
        $promo['servicios'] = $stmt->fetchAll();

        // Datos del servicio generado para el modal
        if ($promo['id_servicio_generado']) {
            $stmt = $db->prepare("SELECT disponibilidad, id_grupo_jornada FROM servicios WHERE id = ?");
            $stmt->execute([$promo['id_servicio_generado']]);
            $srv_data = $stmt->fetch();
            $promo['disponibilidad'] = $srv_data['disponibilidad'] ?? 'auto';
            $promo['id_grupo_jornada'] = $srv_data['id_grupo_jornada'];
        }

        responder_json(true, $promo);
    }

    // Listado
    $stmt = $db->query(
        "SELECT p.*,
                s.activo AS servicio_activo,
                (SELECT COUNT(*) FROM promocion_servicios ps WHERE ps.id_promocion = p.id) AS total_servicios,
                CASE
                    WHEN p.fecha_fin IS NOT NULL AND p.fecha_fin < CURDATE() THEN 'vencida'
                    WHEN p.fecha_inicio IS NOT NULL AND p.fecha_inicio > CURDATE() THEN 'programada'
                    ELSE 'vigente'
                END AS estado_vigencia
         FROM promociones p
         LEFT JOIN servicios s ON p.id_servicio_generado = s.id
         ORDER BY p.id DESC"
    );
    responder_json(true, $stmt->fetchAll());
}

// ── Body JSON ──
$input = json_decode(file_get_contents('php://input'), true) ?: [];

// ── Helper: obtener o crear la categoría de packs ──
function obtenerCatPacks(PDO $db): int {
    $stmt = $db->prepare("SELECT id FROM categorias_servicios WHERE nombre = 'Packs y Promociones' AND activo = 1 LIMIT 1");
    $stmt->execute();
    $cat = $stmt->fetch();
    if ($cat) return (int) $cat['id'];

    // Crear si no existe
    $stmt = $db->prepare(
        "INSERT INTO categorias_servicios (nombre, descripcion, icono, orden, activo, requiere_jornada)
         VALUES ('Packs y Promociones', 'Combos y packs de servicios a precio especial', 'bi-gift', 12, 1, 0)"
    );
    $stmt->execute();
    return (int) $db->lastInsertId();
}

// ── POST (crear) ──
if ($method === 'POST') {
    $nombre          = trim($input['nombre'] ?? '');
    $descripcion     = trim($input['descripcion'] ?? '');
    $precio          = floatval($input['precio_pack'] ?? 0);
    $duracion        = intval($input['duracion_estimada'] ?? 60);
    $fecha_inicio    = $input['fecha_inicio'] ?? null;
    $fecha_fin       = $input['fecha_fin'] ?? null;
    $servicios       = $input['servicios'] ?? [];  // [{id_servicio, cantidad}]
    $disponibilidad  = $input['disponibilidad'] ?? 'auto';
    $grupo_jornada   = $input['id_grupo_jornada'] ?? null;

    if (!$nombre || $precio <= 0) {
        responder_json(false, null, 'Nombre y precio son obligatorios', 400);
    }
    if (empty($servicios)) {
        responder_json(false, null, 'Seleccioná al menos un servicio para el pack', 400);
    }
    if (!in_array($disponibilidad, ['auto', 'normal', 'jornada'])) {
        $disponibilidad = 'auto';
    }
    if ($disponibilidad !== 'jornada') {
        $grupo_jornada = null;
    }

    // Validar fechas
    if ($fecha_inicio && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha_inicio)) $fecha_inicio = null;
    if ($fecha_fin && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha_fin)) $fecha_fin = null;

    $cat_packs = obtenerCatPacks($db);

    try {
        $db->beginTransaction();

        // 1. Crear servicio-pack
        $stmt = $db->prepare(
            "INSERT INTO servicios (nombre, descripcion, precio, duracion_minutos, id_categoria, disponibilidad, id_grupo_jornada, activo)
             VALUES (?, ?, ?, ?, ?, ?, ?, 1)"
        );
        $stmt->execute([$nombre, $descripcion, $precio, $duracion, $cat_packs, $disponibilidad, $grupo_jornada ?: null]);
        $id_servicio = (int) $db->lastInsertId();

        // 2. Crear promoción
        $stmt = $db->prepare(
            "INSERT INTO promociones (nombre, descripcion, precio_pack, duracion_estimada, fecha_inicio, fecha_fin, id_servicio_generado)
             VALUES (?, ?, ?, ?, ?, ?, ?)"
        );
        $stmt->execute([$nombre, $descripcion, $precio, $duracion, $fecha_inicio ?: null, $fecha_fin ?: null, $id_servicio]);
        $id_promo = (int) $db->lastInsertId();

        // 3. Insertar pivot
        $stmt = $db->prepare(
            "INSERT INTO promocion_servicios (id_promocion, id_servicio, cantidad) VALUES (?, ?, ?)"
        );
        foreach ($servicios as $srv) {
            $sid = intval($srv['id_servicio'] ?? $srv['id'] ?? 0);
            $qty = intval($srv['cantidad'] ?? 1);
            if ($sid > 0) {
                $stmt->execute([$id_promo, $sid, max(1, $qty)]);
            }
        }

        $db->commit();
    } catch (Throwable $e) {
        if ($db->inTransaction()) $db->rollBack();
        error_log('Piel Morena Crear Promo Error: ' . $e->getMessage());
        responder_json(false, null, 'Error al crear la promoción', 500);
    }

    responder_json(true, ['id' => $id_promo, 'id_servicio' => $id_servicio]);
}

// ── PUT (editar) ──
if ($method === 'PUT') {
    $id              = intval($input['id'] ?? 0);
    $nombre          = trim($input['nombre'] ?? '');
    $descripcion     = trim($input['descripcion'] ?? '');
    $precio          = floatval($input['precio_pack'] ?? 0);
    $duracion        = intval($input['duracion_estimada'] ?? 60);
    $fecha_inicio    = $input['fecha_inicio'] ?? null;
    $fecha_fin       = $input['fecha_fin'] ?? null;
    $servicios       = $input['servicios'] ?? [];
    $disponibilidad  = $input['disponibilidad'] ?? 'auto';
    $grupo_jornada   = $input['id_grupo_jornada'] ?? null;

    if (!$id || !$nombre || $precio <= 0) {
        responder_json(false, null, 'Datos incompletos', 400);
    }
    if (!in_array($disponibilidad, ['auto', 'normal', 'jornada'])) {
        $disponibilidad = 'auto';
    }
    if ($disponibilidad !== 'jornada') {
        $grupo_jornada = null;
    }
    if ($fecha_inicio && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha_inicio)) $fecha_inicio = null;
    if ($fecha_fin && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha_fin)) $fecha_fin = null;

    // Verificar que la promo existe
    $stmt = $db->prepare("SELECT * FROM promociones WHERE id = ?");
    $stmt->execute([$id]);
    $promo = $stmt->fetch();
    if (!$promo) responder_json(false, null, 'Promoción no encontrada', 404);

    try {
        $db->beginTransaction();

        // 1. Actualizar promoción
        $stmt = $db->prepare(
            "UPDATE promociones SET nombre=?, descripcion=?, precio_pack=?, duracion_estimada=?, fecha_inicio=?, fecha_fin=?
             WHERE id=?"
        );
        $stmt->execute([$nombre, $descripcion, $precio, $duracion, $fecha_inicio ?: null, $fecha_fin ?: null, $id]);

        // 2. Actualizar servicio-pack si existe
        if ($promo['id_servicio_generado']) {
            $stmt = $db->prepare(
                "UPDATE servicios SET nombre=?, descripcion=?, precio=?, duracion_minutos=?, disponibilidad=?, id_grupo_jornada=?
                 WHERE id=?"
            );
            $stmt->execute([$nombre, $descripcion, $precio, $duracion, $disponibilidad, $grupo_jornada ?: null, $promo['id_servicio_generado']]);
        }

        // 3. Sincronizar pivot
        if (!empty($servicios)) {
            $stmt = $db->prepare("DELETE FROM promocion_servicios WHERE id_promocion = ?");
            $stmt->execute([$id]);

            $stmt = $db->prepare(
                "INSERT INTO promocion_servicios (id_promocion, id_servicio, cantidad) VALUES (?, ?, ?)"
            );
            foreach ($servicios as $srv) {
                $sid = intval($srv['id_servicio'] ?? $srv['id'] ?? 0);
                $qty = intval($srv['cantidad'] ?? 1);
                if ($sid > 0) {
                    $stmt->execute([$id, $sid, max(1, $qty)]);
                }
            }
        }

        $db->commit();
    } catch (Throwable $e) {
        if ($db->inTransaction()) $db->rollBack();
        error_log('Piel Morena Editar Promo Error: ' . $e->getMessage());
        responder_json(false, null, 'Error al editar la promoción', 500);
    }

    responder_json(true);
}

// ── DELETE (desactivar) ──
if ($method === 'DELETE') {
    $id = intval($input['id'] ?? 0);
    if (!$id) responder_json(false, null, 'ID requerido', 400);

    $stmt = $db->prepare("SELECT * FROM promociones WHERE id = ?");
    $stmt->execute([$id]);
    $promo = $stmt->fetch();
    if (!$promo) responder_json(false, null, 'Promoción no encontrada', 404);

    // Desactivar promo
    $stmt = $db->prepare("UPDATE promociones SET activo = 0 WHERE id = ?");
    $stmt->execute([$id]);

    // Desactivar servicio asociado
    if ($promo['id_servicio_generado']) {
        $stmt = $db->prepare("UPDATE servicios SET activo = 0 WHERE id = ?");
        $stmt->execute([$promo['id_servicio_generado']]);
    }

    responder_json(true);
}

responder_json(false, null, 'Método no permitido', 405);
