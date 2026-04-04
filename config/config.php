<?php
/**
 * Piel Morena - Configuración General del Sistema
 */

// Evitar acceso directo
if (!defined("PIEL_MORENA")) {
    die("Acceso denegado");
}

// --- Overrides locales opcionales ---
$pm_local_config = [];
$pm_local_config_path = __DIR__ . "/config.local.php";
if (file_exists($pm_local_config_path)) {
    $pm_local_config = require $pm_local_config_path;
    if (!is_array($pm_local_config)) {
        $pm_local_config = [];
    }
}

// --- Detección de entorno ---
$pm_http_host = $_SERVER["HTTP_HOST"] ?? "";
$pm_server_name = $_SERVER["SERVER_NAME"] ?? "";
$pm_request_host = $pm_http_host !== "" ? $pm_http_host : $pm_server_name;
$pm_cli_server = PHP_SAPI === "cli-server";
$pm_is_local_request =
    $pm_request_host === "localhost" ||
    $pm_request_host === "127.0.0.1" ||
    $pm_request_host === "[::1]" ||
    str_starts_with($pm_request_host, "localhost:") ||
    str_starts_with($pm_request_host, "127.0.0.1:") ||
    str_starts_with($pm_request_host, "[::1]:");

$pm_environment =
    $pm_local_config["environment"] ??
    (getenv("PIEL_MORENA_ENV") ?: ($pm_cli_server || $pm_is_local_request ? "development" : "production"));

if (!in_array($pm_environment, ["development", "production"], true)) {
    $pm_environment = "production";
}

define("ENVIRONMENT", $pm_environment); // 'development' | 'production'

// --- URLs ---
$pm_scheme = (!empty($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] !== "off") ? "https" : "http";
$pm_default_url_base =
    ENVIRONMENT === "development"
        ? ($pm_request_host !== "" ? $pm_scheme . "://" . $pm_request_host : "http://localhost:8000")
        : "https://pielmorenaestetica.com.ar";

$pm_url_base =
    $pm_local_config["url_base"] ??
    (getenv("PIEL_MORENA_URL_BASE") ?: $pm_default_url_base);

define("URL_BASE", rtrim($pm_url_base, "/"));
define("URL_ADMIN", URL_BASE . "/admin");
define("URL_API", URL_BASE . "/api");

// --- Rutas del sistema ---
define("ROOT_PATH", dirname(__DIR__) . "/");
define("CONFIG_PATH", ROOT_PATH . "config/");
define("INCLUDES_PATH", ROOT_PATH . "includes/");
define("ADMIN_PATH", ROOT_PATH . "admin/");
define("UPLOADS_PATH", ROOT_PATH . "uploads/");
define("ASSETS_PATH", ROOT_PATH . "assets/");

// --- Información del negocio ---
define("NOMBRE_NEGOCIO", "Piel Morena");
define("TELEFONO_NEGOCIO", "+543624254052");
define("EMAIL_NEGOCIO", "zudaire83@gmail.com");
define("DIRECCION_NEGOCIO", "Vedia 459, Resistencia, Chaco");
define("INSTAGRAM_NEGOCIO", "https://www.instagram.com/pielmorenaesteticaok");

// --- Sesiones ---
define("SESSION_LIFETIME", 3600); // 1 hora en segundos

// --- Zona horaria ---
date_default_timezone_set("America/Argentina/Buenos_Aires");

// --- Errores según entorno ---
if (ENVIRONMENT === "development") {
    error_reporting(E_ALL);
    ini_set("display_errors", 1);
} else {
    error_reporting(0);
    ini_set("display_errors", 0);
}

// --- Google OAuth ---
define("GOOGLE_CLIENT_ID", ""); // Configurar en Google Cloud Console

// --- Verificación de email ---
define("CODIGO_EXPIRACION_MINUTOS", 15);
define("CODIGO_MAX_INTENTOS", 3);
define("CODIGO_MAX_POR_HORA", 5);
