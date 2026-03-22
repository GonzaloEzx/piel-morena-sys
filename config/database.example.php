<?php
/**
 * Piel Morena - Configuración de Base de Datos
 *
 * INSTRUCCIONES:
 * 1. Copiar este archivo como database.php
 * 2. Rellenar los datos reales de conexión
 * 3. database.php está en .gitignore (no se sube al repo)
 */

define('DB_HOST', 'localhost');
define('DB_NAME', 'piel_morena');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

/**
 * Obtener conexión PDO a la base de datos
 */
function getDB(): PDO {
    static $pdo = null;

    if ($pdo === null) {
        $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        try {
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            if (ENVIRONMENT === 'development') {
                die("Error de conexión: " . $e->getMessage());
            } else {
                die("Error de conexión a la base de datos.");
            }
        }
    }

    return $pdo;
}
