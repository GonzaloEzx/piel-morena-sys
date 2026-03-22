<?php
/**
 * Piel Morena - API: Logout
 * POST → JSON { success } o GET → redirige al inicio
 */

require_once __DIR__ . '/../../includes/init.php';

cerrar_sesion();

// Si es AJAX (POST con JSON), responder JSON
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    responder_json(true, ['message' => 'Sesión cerrada']);
}

// Si es GET (link directo desde navbar), redirigir al inicio
header('Location: ' . URL_BASE . '/');
exit;
