<?php
/**
 * Piel Morena — API Admin: Gestión de Citas
 * GET   → Listar (?fecha=&estado=) | Buscar clientes (?buscar_clientes=) | Listar servicios (?servicios_activos) | Empleados por servicio (?empleados_servicio=)
 * POST  → Crear cita manual desde admin
 * PATCH → Cambiar estado
 */

require_once __DIR__ . '/../../includes/init.php';

if (!esta_autenticado()) {
    responder_json(false, null, 'No autorizado', 403);
}
// Admin o empleado pueden acceder
if (!tiene_rol('admin') && !tiene_rol('empleado')) {
    responder_json(false, null, 'No autorizado', 403);
}

$db     = getDB();
$method = $_SERVER['REQUEST_METHOD'];

// ── GET ──
if ($method === 'GET') {
    // Buscar clientes para autocomplete
    if (isset($_GET['buscar_clientes'])) {
        $q = trim($_GET['buscar_clientes']);
        if (strlen($q) < 2) {
            responder_json(true, []);
        }
        $like = '%' . $q . '%';
        $stmt = $db->prepare(
            "SELECT id, nombre, apellidos, email, telefono
             FROM usuarios
             WHERE activo = 1 AND (
                 CONCAT(nombre, ' ', apellidos) LIKE ?
                 OR email LIKE ?
                 OR telefono LIKE ?
             )
             ORDER BY nombre ASC
             LIMIT 10"
        );
        $stmt->execute([$like, $like, $like]);
        responder_json(true, $stmt->fetchAll());
    }

    // Listar servicios activos (para el select del modal)
    if (isset($_GET['servicios_activos'])) {
        $stmt = $db->query("SELECT id, nombre, precio, duracion_minutos FROM servicios WHERE activo = 1 ORDER BY nombre ASC");
        responder_json(true, $stmt->fetchAll());
    }

    // Empleados que ofrecen un servicio específico
    if (!empty($_GET['empleados_servicio'])) {
        $id_servicio = intval($_GET['empleados_servicio']);
        $stmt = $db->prepare(
            "SELECT u.id, CONCAT(u.nombre, ' ', u.apellidos) AS nombre
             FROM empleados_servicios es
             JOIN usuarios u ON es.id_empleado = u.id
             WHERE es.id_servicio = ? AND u.activo = 1
             ORDER BY u.nombre"
        );
        $stmt->execute([$id_servicio]);
        $empleados = $stmt->fetchAll();
        // Si no hay asignaciones, devolver todos los empleados activos
        if (empty($empleados)) {
            $stmt = $db->query("SELECT id, CONCAT(nombre, ' ', apellidos) AS nombre FROM usuarios WHERE rol = 'empleado' AND activo = 1 ORDER BY nombre");
            $empleados = $stmt->fetchAll();
        }
        responder_json(true, $empleados);
    }

    $where  = [];
    $params = [];

    if (!empty($_GET['fecha'])) {
        $where[]  = 'c.fecha = ?';
        $params[] = $_GET['fecha'];
    }
    if (!empty($_GET['fecha_desde']) && !empty($_GET['fecha_hasta'])) {
        $where[]  = 'c.fecha >= ?';
        $params[] = $_GET['fecha_desde'];
        $where[]  = 'c.fecha <= ?';
        $params[] = $_GET['fecha_hasta'];
    }
    if (!empty($_GET['estado'])) {
        $where[]  = 'c.estado = ?';
        $params[] = $_GET['estado'];
    }

    $sql = "SELECT c.id, c.fecha, c.hora_inicio, c.hora_fin, c.estado, c.notas,
                   c.id_servicio, c.id_empleado,
                   s.nombre AS servicio, s.precio,
                   CONCAT(u.nombre, ' ', u.apellidos) AS cliente, u.telefono AS cliente_tel,
                   CONCAT(COALESCE(e.nombre,''), ' ', COALESCE(e.apellidos,'')) AS empleado
            FROM citas c
            JOIN servicios s ON c.id_servicio = s.id
            JOIN usuarios u  ON c.id_cliente = u.id
            LEFT JOIN usuarios e ON c.id_empleado = e.id";

    if ($where) {
        $sql .= ' WHERE ' . implode(' AND ', $where);
    }
    $sql .= ' ORDER BY c.fecha DESC, c.hora_inicio ASC';

    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    responder_json(true, $stmt->fetchAll());
}

