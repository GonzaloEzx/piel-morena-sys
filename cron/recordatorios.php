<?php
/**
 * Piel Morena — Cron: Recordatorios de citas
 *
 * Envia emails de recordatorio a clientes con citas para manana.
 * Ejecutar: php cron/recordatorios.php
 * Cron job sugerido: 0 20 * * * php /path/to/cron/recordatorios.php
 */

// Bootstrap minimo (sin sesion)
define('PIEL_MORENA', true);

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/mail_helper.php';

$db = getDB();
$enviados = 0;
$errores  = 0;

// Obtener citas de manana con estado pendiente o confirmada
$stmt = $db->prepare(
    "SELECT c.id AS id_cita, c.fecha, c.hora_inicio,
            s.nombre AS servicio,
            u.id AS id_cliente, u.email,
            CONCAT(u.nombre, ' ', u.apellidos) AS cliente_nombre
     FROM citas c
     JOIN servicios s ON c.id_servicio = s.id
     JOIN usuarios u  ON c.id_cliente = u.id
     WHERE c.fecha = CURDATE() + INTERVAL 1 DAY
       AND c.estado IN ('pendiente', 'confirmada')"
);
$stmt->execute();
$citas = $stmt->fetchAll();

foreach ($citas as $cita) {
    // Anti-duplicado: verificar que no se envio recordatorio hoy para este cliente
    $stmt = $db->prepare(
        "SELECT COUNT(*) FROM notificaciones
         WHERE id_usuario = ? AND tipo = 'recordatorio_cita'
           AND titulo LIKE 'Recordatorio%'
           AND DATE(fecha_envio) = CURDATE()"
    );
    $stmt->execute([$cita['id_cliente']]);
    $ya_enviado = (int) $stmt->fetchColumn() > 0;

    if ($ya_enviado) {
        continue;
    }

    $fecha_fmt = date('d/m/Y', strtotime($cita['fecha']));

    try {
        $ok = enviar_email($cita['email'], 'Recordatorio de cita — Piel Morena', 'recordatorio_cita', [
            'cliente_nombre' => $cita['cliente_nombre'],
            'servicio'       => $cita['servicio'],
            'fecha'          => $fecha_fmt,
            'hora'           => substr($cita['hora_inicio'], 0, 5),
        ]);

        registrar_notificacion(
            $cita['id_cliente'],
            'recordatorio_cita',
            'Recordatorio: cita manana',
            'Te recordamos que manana tenes cita de ' . $cita['servicio'] . ' a las ' . substr($cita['hora_inicio'], 0, 5) . '.'
        );

        if ($ok) {
            $enviados++;
        } else {
            $errores++;
        }
    } catch (Exception $e) {
        $errores++;
        error_log('Piel Morena Cron Recordatorios Error: ' . $e->getMessage());
    }
}

// Output para log del cron
$total = count($citas);
echo date('Y-m-d H:i:s') . " — Recordatorios: {$enviados} enviados, {$errores} errores, {$total} citas encontradas\n";
