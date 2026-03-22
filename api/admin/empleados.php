<?php
/**
 * Piel Morena — API Admin: CRUD Empleados
 */

require_once __DIR__ . '/../../includes/init.php';

if (!esta_autenticado() || !tiene_rol('admin')) {
    responder_json(false, null, 'No autorizado', 403);
}

$db     = getDB();
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    if (!empty($_GET['id'])) {
        $stmt = $db->prepare("SELECT id, nombre, apellidos, email, telefono, activo FROM usuarios WHERE id = ? AND rol = 'empleado'");
        $stmt->execute([$_GET['id']]);
        $emp = $stmt->fetch();
        if (!$emp) responder_json(false, null, 'Empleado no encontrado', 404);
        responder_json(true, $emp);
    }

    $stmt = $db->query("SELECT id, nombre, apellidos, email, telefono, activo, created_at FROM usuarios WHERE rol = 'empleado' ORDER BY id DESC");
    responder_json(true, $stmt->fetchAll());
}

$input = json_decode(file_get_contents('php://input'), true) ?: [];

if ($method === 'POST') {
    $nombre    = trim($input['nombre'] ?? '');
    $apellidos = trim($input['apellidos'] ?? '');
    $email     = trim($input['email'] ?? '');
    $telefono  = trim($input['telefono'] ?? '');
    $password  = $input['password'] ?? '';

    if (!$nombre || !$apellidos || !$email || !$password) {
        responder_json(false, null, 'Todos los campos obligatorios son requeridos', 400);
    }

    $stmt = $db->prepare("SELECT id FROM usuarios WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) responder_json(false, null, 'El email ya está registrado', 409);

    $hash = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $db->prepare("INSERT INTO usuarios (nombre, apellidos, email, password, telefono, rol) VALUES (?, ?, ?, ?, ?, 'empleado')");
    $stmt->execute([$nombre, $apellidos, $email, $hash, $telefono]);
    responder_json(true, ['id' => $db->lastInsertId()]);
}

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

    $stmt = $db->prepare("SELECT id FROM usuarios WHERE email = ? AND id != ?");
    $stmt->execute([$email, $id]);
    if ($stmt->fetch()) responder_json(false, null, 'El email ya está en uso', 409);

    if ($password) {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $db->prepare("UPDATE usuarios SET nombre=?, apellidos=?, email=?, telefono=?, password=? WHERE id=? AND rol='empleado'");
        $stmt->execute([$nombre, $apellidos, $email, $telefono, $hash, $id]);
    } else {
        $stmt = $db->prepare("UPDATE usuarios SET nombre=?, apellidos=?, email=?, telefono=? WHERE id=? AND rol='empleado'");
        $stmt->execute([$nombre, $apellidos, $email, $telefono, $id]);
    }
    responder_json(true);
}

if ($method === 'PATCH') {
    $id     = intval($input['id'] ?? 0);
    $activo = intval($input['activo'] ?? 1);
    if (!$id) responder_json(false, null, 'ID requerido', 400);

    $stmt = $db->prepare("UPDATE usuarios SET activo = ? WHERE id = ? AND rol = 'empleado'");
    $stmt->execute([$activo, $id]);
    responder_json(true);
}

responder_json(false, null, 'Método no permitido', 405);
