<?php
/**
 * Piel Morena — Restablecer contraseña con código verificado
 * POST: { email: string, codigo: string, password: string }
 */
define('PIEL_MORENA', true);
require_once __DIR__ . '/../../includes/init.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    responder_json(false, null, 'Método no permitido', 405);
}

$input = json_decode(file_get_contents('php://input'), true);
$email = trim($input['email'] ?? '');
$codigo = trim($input['codigo'] ?? '');
$password = $input['password'] ?? '';

if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    responder_json(false, null, 'Email inválido', 400);
}

if (!$codigo || strlen($codigo) !== 6) {
    responder_json(false, null, 'Código inválido', 400);
}

if (!$password || strlen($password) < 6) {
    responder_json(false, null, 'La contraseña debe tener al menos 6 caracteres', 400);
}

// Verificar código
$verificacion = verificar_codigo($email, $codigo, 'recuperacion');

if (!$verificacion['success']) {
    responder_json(false, null, $verificacion['error'], 400);
}

// Resetear contraseña
$resultado = resetear_password($email, $password);

if ($resultado['success']) {
    responder_json(true, ['message' => 'Contraseña actualizada correctamente. Ya podés iniciar sesión.']);
} else {
    responder_json(false, null, $resultado['error'], 400);
}
