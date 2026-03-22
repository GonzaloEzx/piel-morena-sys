<?php
/**
 * Piel Morena — Actualizar perfil del cliente autenticado
 * POST: { nombre, apellidos, telefono }
 */
define('PIEL_MORENA', true);
require_once __DIR__ . '/../../includes/init.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    responder_json(false, null, 'Método no permitido', 405);
}

if (!esta_autenticado()) {
    responder_json(false, null, 'No autenticado', 401);
}

$input = json_decode(file_get_contents('php://input'), true);
$nombre = trim($input['nombre'] ?? '');
$apellidos = trim($input['apellidos'] ?? '');
$telefono = trim($input['telefono'] ?? '');

if (!$nombre) {
    responder_json(false, null, 'El nombre es requerido', 400);
}

if (!$apellidos) {
    responder_json(false, null, 'Los apellidos son requeridos', 400);
}

$db = getDB();
$stmt = $db->prepare("UPDATE usuarios SET nombre = ?, apellidos = ?, telefono = ? WHERE id = ?");
$stmt->execute([$nombre, $apellidos, $telefono, usuario_actual_id()]);

// Actualizar sesión
$_SESSION['usuario_nombre'] = $nombre . ' ' . $apellidos;

responder_json(true, ['message' => 'Perfil actualizado correctamente.']);
