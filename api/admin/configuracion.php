<?php
/**
 * Piel Morena — API Admin: Configuración
 * GET → Listar todas las configuraciones
 * PUT → Actualizar múltiples claves
 */

require_once __DIR__ . '/../../includes/init.php';

if (!esta_autenticado() || !tiene_rol('admin')) {
    responder_json(false, null, 'No autorizado', 403);
}

$db     = getDB();
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    $stmt = $db->query("SELECT clave, valor, descripcion FROM configuracion ORDER BY id");
    responder_json(true, $stmt->fetchAll());
}

if ($method === 'PUT') {
    $input = json_decode(file_get_contents('php://input'), true) ?: [];

    $stmt = $db->prepare("UPDATE configuracion SET valor = ? WHERE clave = ?");

    foreach ($input as $clave => $valor) {
        $stmt->execute([trim($valor), $clave]);
    }

    responder_json(true);
}

responder_json(false, null, 'Método no permitido', 405);
