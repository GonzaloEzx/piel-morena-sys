<?php
/**
 * Piel Morena — Cambiar contraseña (usuario autenticado)
 * POST: { password_actual: string, password_nueva: string }
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
$password_actual = $input['password_actual'] ?? '';
$password_nueva = $input['password_nueva'] ?? '';

if (!$password_nueva || strlen($password_nueva) < 6) {
    responder_json(false, null, 'La nueva contraseña debe tener al menos 6 caracteres', 400);
}

$resultado = cambiar_password(usuario_actual_id(), $password_actual, $password_nueva);

if ($resultado['success']) {
    responder_json(true, ['message' => 'Contraseña actualizada correctamente.']);
} else {
    responder_json(false, null, $resultado['error'], 400);
}
