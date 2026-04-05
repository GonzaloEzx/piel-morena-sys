<?php
/**
 * Piel Morena — API Pública: Listar promociones vigentes
 * GET → JSON { success, data: [{ id, nombre, descripcion, precio_pack, ... }] }
 *
 * Devuelve solo promos activas y dentro de su rango de vigencia,
 * con el id del servicio generado para linkear al wizard de reserva.
 */

require_once __DIR__ . '/../../includes/init.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    responder_json(false, null, 'Método no permitido', 405);
}

$db = getDB();

$stmt = $db->prepare(
    "SELECT p.id, p.nombre, p.descripcion, p.precio_pack, p.duracion_estimada,
            p.fecha_inicio, p.fecha_fin, p.imagen,
            p.id_servicio_generado,
            (SELECT GROUP_CONCAT(s.nombre ORDER BY s.nombre SEPARATOR ', ')
             FROM promocion_servicios ps
             JOIN servicios s ON ps.id_servicio = s.id
             WHERE ps.id_promocion = p.id
            ) AS servicios_incluidos,
            (SELECT COUNT(*) FROM promocion_servicios ps WHERE ps.id_promocion = p.id) AS total_servicios
     FROM promociones p
     WHERE p.activo = 1
       AND (p.fecha_inicio IS NULL OR p.fecha_inicio <= CURDATE())
       AND (p.fecha_fin IS NULL OR p.fecha_fin >= CURDATE())
     ORDER BY p.id DESC"
);
$stmt->execute();

responder_json(true, $stmt->fetchAll());
