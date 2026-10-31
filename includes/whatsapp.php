<?php
/**
 * Piel Morena - Contacto por WhatsApp
 *
 * Única fuente del número del salón y de los mensajes que arma la web.
 * La web no reserva: abre WhatsApp con el servicio escrito y el turno se
 * coordina con el salón.
 */

/**
 * Número del salón en formato wa.me (solo dígitos, con código de país).
 * Se puede fijar en config.local.php con "whatsapp_numero".
 */
function pm_wa_numero(): string
{
    return preg_replace('/\D/', '', WHATSAPP_NUMERO);
}

/**
 * Link a WhatsApp del salón con un mensaje ya escrito.
 */
function pm_wa_link(string $mensaje = ''): string
{
    $url = 'https://wa.me/' . pm_wa_numero();
    return $mensaje === '' ? $url : $url . '?text=' . rawurlencode($mensaje);
}

/**
 * Mensaje para pedir turno de un servicio, tratamiento o promoción.
 * Sin nombre, la consulta es general.
 */
function pm_wa_mensaje_turno(string $que = ''): string
{
    return $que === ''
        ? 'Hola, Piel Morena. Quisiera coordinar un turno. ¿Me ayudan a elegir el servicio?'
        : "Hola, Piel Morena. Vi {$que} en la web y quisiera coordinar un turno. ¿Qué días y horarios tienen disponibles?";
}

/**
 * Atajo: link para pedir turno de algo (o consulta general).
 */
function pm_wa_turno(string $que = ''): string
{
    return pm_wa_link(pm_wa_mensaje_turno($que));
}
