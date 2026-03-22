<?php
/**
 * Piel Morena - Helper de Email y Notificaciones
 * Funciones para enviar emails con templates HTML y registrar notificaciones en BD.
 */

/**
 * Enviar email con template HTML
 *
 * @param string $destinatario Email del destinatario
 * @param string $asunto       Asunto del email
 * @param string $template     Nombre del template (sin extension, en templates/email/)
 * @param array  $vars         Variables disponibles dentro del template
 * @return bool
 */
function enviar_email(string $destinatario, string $asunto, string $template, array $vars = []): bool {
    $template_path = ROOT_PATH . 'templates/email/' . $template . '.php';

    if (!file_exists($template_path)) {
        error_log("Piel Morena Mail: template no encontrado: $template_path");
        return false;
    }

    // Renderizar el template especifico
    extract($vars);
    ob_start();
    include $template_path;
    $contenido = ob_get_clean();

    // Renderizar el template base con el contenido
    ob_start();
    include ROOT_PATH . 'templates/email/base.php';
    $cuerpo = ob_get_clean();

    $headers = "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
    $headers .= "From: " . NOMBRE_NEGOCIO . " <" . EMAIL_NEGOCIO . ">\r\n";

    return @mail($destinatario, $asunto, $cuerpo, $headers);
}

/**
 * Registrar notificacion en BD
 *
 * @param int    $id_usuario ID del usuario
 * @param string $tipo       Tipo: recordatorio_cita, promocion, sistema, general
 * @param string $titulo     Titulo de la notificacion
 * @param string $mensaje    Mensaje/cuerpo
 * @return int ID de la notificacion creada
 */
function registrar_notificacion(int $id_usuario, string $tipo, string $titulo, string $mensaje): int {
    $db = getDB();
    $stmt = $db->prepare(
        "INSERT INTO notificaciones (id_usuario, tipo, titulo, mensaje, leida, fecha_envio)
         VALUES (?, ?, ?, ?, 0, NOW())"
    );
    $stmt->execute([$id_usuario, $tipo, $titulo, $mensaje]);
    return (int) $db->lastInsertId();
}

/**
 * Enviar email + registrar notificacion en un solo paso
 *
 * @param int    $id_usuario ID del usuario destinatario
 * @param string $tipo       Tipo de notificacion para la BD
 * @param string $asunto     Asunto del email
 * @param string $template   Nombre del template de email
 * @param array  $vars       Variables para el template
 * @return bool true si el email se envio
 */
function enviar_notificacion_email(int $id_usuario, string $tipo, string $asunto, string $template, array $vars = []): bool {
    $db = getDB();

    // Obtener email del usuario
    $stmt = $db->prepare("SELECT email FROM usuarios WHERE id = ? LIMIT 1");
    $stmt->execute([$id_usuario]);
    $email = $stmt->fetchColumn();

    if (!$email) {
        return false;
    }

    $enviado = enviar_email($email, $asunto, $template, $vars);

    // Registrar notificacion sin importar si el email se envio
    registrar_notificacion($id_usuario, $tipo, $asunto, $vars['mensaje_notificacion'] ?? $asunto);

    return $enviado;
}
