<?php
/**
 * Piel Morena - API: Registro de cliente
 * POST { nombre, apellidos, email, telefono, password } → JSON { success }
 */

require_once __DIR__ . '/../../includes/init.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    responder_json(false, null, 'Método no permitido', 405);
}

$input = json_decode(file_get_contents('php://input'), true);

$nombre    = trim($input['nombre'] ?? '');
$apellidos = trim($input['apellidos'] ?? '');
$email     = trim($input['email'] ?? '');
$telefono  = trim($input['telefono'] ?? '');
$password  = $input['password'] ?? '';

// Validaciones
if (!$nombre) {
    responder_json(false, null, 'El nombre es requerido', 400);
}

if (!$apellidos) {
    responder_json(false, null, 'Los apellidos son requeridos', 400);
}

if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    responder_json(false, null, 'Ingresa un email válido', 400);
}

if (!$password || strlen($password) < 6) {
    responder_json(false, null, 'La contraseña debe tener al menos 6 caracteres', 400);
}

$resultado = registrar_cliente($nombre, $apellidos, $email, $password, $telefono);

if ($resultado['success']) {
    // Enviar email de bienvenida (best-effort)
    try {
        enviar_email($email, 'Bienvenida a Piel Morena', 'bienvenida', [
            'nombre' => $nombre,
        ]);
        registrar_notificacion($resultado['id'], 'sistema', 'Bienvenida', 'Bienvenida a Piel Morena. Tu cuenta fue creada con exito.');
    } catch (Exception $e) {
        error_log('Piel Morena Mail Error (registro): ' . $e->getMessage());
    }

    responder_json(true, ['id' => $resultado['id']]);
} else {
    responder_json(false, null, $resultado['error'], 409);
}
