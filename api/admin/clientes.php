<?php
/**
 * Piel Morena — API Admin: CRUD Clientes
 * GET    → Listar / obtener uno (?id=X)
 * POST   → Crear
 * PUT    → Editar
 * PATCH  → Toggle activo
 */

require_once __DIR__ . '/../../includes/init.php';

if (!esta_autenticado() || !tiene_rol('admin')) {
    responder_json(false, null, 'No autorizado', 403);
}

$db     = getDB();
$method = $_SERVER['REQUEST_METHOD'];

// ── GET ──
if ($method === 'GET') {
    if (!empty($_GET['id'])) {
        $stmt = $db->prepare("SELECT id, nombre, apellidos, email, telefono, activo, google_id, email_verificado, ultimo_acceso FROM usuarios WHERE id = ? AND rol = 'cliente'");
        $stmt->execute([$_GET['id']]);
        $cli = $stmt->fetch();
        if (!$cli) responder_json(false, null, 'Cliente no encontrado', 404);
        responder_json(true, $cli);
    }

    $stmt = $db->query(
        "SELECT u.id, u.nombre, u.apellidos, u.email, u.telefono, u.activo, u.created_at,
                u.google_id, u.email_verificado, u.ultimo_acceso,
                COUNT(c.id) AS total_citas
         FROM usuarios u
         LEFT JOIN citas c ON u.id = c.id_cliente
         WHERE u.rol = 'cliente'
         GROUP BY u.id
         ORDER BY u.id DESC"
    );
    responder_json(true, $stmt->fetchAll());
}

$input = json_decode(file_get_contents('php://input'), true) ?: [];

// ── POST (crear) ──
if ($method === 'POST') {
    $nombre    = trim($input['nombre'] ?? '');
    $apellidos = trim($input['apellidos'] ?? '');
    $email     = trim($input['email'] ?? '');
    $telefono  = trim($input['telefono'] ?? '');
    $password  = $input['password'] ?? '';

    if (!$nombre || !$apellidos || !$email || !$password) {
        responder_json(false, null, 'Nombre, apellidos, email y contraseña son obligatorios', 400);
    }

    // Email único
    $stmt = $db->prepare("SELECT id FROM usuarios WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) responder_json(false, null, 'El email ya está registrado', 409);

    $hash = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $db->prepare(
        "INSERT INTO usuarios (nombre, apellidos, email, password, telefono, rol) VALUES (?, ?, ?, ?, ?, 'cliente')"
    );
    $stmt->execute([$nombre, $apellidos, $email, $hash, $telefono]);
    responder_json(true, ['id' => $db->lastInsertId()]);
}

// ── PUT (editar) ──
if ($method === 'PUT') {
    $id        = intval($input['id'] ?? 0);
    $nombre    = trim($input['nombre'] ?? '');
    $apellidos = trim($input['apellidos'] ?? '');
    $email     = trim($input['email'] ?? '');
    $telefono  = trim($input['telefono'] ?? '');
    $password  = $input['password'] ?? '';

    if (!$id || !$nombre || !$apellidos || !$email) {
        responder_json(false, null, 'Datos incompletos', 400);
    }

    // Email único (excluyendo este usuario)
    $stmt = $db->prepare("SELECT id FROM usuarios WHERE email = ? AND id != ?");
    $stmt->execute([$email, $id]);
    if ($stmt->fetch()) responder_json(false, null, 'El email ya está en uso', 409);

    if ($password) {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $db->prepare("UPDATE usuarios SET nombre=?, apellidos=?, email=?, telefono=?, password=? WHERE id=? AND rol='cliente'");
        $stmt->execute([$nombre, $apellidos, $email, $telefono, $hash, $id]);
    } else {
        $stmt = $db->prepare("UPDATE usuarios SET nombre=?, apellidos=?, email=?, telefono=? WHERE id=? AND rol='cliente'");
        $stmt->execute([$nombre, $apellidos, $email, $telefono, $id]);
    }
    responder_json(true);
}

// ── PATCH (toggle activo) ──
if ($method === 'PATCH') {
    $id     = intval($input['id'] ?? 0);
    $activo = intval($input['activo'] ?? 1);
    if (!$id) responder_json(false, null, 'ID requerido', 400);

    $stmt = $db->prepare("UPDATE usuarios SET activo = ? WHERE id = ? AND rol = 'cliente'");
    $stmt->execute([$activo, $id]);
    responder_json(true);
}

responder_json(false, null, 'Método no permitido', 405);