// ── POST (crear cita manual desde admin/empleado) ──
if ($method === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true) ?: [];

    $id_servicio = intval($input['id_servicio'] ?? 0);
    $fecha       = trim($input['fecha'] ?? '');
    $hora_inicio = trim($input['hora_inicio'] ?? '');
    $id_empleado = !empty($input['id_empleado']) ? intval($input['id_empleado']) : null;
    $notas       = trim($input['notas'] ?? '');

    // Cliente existente o nuevo
    $id_cliente  = intval($input['id_cliente'] ?? 0);
    $nombre      = trim($input['nombre'] ?? '');
    $email       = trim($input['email'] ?? '');
    $telefono    = trim($input['telefono'] ?? '');

    // Validaciones
    if ($id_servicio <= 0) {
        responder_json(false, null, 'Selecciona un servicio', 400);
    }
    if (!$fecha || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha)) {
        responder_json(false, null, 'Fecha inválida', 400);
    }
    if (!$hora_inicio || !preg_match('/^\d{2}:\d{2}$/', $hora_inicio)) {
        responder_json(false, null, 'Hora inválida', 400);
    }

    // Obtener servicio
    $stmt = $db->prepare("SELECT id, nombre, duracion_minutos, precio FROM servicios WHERE id = ? AND activo = 1 LIMIT 1");
    $stmt->execute([$id_servicio]);
    $servicio = $stmt->fetch();
    if (!$servicio) {
        responder_json(false, null, 'Servicio no encontrado', 404);
    }

    $hora_fin = date('H:i', strtotime($hora_inicio) + ($servicio['duracion_minutos'] * 60));

    // Determinar cliente
    if ($id_cliente <= 0) {
        // Crear cliente nuevo
        if (!$nombre) {
            responder_json(false, null, 'El nombre del cliente es requerido', 400);
        }
        if ($email) {
            // Buscar si ya existe un usuario con ese email
            $stmt = $db->prepare("SELECT id FROM usuarios WHERE email = ? LIMIT 1");
            $stmt->execute([$email]);
            $existente = $stmt->fetch();
            if ($existente) {
                $id_cliente = $existente['id'];
            }
        }
        if ($id_cliente <= 0) {
            $password_temp = password_hash(bin2hex(random_bytes(8)), PASSWORD_DEFAULT);
            $partes = explode(' ', $nombre, 2);
            $nom = $partes[0];
            $ape = $partes[1] ?? '';
            $stmt = $db->prepare(
                "INSERT INTO usuarios (nombre, apellidos, email, password, telefono, rol) VALUES (?, ?, ?, ?, ?, 'cliente')"
            );
            $stmt->execute([$nom, $ape, $email ?: null, $password_temp, $telefono]);
            $id_cliente = $db->lastInsertId();
        }
    }

    // Verificar disponibilidad (anti-solapamiento)
    $stmt = $db->prepare(
        "SELECT id FROM citas
         WHERE fecha = ? AND estado IN ('pendiente', 'confirmada', 'en_proceso')
         AND hora_inicio < ? AND hora_fin > ?
         LIMIT 1"
    );
    $stmt->execute([$fecha, $hora_fin, $hora_inicio]);
    if ($stmt->fetch()) {
        responder_json(false, null, 'Este horario ya está ocupado. Selecciona otro.', 409);
    }

    // Crear la cita
    $stmt = $db->prepare(
        "INSERT INTO citas (id_cliente, id_servicio, id_empleado, fecha, hora_inicio, hora_fin, estado, notas)
         VALUES (?, ?, ?, ?, ?, ?, 'confirmada', ?)"
    );
    $stmt->execute([
        $id_cliente,
        $id_servicio,
        $id_empleado,
        $fecha,
        $hora_inicio,
        $hora_fin,
        $notas ?: null,
    ]);
    $id_cita = $db->lastInsertId();

    // Notificación al cliente (best-effort)
    try {
        $stmt = $db->prepare("SELECT email, CONCAT(nombre, ' ', apellidos) AS nombre FROM usuarios WHERE id = ?");
        $stmt->execute([$id_cliente]);
        $cliente_data = $stmt->fetch();
        if ($cliente_data && $cliente_data['email']) {
            $fecha_fmt = date('d/m/Y', strtotime($fecha));
            enviar_email($cliente_data['email'], 'Cita agendada — Piel Morena', 'confirmacion_cita', [
                'cliente_nombre' => $cliente_data['nombre'],
                'servicio'       => $servicio['nombre'],
                'fecha'          => $fecha_fmt,
                'hora'           => $hora_inicio,
            ]);
            registrar_notificacion($id_cliente, 'sistema', 'Cita agendada', 'Tu cita de ' . $servicio['nombre'] . ' para el ' . $fecha_fmt . ' fue agendada.');
        }
    } catch (Exception $e) {
        error_log('Piel Morena Mail Error (admin crear cita): ' . $e->getMessage());
    }

    responder_json(true, ['id' => $id_cita, 'servicio' => $servicio['nombre'], 'fecha' => $fecha, 'hora' => $hora_inicio]);
}

