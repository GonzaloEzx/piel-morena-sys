<?php
/**
 * Arma una base de PRUEBA con datos dummy. Usa config/database.php del
 * checkout donde corre.
 *
 * Receta: schema.sql (estructura vigente hasta la 015) + migraciones que
 * agregan lo que el esquema no trae (001-003), catálogo real (004-005),
 * testimonios (007-008), promociones nuevas (012), la 016 y los datos de demo. Las demás migraciones
 * dependen de ids de producción y no se reproducen.
 *
 * Al final cambia la contraseña del admin del seed (admin123, pública en el
 * repo) por una aleatoria y la muestra una sola vez.
 *
 * Uso (CLI): php database/demo/armar_base_test.php
 *
 * Seguridad: se niega a correr si el nombre de la base no contiene "test"
 * o si la base ya tiene tablas (no pisa nada). Nunca apuntarlo a producción.
 */

if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

define('PIEL_MORENA', true);
$raiz = dirname(__DIR__, 2);
require $raiz . '/config/config.php';
require $raiz . '/config/database.php';

if (stripos(DB_NAME, 'test') === false) {
    fwrite(STDERR, "Base '" . DB_NAME . "': el nombre no contiene 'test'. No se toca.\n");
    exit(1);
}

$db = getDB();
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$tablas = $db->query('SHOW TABLES')->fetchAll(PDO::FETCH_COLUMN);
if ($tablas) {
    fwrite(STDERR, "La base '" . DB_NAME . "' ya tiene " . count($tablas) . " tablas. Vaciala a mano si querés rearmarla.\n");
    exit(1);
}

$m = $raiz . '/database/migrations/';
$archivos = [
    $raiz . '/database/schema.sql',
    $raiz . '/database/seed.sql',
    $m . '001_add_google_id.sql',
    $m . '002_add_email_verificado.sql',
    $m . '003_create_codigos_verificacion.sql',
    $m . '004_servicios_reales.sql',
    $m . '005_cleanup_datos_prueba.sql',
    $m . '007_create_testimonios.sql',
    $m . '008_testimonios_slots_fijos.sql',
    $m . '012_promociones_packs.sql',
    $m . '016_agenda_por_profesional.sql',
    $raiz . '/database/demo/seed_demo_agenda.sql',
];

// Errores esperables: el esquema ya trae parte de lo que agregan las migraciones
$esperables = [1050, 1060, 1061, 1062, 1091, 1146, 1054, 1826, 1022];

foreach ($archivos as $archivo) {
    $sql = file_get_contents($archivo);
    $sql = preg_replace('/^\s*CREATE DATABASE[^;]*;/mi', '', $sql);
    $sql = preg_replace('/^\s*USE\s+[^;]+;/mi', '', $sql);
    $sql = preg_replace('/^\s*--.*$/m', '', $sql);

    $ok = 0;
    $omitidas = 0;
    foreach (preg_split('/;\s*(\r?\n|$)/', $sql) as $sentencia) {
        if (trim($sentencia) === '') {
            continue;
        }
        try {
            $db->exec($sentencia);
            $ok++;
        } catch (PDOException $e) {
            $codigo = (int) ($e->errorInfo[1] ?? 0);
            if (!in_array($codigo, $esperables, true)) {
                fwrite(STDERR, basename($archivo) . ": error $codigo: " . $e->getMessage() . "\n");
                exit(1);
            }
            $omitidas++;
        }
    }
    echo str_pad(basename($archivo), 42) . " ok=$ok omitidas=$omitidas\n";
}

$clave = bin2hex(random_bytes(9));
$db->prepare("UPDATE usuarios SET password = ? WHERE email = 'admin@pielmorena.com'")
   ->execute([password_hash($clave, PASSWORD_DEFAULT)]);

$n = fn($sql) => $db->query($sql)->fetchColumn();
echo "\nservicios activos: " . $n('SELECT COUNT(*) FROM servicios WHERE activo = 1')
    . " · profesionales: " . $n("SELECT COUNT(*) FROM usuarios WHERE rol = 'empleado'")
    . " · citas: " . $n('SELECT COUNT(*) FROM citas') . "\n"
    . "\nAdmin: admin@pielmorena.com · contraseña nueva: $clave (se muestra solo ahora)\n";
