<?php
/**
 * Piel Morena - API: Guardar mensaje de contacto
 * POST { nombre, email, telefono, mensaje } → JSON { success }
 */

require_once __DIR__ . '/../includes/init.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    responder_json(false, null, 'Método no permitido', 405);
}

$input = json_decode(file_get_contents('php://input'), true);

$nombre   = trim($input['nombre'] ?? '');
$email    = trim($input['email'] ?? '');
$telefono = trim($input['telefono'] ?? '');
$mensaje  = trim($input['mensaje'] ?? '');

// Validaciones
if (!$nombre) {
    responder_json(false, null, 'El nombre es requerido', 400);
}

if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    responder_json(false, null, 'Ingresa un email válido', 400);
}

if (!$mensaje) {
    responder_json(false, null, 'El mensaje es requerido', 400);
}

$db = getDB();

$stmt = $db->prepare("INSERT INTO contacto_mensajes (nombre, email, telefono, mensaje) VALUES (?, ?, ?, ?)");
$stmt->execute([$nombre, $email, $telefono, $mensaje]);

responder_json(true, ['message' => 'Mensaje enviado correctamente']);