// ── PATCH (cambiar estado) ──
if ($method === 'PATCH') {
    $input  = json_decode(file_get_contents('php://input'), true) ?: [];
    $id     = intval($input['id'] ?? 0);
    $estado = $input['estado'] ?? '';

    $validos = ['pendiente', 'confirmada', 'en_proceso', 'completada', 'cancelada'];
    if (!$id || !in_array($estado, $validos)) {
        responder_json(false, null, 'ID y estado válido son requeridos', 400);
    }

    // Asignar empleado si se proporciona
    $id_empleado = !empty($input['id_empleado']) ? intval($input['id_empleado']) : null;

    if ($id_empleado) {
        $stmt = $db->prepare("UPDATE citas SET estado = ?, id_empleado = ? WHERE id = ?");
        $stmt->execute([$estado, $id_empleado, $id]);
    } else {
        $stmt = $db->prepare("UPDATE citas SET estado = ? WHERE id = ?");
        $stmt->execute([$estado, $id]);
    }

    // Obtener datos de la cita (servicio + cliente) — se usa para caja y notificaciones
    $stmt = $db->prepare(
        "SELECT c.fecha, c.hora_inicio, s.nombre AS servicio, s.precio,
                u.id AS id_cliente, u.email AS cliente_email,
                CONCAT(u.nombre, ' ', u.apellidos) AS cliente
         FROM citas c
         JOIN servicios s ON c.id_servicio = s.id
         JOIN usuarios u  ON c.id_cliente = u.id
         WHERE c.id = ?"
    );
    $stmt->execute([$id]);
    $cita = $stmt->fetch();

    $caja_registrada = false;

    // Auto-registro en caja al completar cita
    if ($estado === 'completada' && $cita && $cita['precio'] > 0) {
        $stmt = $db->prepare("SELECT COUNT(*) FROM caja_movimientos WHERE id_cita = ?");
        $stmt->execute([$id]);
        $ya_registrada = (int) $stmt->fetchColumn() > 0;

        if (!$ya_registrada) {
            $concepto = 'Servicio: ' . $cita['servicio'] . ' — Cliente: ' . $cita['cliente'];
            $stmt = $db->prepare(
                "INSERT INTO caja_movimientos (tipo, monto, concepto, metodo_pago, id_cita, id_usuario, fecha)
                 VALUES ('entrada', ?, ?, 'efectivo', ?, ?, ?)"
            );
            $stmt->execute([
                $cita['precio'],
                $concepto,
                $id,
                usuario_actual_id(),
                $cita['fecha']
            ]);
            $caja_registrada = true;
        }
    }

    // Enviar notificaciones por email segun el nuevo estado
    if ($cita) {
        try {
            $fecha_fmt = date('d/m/Y', strtotime($cita['fecha']));
            $vars_email = [
                'cliente_nombre' => $cita['cliente'],
                'servicio'       => $cita['servicio'],
                'fecha'          => $fecha_fmt,
                'hora'           => substr($cita['hora_inicio'], 0, 5),
            ];

            if ($estado === 'confirmada') {
                enviar_email($cita['cliente_email'], 'Tu cita fue confirmada — Piel Morena', 'cita_confirmada_admin', $vars_email);
                registrar_notificacion($cita['id_cliente'], 'sistema', 'Cita confirmada', 'Tu cita de ' . $cita['servicio'] . ' para el ' . $fecha_fmt . ' fue confirmada.');
            } elseif ($estado === 'cancelada') {
                $vars_email['cancelado_por'] = 'salon';
                enviar_email($cita['cliente_email'], 'Tu cita fue cancelada — Piel Morena', 'cancelacion_cita', $vars_email);
                registrar_notificacion($cita['id_cliente'], 'sistema', 'Cita cancelada', 'Tu cita de ' . $cita['servicio'] . ' para el ' . $fecha_fmt . ' fue cancelada por el salon.');
            }
        } catch (Exception $e) {
            error_log('Piel Morena Mail Error (admin citas): ' . $e->getMessage());
        }
    }

    responder_json(true, ['caja_registrada' => $caja_registrada]);
}

responder_json(false, null, 'Método no permitido', 405);
