<?php
/**
 * Piel Morena — Solicitar recuperación de contraseña
 * POST: { email: string }
 * Genera un código y lo envía por email
 */
define('PIEL_MORENA', true);
require_once __DIR__ . '/../../includes/init.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    responder_json(false, null, 'Método no permitido', 405);
}

$input = json_decode(file_get_contents('php://input'), true);
$email = trim($input['email'] ?? '');

if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    responder_json(false, null, 'Email inválido', 400);
}

$db = getDB();

// Verificar que el email exista (pero no revelar en la respuesta)
$stmt = $db->prepare("SELECT id FROM usuarios WHERE email = ? AND activo = 1 LIMIT 1");
$stmt->execute([$email]);
$usuario = $stmt->fetch();

if ($usuario) {
    // Generar código
    $resultado = generar_codigo_verificacion($email, 'recuperacion');

    if ($resultado['success']) {
        enviar_email_codigo($email, $resultado['codigo'], 'recuperacion');
    }
}

// Siempre responder con éxito (no revelar si el email existe)
responder_json(true, ['message' => 'Si el correo está registrado, recibirás un código para restablecer tu contraseña.']);
