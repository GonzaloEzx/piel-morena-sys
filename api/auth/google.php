<?php
/**
 * Piel Morena — Login/Registro via Google OAuth
 * POST: { credential: string (Google JWT id_token) }
 */
define('PIEL_MORENA', true);
require_once __DIR__ . '/../../includes/init.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    responder_json(false, null, 'Método no permitido', 405);
}

$input = json_decode(file_get_contents('php://input'), true);
$credential = $input['credential'] ?? '';

if (!$credential) {
    responder_json(false, null, 'Token de Google requerido', 400);
}

// Verificar el token con Google
$google_url = 'https://oauth2.googleapis.com/tokeninfo?id_token=' . urlencode($credential);
$ch = curl_init($google_url);
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_SSL_VERIFYPEER => true,
    CURLOPT_TIMEOUT => 10,
]);
$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($http_code !== 200 || !$response) {
    responder_json(false, null, 'Token de Google inválido', 401);
}

$payload = json_decode($response, true);

// Verificar que el token sea para nuestra app
if (!defined('GOOGLE_CLIENT_ID') || empty(GOOGLE_CLIENT_ID)) {
    // Si no está configurado, aceptar cualquier token válido (dev mode)
    // En producción SIEMPRE verificar el aud
} else if (($payload['aud'] ?? '') !== GOOGLE_CLIENT_ID) {
    responder_json(false, null, 'Token no autorizado para esta aplicación', 401);
}

$google_id = $payload['sub'] ?? '';
$email = $payload['email'] ?? '';
$nombre = $payload['given_name'] ?? $payload['name'] ?? '';
$apellidos = $payload['family_name'] ?? '';
$email_verified = $payload['email_verified'] ?? false;

if (!$google_id || !$email) {
    responder_json(false, null, 'Datos de Google incompletos', 400);
}

// Login o crear cuenta
$resultado = login_google($google_id, $email, $nombre, $apellidos);

if ($resultado['success']) {
    responder_json(true, [
        'rol' => $resultado['rol'],
        'nombre' => $nombre . ' ' . $apellidos,
        'nuevo' => $resultado['nuevo'] ?? false,
    ]);
} else {
    responder_json(false, null, $resultado['error'], 401);
}
