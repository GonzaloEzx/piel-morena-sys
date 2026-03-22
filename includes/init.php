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
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/mail_helper.php';
