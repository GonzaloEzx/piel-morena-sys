<?php
/**
 * Piel Morena - API: Profesionales que realizan un servicio
 * GET ?id_servicio=X → JSON { success, data: [{ id, nombre, foto }] }
 *
 * Uso interno del panel (alta de turnos). Lista vacía: el servicio no
 * tiene profesionales asignadas.
 */

require_once __DIR__ . '/../../includes/init.php';
require_once __DIR__ . '/../../includes/agenda.php';

if (!esta_autenticado() || (!tiene_rol('admin') && !tiene_rol('empleado'))) {
    responder_json(false, null, 'No autorizado', 403);
}
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    responder_json(false, null, 'Método no permitido', 405);
}

$id_servicio = intval($_GET['id_servicio'] ?? 0);
if ($id_servicio <= 0) {
    responder_json(false, null, 'ID de servicio inválido', 400);
}

responder_json(true, pm_profesionales_servicio(getDB(), $id_servicio));
