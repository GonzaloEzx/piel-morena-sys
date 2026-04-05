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
            s.id_categoria, c.nombre AS categoria, c.icono AS categoria_icono, c.orden AS categoria_orden
     FROM servicios s
     LEFT JOIN categorias_servicios c ON s.id_categoria = c.id
     LEFT JOIN promociones p ON p.id_servicio_generado = s.id
     WHERE s.activo = 1
       AND (
         p.id IS NULL
         OR (
           p.activo = 1
           AND (p.fecha_inicio IS NULL OR p.fecha_inicio <= CURDATE())
           AND (p.fecha_fin IS NULL OR p.fecha_fin >= CURDATE())
         )
       )
     ORDER BY c.orden, s.nombre"
);
$stmt->execute();
$servicios = $stmt->fetchAll();

responder_json(true, $servicios);
