<?php
/**
 * Piel Morena — API Admin: Estadísticas del Dashboard
 * GET → JSON { success, data: { citas_hoy, clientes_total, ingresos_mes,
 *              servicios_activos, citas_proximas, servicios_populares } }
 */

require_once __DIR__ . '/../../includes/init.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    responder_json(false, null, 'Método no permitido', 405);
}

// Solo admin
if (!esta_autenticado() || !tiene_rol('admin')) {
    responder_json(false, null, 'No autorizado', 403);
}

$db  = getDB();
$hoy = date('Y-m-d');
$mes = date('Y-m');

// ── Citas de hoy ──
$stmt = $db->prepare("SELECT COUNT(*) FROM citas WHERE fecha = ? AND estado NOT IN ('cancelada')");
$stmt->execute([$hoy]);
$citas_hoy = (int) $stmt->fetchColumn();

// ── Total clientes activos ──
$stmt = $db->query("SELECT COUNT(*) FROM usuarios WHERE rol = 'cliente' AND activo = 1");
$clientes_total = (int) $stmt->fetchColumn();

// ── Ingresos del mes (citas completadas) ──
$stmt = $db->prepare(
    "SELECT COALESCE(SUM(s.precio), 0)
     FROM citas c
     JOIN servicios s ON c.id_servicio = s.id
     WHERE c.estado = 'completada' AND c.fecha LIKE ?"
);
$stmt->execute([$mes . '%']);
$ingresos_mes = (float) $stmt->fetchColumn();

// ── Servicios activos ──
$stmt = $db->query("SELECT COUNT(*) FROM servicios WHERE activo = 1");
$servicios_activos = (int) $stmt->fetchColumn();

// ── Próximas 5 citas de hoy/futuro ──
$stmt = $db->prepare(
    "SELECT c.id, c.fecha, c.hora_inicio, c.estado,
            s.nombre AS servicio,
            CONCAT(u.nombre, ' ', u.apellidos) AS cliente
     FROM citas c
     JOIN servicios s ON c.id_servicio = s.id
     JOIN usuarios u  ON c.id_cliente = u.id
     WHERE c.fecha >= ? AND c.estado NOT IN ('cancelada','completada')
     ORDER BY c.fecha, c.hora_inicio
     LIMIT 5"
);
$stmt->execute([$hoy]);
$citas_proximas = $stmt->fetchAll();

// ── Top 5 servicios más consultados (precio) últimos 30 días ──
$stmt = $db->prepare(
    "SELECT s.nombre, COUNT(cp.id) AS consultas
     FROM consultas_precio cp
     JOIN servicios s ON cp.id_servicio = s.id
     WHERE cp.created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
     GROUP BY cp.id_servicio
     ORDER BY consultas DESC
     LIMIT 5"
);
$stmt->execute();
$servicios_populares = $stmt->fetchAll();

// ── Citas por estado (resumen) ──
$stmt = $db->prepare(
    "SELECT estado, COUNT(*) AS total
     FROM citas
     WHERE fecha >= DATE_SUB(?, INTERVAL 30 DAY)
     GROUP BY estado"
);
$stmt->execute([$hoy]);
$citas_por_estado = $stmt->fetchAll();

// ── Mensajes no leídos ──
$stmt = $db->query("SELECT COUNT(*) FROM contacto_mensajes WHERE leido = 0");
$mensajes_no_leidos = (int) $stmt->fetchColumn();

responder_json(true, [
    'citas_hoy'          => $citas_hoy,
    'clientes_total'     => $clientes_total,
    'ingresos_mes'       => $ingresos_mes,
    'servicios_activos'  => $servicios_activos,
    'citas_proximas'     => $citas_proximas,
    'servicios_populares'=> $servicios_populares,
    'citas_por_estado'   => $citas_por_estado,
    'mensajes_no_leidos' => $mensajes_no_leidos
]);
