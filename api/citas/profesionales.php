<?php
/**
 * Piel Morena - API: Profesionales que realizan un servicio
 * GET ?id_servicio=X → JSON { success, data: [{ id, nombre, foto }] }
 *
 * Lista vacía: el servicio no tiene profesionales asignadas y se reserva
 * sin elegir.
 */

require_once __DIR__ . '/../../includes/init.php';
require_once __DIR__ . '/../../includes/agenda.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    responder_json(false, null, 'Método no permitido', 405);
}

$id_servicio = intval($_GET['id_servicio'] ?? 0);
if ($id_servicio <= 0) {
    responder_json(false, null, 'ID de servicio inválido', 400);
}

responder_json(true, pm_profesionales_servicio(getDB(), $id_servicio));
