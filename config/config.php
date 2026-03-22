<?php
/**
 * Piel Morena - Configuración General del Sistema
 */

// Evitar acceso directo
if (!defined('PIEL_MORENA')) {
    die('Acceso denegado');
}

// --- Entorno ---
define('ENVIRONMENT', 'production'); // 'development' | 'production'

// --- URLs ---
define('URL_BASE', 'https://skyneosec.kescom.com.ar');
define('URL_ADMIN', URL_BASE . '/admin');
define('URL_API', URL_BASE . '/api');

// --- Rutas del sistema ---
define('ROOT_PATH', dirname(__DIR__) . '/');
define('CONFIG_PATH', ROOT_PATH . 'config/');
define('INCLUDES_PATH', ROOT_PATH . 'includes/');
define('ADMIN_PATH', ROOT_PATH . 'admin/');
define('UPLOADS_PATH', ROOT_PATH . 'uploads/');
define('ASSETS_PATH', ROOT_PATH . 'assets/');

// --- Información del negocio ---
define('NOMBRE_NEGOCIO', 'Piel Morena');
define('TELEFONO_NEGOCIO', '+XX XXX XXX XXXX');
define('EMAIL_NEGOCIO', 'contacto@pielmorena.com');
define('DIRECCION_NEGOCIO', 'Dirección del salón');

// --- Sesiones ---
define('SESSION_LIFETIME', 3600); // 1 hora en segundos

// --- Zona horaria ---
date_default_timezone_set('America/Argentina/Buenos_Aires');

// --- Errores según entorno ---
if (ENVIRONMENT === 'development') {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

// --- Google OAuth ---
define('GOOGLE_CLIENT_ID', ''); // Configurar en Google Cloud Console

// --- Verificación de email ---
define('CODIGO_EXPIRACION_MINUTOS', 15);
define('CODIGO_MAX_INTENTOS', 3);
define('CODIGO_MAX_POR_HORA', 5);
