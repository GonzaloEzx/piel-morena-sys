<?php
/**
 * Piel Morena - API: Login
 * POST { email, password } → JSON { success, data: { rol, nombre } }
 */

require_once __DIR__ . '/../../includes/init.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    responder_json(false, null, 'Método no permitido', 405);
}

$input = json_decode(file_get_contents('php://input'), true);

$email    = trim($input['email'] ?? '');
$password = $input['password'] ?? '';

if (!$email || !$password) {
    responder_json(false, null, 'Email y contraseña son requeridos', 400);
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    responder_json(false, null, 'Formato de email inválido', 400);
}

$resultado = iniciar_sesion($email, $password);

if ($resultado['success']) {
    responder_json(true, [
        'rol'    => $resultado['rol'],
        'nombre' => $_SESSION['usuario_nombre'],
    ]);
} else {
    responder_json(false, null, $resultado['error'], 401);
}
