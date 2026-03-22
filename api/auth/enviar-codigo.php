<?php
/**
 * Piel Morena — Enviar código de verificación por email
 * POST: { email: string, tipo: 'registro'|'recuperacion' }
 */
define('PIEL_MORENA', true);
require_once __DIR__ . '/../../includes/init.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    responder_json(false, null, 'Método no permitido', 405);
}

$input = json_decode(file_get_contents('php://input'), true);
$email = trim($input['email'] ?? '');
$tipo = $input['tipo'] ?? 'registro';

if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    responder_json(false, null, 'Email inválido', 400);
}

if (!in_array($tipo, ['registro', 'recuperacion'])) {
    responder_json(false, null, 'Tipo inválido', 400);
}

$db = getDB();

// Para recuperación: verificar que el email exista
if ($tipo === 'recuperacion') {
    $stmt = $db->prepare("SELECT id FROM usuarios WHERE email = ? AND activo = 1 LIMIT 1");
    $stmt->execute([$email]);
    if (!$stmt->fetch()) {
        // No revelar si el email existe (seguridad)
        responder_json(true, ['message' => 'Si el correo está registrado, recibirás un código.']);
    }
}

// Para registro: verificar que el email NO exista
if ($tipo === 'registro') {
    $stmt = $db->prepare("SELECT id FROM usuarios WHERE email = ? LIMIT 1");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        responder_json(false, null, 'Este email ya tiene una cuenta. Iniciá sesión.', 409);
    }
}

// Generar código
$resultado = generar_codigo_verificacion($email, $tipo);

if (!$resultado['success']) {
    responder_json(false, null, $resultado['error'], 429);
}

// Enviar email
$enviado = enviar_email_codigo($email, $resultado['codigo'], $tipo);

// Responder siempre con éxito (no revelar si el envío falló por razones de seguridad en recuperación)
if ($tipo === 'recuperacion') {
    responder_json(true, ['message' => 'Si el correo está registrado, recibirás un código.']);
} else {
    if ($enviado) {
        responder_json(true, ['message' => 'Código enviado a tu correo electrónico.']);
    } else {
        // En desarrollo, devolver el código para testing
        if (defined('ENVIRONMENT') && ENVIRONMENT === 'development') {
            responder_json(true, ['message' => 'Código generado (dev mode).', 'codigo_dev' => $resultado['codigo']]);
        } else {
            responder_json(true, ['message' => 'Código enviado a tu correo electrónico.']);
        }
    }
}
