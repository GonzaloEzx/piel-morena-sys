<?php
/**
 * Piel Morena - API: Crear reserva
 * POST { id_servicio, fecha, hora_inicio, id_empleado?, nombre?, telefono?, email?, sitio? }
 *   → JSON { success, data: { id, token, link_gestion, servicio, fecha, hora, profesional } }
 *
 * Con sesión: la reserva queda a nombre de la usuaria logueada.
 * Sin sesión (invitada): nombre y WhatsApp obligatorios, email opcional.
 * La clienta se reconoce por teléfono. La cita queda `pendiente` hasta que
 * el salón la confirma por WhatsApp.
 *
 * `sitio` es un campo trampa: si viene con texto, es un bot.
 */

require_once __DIR__ . '/../../includes/init.php';
require_once __DIR__ . '/../../includes/agenda.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    responder_json(false, null, 'Método no permitido', 405);
}

$input = json_decode(file_get_contents('php://input'), true) ?: [];

$id_servicio = intval($input['id_servicio'] ?? 0);
$fecha       = trim($input['fecha'] ?? '');
$hora_inicio = trim($input['hora_inicio'] ?? '');
$id_empleado = intval($input['id_empleado'] ?? 0) ?: null;
$nombre      = trim($input['nombre'] ?? '');
$email       = trim($input['email'] ?? '');
$telefono    = trim($input['telefono'] ?? '');

if (trim($input['sitio'] ?? '') !== '') {
    responder_json(false, null, 'No se pudo registrar la reserva', 400);
}

// --- Validaciones básicas ---
if ($id_servicio <= 0) {
    responder_json(false, null, 'Selecciona un servicio', 400);
}
if (!$fecha || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha) || strtotime($fecha) < strtotime('today')) {
    responder_json(false, null, 'Fecha inválida', 400);
}
if (strtotime($fecha) > strtotime('+60 days')) {
    responder_json(false, null, 'Se puede reservar hasta 60 días adelante', 400);
}
if (!$hora_inicio || !preg_match('/^\d{2}:\d{2}$/', $hora_inicio)) {
    responder_json(false, null, 'Hora inválida', 400);
}

$db = getDB();

// --- Determinar clienta ---
if (esta_autenticado()) {
    $id_cliente = usuario_actual_id();
    $stmt = $db->prepare("SELECT nombre, email, telefono FROM usuarios WHERE id = ?");
    $stmt->execute([$id_cliente]);
    $u = $stmt->fetch() ?: [];
    $nombre = $u['nombre'] ?? '';
    $email = (string) ($u['email'] ?? '');
    $telefono = (string) ($u['telefono'] ?? '');
} else {
    if (mb_strlen($nombre) < 2) {
        responder_json(false, null, 'Contanos tu nombre', 400);
    }
    if (pm_tel_norm($telefono) === '') {
        responder_json(false, null, 'Ingresá tu WhatsApp con código de área (ej: 3624 123456)', 400);
    }
    if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        responder_json(false, null, 'El email no es válido', 400);
    }

    // Máximo 5 reservas por hora desde la misma IP
    $marca = sys_get_temp_dir() . '/pm_reservas_' . md5($_SERVER['REMOTE_ADDR'] ?? '') . '.json';
    $previas = is_file($marca) ? (json_decode((string) file_get_contents($marca), true) ?: []) : [];
    $previas = array_values(array_filter($previas, fn($t) => $t > time() - 3600));
    if (count($previas) >= 5) {
        responder_json(false, null, 'Hiciste muchas reservas seguidas. Escribinos por WhatsApp.', 429);
    }

    $id_cliente = pm_clienta_por_telefono($db, $nombre, $telefono, $email);
}

$r = pm_reservar($db, [
    'id_servicio' => $id_servicio,
    'fecha'       => $fecha,
    'hora_inicio' => $hora_inicio,
    'id_empleado' => $id_empleado,
    'id_cliente'  => $id_cliente,
    'estado'      => 'pendiente',
]);

if (!$r['ok']) {
    responder_json(false, null, $r['error'], $r['codigo']);
}

if (isset($marca)) {
    $previas[] = time();
    @file_put_contents($marca, json_encode($previas));
}

$stmt = $db->prepare("SELECT nombre FROM servicios WHERE id = ?");
$stmt->execute([$id_servicio]);
$servicio_nombre = (string) $stmt->fetchColumn();

$profesional = null;
if ($r['id_empleado']) {
    $stmt = $db->prepare("SELECT nombre FROM usuarios WHERE id = ?");
    $stmt->execute([$r['id_empleado']]);
    $profesional = $stmt->fetchColumn() ?: null;
}

$link_gestion = URL_BASE . '/mi-turno.php?t=' . $r['token'];

// Email y notificación (best-effort, no bloquean la reserva)
try {
    $fecha_fmt = date('d/m/Y', strtotime($fecha));
    if ($email !== '') {
        enviar_email($email, 'Recibimos tu reserva — Piel Morena', 'confirmacion_cita', [
            'cliente_nombre' => $nombre,
            'servicio'       => $servicio_nombre,
            'fecha'          => $fecha_fmt,
            'hora'           => $hora_inicio,
            'link_gestion'   => $link_gestion,
        ]);
    }
    registrar_notificacion($id_cliente, 'sistema', 'Reserva recibida', 'Tu turno de ' . $servicio_nombre . ' para el ' . $fecha_fmt . ' quedó pendiente de confirmación.');
} catch (Throwable $e) {
    error_log('Piel Morena Mail Error (crear cita): ' . $e->getMessage());
}

responder_json(true, [
    'id'           => $r['id'],
    'token'        => $r['token'],
    'link_gestion' => $link_gestion,
    'servicio'     => $servicio_nombre,
    'fecha'        => $fecha,
    'hora'         => $hora_inicio,
    'profesional'  => $profesional,
]);
