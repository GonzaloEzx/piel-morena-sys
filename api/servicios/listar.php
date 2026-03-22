<?php
/**
 * Piel Morena - API: Listar servicios activos
 * GET → JSON { success, data: [{ id, nombre, precio, duracion, categoria }] }
 */

require_once __DIR__ . '/../../includes/init.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    responder_json(false, null, 'Método no permitido', 405);
}

$db = getDB();

$stmt = $db->prepare(
    "SELECT s.id, s.nombre, s.descripcion, s.precio, s.duracion_minutos, s.imagen,
            c.nombre AS categoria, c.icono AS categoria_icono
     FROM servicios s
     LEFT JOIN categorias_servicios c ON s.id_categoria = c.id
     WHERE s.activo = 1
     ORDER BY c.orden, s.nombre"
);
$stmt->execute();
$servicios = $stmt->fetchAll();

responder_json(true, $servicios);
