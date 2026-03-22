<?php
/**
 * Piel Morena - API: Crear cita/reserva
 * POST { id_servicio, fecha, hora_inicio, nombre?, email?, telefono? }
 * → JSON { success, data: { id, token } }
 *
 * Permite reservar como usuario logueado o como invitado.
 * Si invitado: crea registro temporal en usuarios con rol 'cliente'.
 */

require_once __DIR__ . '/../../includes/init.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    responder_json(false, null, 'Método no permitido', 405);
}

$input = json_decode(file_get_contents('php://input'), true);

$id_servicio = intval($input['id_servicio'] ?? 0);
$fecha       = trim($input['fecha'] ?? '');
$hora_inicio = trim($input['hora_inicio'] ?? '');

// Datos de invitado (opcionales si está logueado)
$nombre   = trim($input['nombre'] ?? '');
$email    = trim($input['email'] ?? '');
$telefono = trim($input['telefono'] ?? '');

// --- Validaciones básicas ---
if ($id_servicio <= 0) {
    responder_json(false, null, 'Selecciona un servicio', 400);
}

if (!$fecha || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha)) {
    responder_json(false, null, 'Fecha inválida', 400);
}

if (strtotime($fecha) < strtotime('today')) {
    responder_json(false, null, 'No se puede reservar en una fecha pasada', 400);
}

if (!$hora_inicio || !preg_match('/^\d{2}:\d{2}$/', $hora_inicio)) {
    responder_json(false, null, 'Hora inválida', 400);
}

$db = getDB();

// --- Obtener servicio ---
$stmt = $db->prepare("SELECT id, nombre, duracion_minutos FROM servicios WHERE id = ? AND activo = 1 LIMIT 1");
$stmt->execute([$id_servicio]);
$servicio = $stmt->fetch();

if (!$servicio) {
    responder_json(false, null, 'Servicio no encontrado', 404);
}

$hora_fin = date('H:i', strtotime($hora_inicio) + ($servicio['duracion_minutos'] * 60));

// --- Determinar cliente ---
$id_cliente = null;

if (esta_autenticado()) {
    $id_cliente = usuario_actual_id();
} else {
    // Invitado: validar datos
    if (!$nombre) {
        responder_json(false, null, 'El nombre es requerido', 400);
    }
    if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        responder_json(false, null, 'Ingresa un email válido', 400);
    }

    // Buscar si ya existe un usuario con ese email
    $stmt = $db->prepare("SELECT id FROM usuarios WHERE email = ? LIMIT 1");
    $stmt->execute([$email]);
    $existente = $stmt->fetch();

    if ($existente) {
        $id_cliente = $existente['id'];
    } else {
        // Crear usuario invitado
        $password_temp = password_hash(bin2hex(random_bytes(8)), PASSWORD_DEFAULT);
        $stmt = $db->prepare(
            "INSERT INTO usuarios (nombre, apellidos, email, password, telefono, rol) VALUES (?, '', ?, ?, ?, 'cliente')"
        );
        $stmt->execute([$nombre, $email, $password_temp, $telefono]);
        $id_cliente = $db->lastInsertId();
    }
}

// --- Verificar disponibilidad (anti-solapamiento) ---
$stmt = $db->prepare(
    "SELECT id FROM citas
     WHERE fecha = ? AND estado IN ('pendiente', 'confirmada', 'en_proceso')
     AND hora_inicio < ? AND hora_fin > ?
     LIMIT 1"
);
$stmt->execute([$fecha, $hora_fin, $hora_inicio]);

if ($stmt->fetch()) {
    responder_json(false, null, 'Este horario ya no está disponible. Selecciona otro.', 409);
}

// --- Crear la cita ---
$token = bin2hex(random_bytes(16)); // Token para cancelación sin login

$stmt = $db->prepare(
    "INSERT INTO citas (id_cliente, id_servicio, fecha, hora_inicio, hora_fin, estado, notas)
     VALUES (?, ?, ?, ?, ?, 'pendiente', ?)"
);
$stmt->execute([
    $id_cliente,
    $id_servicio,
    $fecha,
    $hora_inicio,
    $hora_fin,
    'token:' . $token,
]);

$id_cita = $db->lastInsertId();

// Enviar email de confirmacion (best-effort, no bloquea la reserva)
try {
    $email_cliente = esta_autenticado() ? ($_SESSION['usuario_email'] ?? '') : $email;
    $nombre_cliente = esta_autenticado() ? ($_SESSION['usuario_nombre'] ?? '') : $nombre;

    if ($email_cliente) {
        $fecha_fmt = date('d/m/Y', strtotime($fecha));
        enviar_email($email_cliente, 'Confirmacion de cita — Piel Morena', 'confirmacion_cita', [
            'cliente_nombre' => $nombre_cliente,
            'servicio'       => $servicio['nombre'],
            'fecha'          => $fecha_fmt,
            'hora'           => $hora_inicio,
        ]);

        if ($id_cliente) {
            registrar_notificacion($id_cliente, 'sistema', 'Cita reservada', 'Tu cita de ' . $servicio['nombre'] . ' para el ' . $fecha_fmt . ' fue reservada con exito.');
        }
    }
} catch (Exception $e) {
    error_log('Piel Morena Mail Error (crear cita): ' . $e->getMessage());
}

responder_json(true, [
    'id'       => $id_cita,
    'token'    => $token,
    'servicio' => $servicio['nombre'],
    'fecha'    => $fecha,
    'hora'     => $hora_inicio,
]);
