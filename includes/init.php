<?php
/**
 * Piel Morena - Archivo de Inicialización
 * Incluir este archivo al inicio de cada página/script
 */

// Constante de seguridad
define('PIEL_MORENA', true);

// Iniciar sesión
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Cargar configuración
require_once __DIR__ . '/../config/config.php';

// Expiración de sesión por inactividad
if (isset($_SESSION['usuario_id']) && isset($_SESSION['ultima_actividad'])) {
    if (time() - $_SESSION['ultima_actividad'] > SESSION_LIFETIME) {
        session_unset();
        session_destroy();
        session_start();
    }
}
if (isset($_SESSION['usuario_id'])) {
    $_SESSION['ultima_actividad'] = time();
}
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/mail_helper.php';
