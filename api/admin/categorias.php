<?php
/**
 * Piel Morena — API Admin: Listar categorías
 * GET → JSON { success, data: [{ id, nombre }] }
 */

require_once __DIR__ . '/../../includes/init.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    responder_json(false, null, 'Método no permitido', 405);
}

if (!esta_autenticado() || !tiene_rol('admin')) {
    responder_json(false, null, 'No autorizado', 403);
}

$db   = getDB();
$stmt = $db->query("SELECT id, nombre, icono FROM categorias_servicios WHERE activo = 1 ORDER BY orden");
responder_json(true, $stmt->fetchAll());
