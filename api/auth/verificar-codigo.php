<?php
/**
 * Piel Morena — Verificar código de verificación
 * POST: { email: string, codigo: string, tipo: 'registro'|'recuperacion' }
 */
define('PIEL_MORENA', true);
require_once __DIR__ . '/../../includes/init.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    responder_json(false, null, 'Método no permitido', 405);
}

$input = json_decode(file_get_contents('php://input'), true);
$email = trim($input['email'] ?? '');
$codigo = trim($input['codigo'] ?? '');
$tipo = $input['tipo'] ?? 'registro';

if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    responder_json(false, null, 'Email inválido', 400);
}

if (!$codigo || strlen($codigo) !== 6) {
    responder_json(false, null, 'Código inválido. Debe ser de 6 dígitos.', 400);
}

if (!in_array($tipo, ['registro', 'recuperacion'])) {
    responder_json(false, null, 'Tipo inválido', 400);
}

$resultado = verificar_codigo($email, $codigo, $tipo);

if ($resultado['success']) {
    // Si es verificación de registro, marcar email como verificado
    if ($tipo === 'registro') {
        $db = getDB();
        $stmt = $db->prepare("UPDATE usuarios SET email_verificado = 1 WHERE email = ?");
        $stmt->execute([$email]);
    }

    responder_json(true, ['message' => 'Código verificado correctamente.']);
} else {
    responder_json(false, null, $resultado['error'], 400);
}
